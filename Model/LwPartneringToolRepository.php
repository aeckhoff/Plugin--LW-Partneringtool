<?php

class LwPartneringToolRepository
{
	private $db;
	private $configuration;

	public function __construct($db,$configuration,$instanceId)
	{
		$this->db = $db;
		
		//$this->importGermanCountryNames();
		
		$this->configuration = $configuration;
		$this->table = $this->configuration['dbt']['epss_partnering'];
	    	
	    $this->instanceId = $instanceId;
	    
	    $this->validKeys = explode(',','contactname,organisation,email,telephone,keywords,description,topic,orgatype,country,orgadesc,created_at,published,request_type');
	    $this->updateKeys = explode(',','contactname,organisation,email,telephone,keywords,description,topic,orgatype,country,orgadesc');
	    
	    /*
	    $sql = "CREATE TABLE ".$this->table." (id number(11),contactname varchar2(255),organisation varchar2(255),email varchar2(255),telephone varchar2(255), country varchar2(3), description varchar2(3000),topic_id number(11), orgatype_id number(11), created_at number(14),published number(14), request_type varchar2(255))";
		$ok = $this->db->dbquery($sql);
		echo $sql.":$ok<br>";
		
		$seqstart = 1;
		
        $sql = "CREATE SEQUENCE ".$this->table."_seq START WITH ".$seqstart." INCREMENT BY 1 MAXVALUE 1E27 MINVALUE 1 NOCACHE NOCYCLE ORDER";
        $ok = $this->db->dbquery($sql);
		echo $sql.":$ok<br>";
        
        $sql = "CREATE TRIGGER ".$this->table."_ib before insert on ".$this->table." for each row begin if :new.id is null then select ".$this->table."_seq.nextval into :new.id from dual; end if; end;";
        $ok = $this->db->dbquery($sql);
		echo $sql.":$ok<br>";
	    die();
	    */
	}
	
	public function getLanguageOfPageById($id)
	{
	    $sql = "SELECT language FROM ".$this->db->gt('lw_page_langlink')." WHERE page_id = ".intval($id)." AND page_link < 1";
	    $result = $this->db->select1($sql);
	    return $result['language'];
	}
	
    public function loadPluginDataForOid($oid)
    {
        $sql = "SELECT * FROM " . $this->configuration['dbt']['plugins'] . " WHERE container_id ='" . intval($oid) . "'";
        $erg = $this->db->select1($sql);
        
        if ($erg['parameter']) {
            $data['parameter'] = unserialize(stripslashes($erg['parameter']));
        }
        else {
            $data['parameter'] = array();
        }
        $data['content'] = stripslashes($erg['content']);
        
        return $data;
    } 	
	
	private function importGermanCountryNames()
	{
        die();
        if (($handle = fopen(dirname(__FILE__)."/laenderliste.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $dummy['name'] = $data[0];
                    $dummy['kuerzel2'] = $data[1];
                    $dummy['kuerzel3'] = $data[2];
                    $name_de[$data[4]] = $data[0];
                    $out[] = $dummy;
            }
            fclose($handle);
        }
        
        $countries = $this->getCountries();
        foreach($countries as $country)
        {
            $nameDe = trim($name_de[$country['kuerzel2']]);
            if (strlen($nameDe)<1) {
                $nameDe = $country['name_en'];
            }
            $sql = "UPDATE lw_countries SET name_de = '".$this->db->quote($nameDe)."' WHERE id = ".$country['id'];
            $ok = $this->db->dbquery($sql);
            echo $ok.": ".$sql."<br>"; 
        }
        
        
        exit();
	}
	
	public function update($id,$data)
	{
        $id = intval($id);	    
	    if ($id < 1) return false;
	    
	    foreach($this->updateKeys as $validKey) {
	        $update.= "$validKey='".$this->db->quote($data[$validKey])."',";
	    }
	    
        $update = substr($update,0,-1);	    
	    
	    $sql = "UPDATE ".$this->table." SET $update WHERE id = '$id'";
	    $ok = $this->db->dbquery($sql);
	    
	    return $ok;
	}
	
	public function create($searchOrOffer,$data)
	{
	    if ($searchOrOffer == 'search') {
	        $data['request_type'] = 'search';
	    } else if ($searchOrOffer == 'offer') {
	        $data['request_type'] = 'offer';
	    } else {
	        return false;
	    }
	    
	    $data['published'] = 0;
	    $data['created_at'] = date('YmdHis');
	    
	    $keys='';
	    $values='';
	    foreach($this->validKeys as $validKey) {
	        $keys.= $validKey.',';
	        $values.= "'".$this->db->quote($data[$validKey])."',";
	    }
	    
	    $keys = substr($keys,0,-1);
	    $values = substr($values,0,-1);
	    
	    $sql = "INSERT INTO ".$this->table." ($keys) VALUES ($values)";
	    $id = $this->db->dbinsert($sql,$this->table);
	    
	    if ($id > 0) return true;
	    return false;
	}
	
	public function publish($id)
	{
	    $id = intval($id);
	    if ($id < 1) return false;
	    
	    $now = date('YmdHis');
	    
	    $sql = "UPDATE ".$this->table." SET published = '$now' WHERE id = '$id'";
        $ok = $this->db->dbquery($sql);
        return $ok;
	}
	
	public function unpublish($id)
	{
	    $id = intval($id);
	    if ($id < 1) return false;
	    
	    $sql = "UPDATE ".$this->table." SET published = '0' WHERE id = '$id'";
        $ok = $this->db->dbquery($sql);
        return $ok;
	}
	
	public function delete($id)
	{
	    $id = intval($id);
	    if ($id < 1) return false;
	    
	    $sql = "DELETE FROM ".$this->table." WHERE id = '$id'";
	    $ok = $this->db->dbquery($sql);
	    
	    return $ok;
	}	
	
	public function getEntries($filter = 'all')
	{
	    if ($filter == 'all') {
	        $filterString = '';
	    } else if ($filter == 'published') {
	        $filterString = "AND (published IS NOT NULL AND published > 1)";
	    } else {
	        $filterString = "AND (published IS NULL OR published = '0')";
	    }
	
	    $sql = "SELECT * FROM ".$this->table." WHERE 1=1 $filterString ORDER BY created_at DESC";
	    $results = $this->db->select($sql);
	    return $results;
	}
	
	public function getPublishedEntries($filter = 'all', $country = 'all', $topic = 'all', $orgatype = 'all', $freetext = '')
    {
        
	    if ($filter == 'all') {
	        $filterString = '';
	    } else if ($filter == 'search') {
	        $filterString = "AND request_type = 'search'";
	    } else {
	        $filterString = "AND request_type = 'offer'";
	    }
	    if ($country != 'all') {
	        $filterString.= " AND country = '".$country."'";
	    }
	    if ($topic != 'all') {
	        $filterString.= " AND topic = ".intval($topic)."";
	    }
	    if ($orgatype != 'all') {
	        $filterString.= " AND orgatype = ".intval($orgatype)."";
	    }
	    if (strlen(trim($freetext))> 0) {
	        $filterString.= " AND (UPPER(description) like '%".strtoupper($this->db->quote($freetext))."%' OR UPPER(keywords) like '%".strtoupper($this->db->quote($freetext))."%') ";
	    }
	    $sql = "SELECT * FROM ".$this->table." WHERE (published IS NOT NULL AND published > 0) $filterString ORDER BY created_at DESC";
	    $results = $this->db->select($sql);
	    return $results;
	}
	
	public function getEntry($id)
	{
	    $id = intval($id);
	    if ($id < 1) die();
	    
	    $sql = "SELECT * FROM ".$this->table." WHERE id = '$id'";
	    $result = $this->db->select1($sql);
	    return $result;
	}
	
	public function getTopics()
	{
	    $sql = "SELECT id, name FROM ".$this->db->gt('lw_master')." WHERE lw_object = 'nks_partnersuchthema' ORDER BY id ASC";
	    $result = $this->db->select($sql);
	    foreach($result as $entry) {
	        $out[$entry['id']] = $entry['name'];
	    }
	    return $out;
	}

	public function getOrganisationtypes()
	{
	    $sql = "SELECT id, name FROM ".$this->db->gt('lw_master')." WHERE lw_object = 'nks_organisationstyp' ORDER BY id";
	    $result = $this->db->select($sql);
	    foreach($result as $entry) {
	        $out[$entry['id']] = $entry['name'];
	    }
	    return $out;
	}
	
	public function getCountries($lang="de")
	{
	    if ($lang == "de") {
	        $sql = "SELECT * FROM ".$this->db->gt('lw_countries')." ORDER BY name_de";
	    }
	    else {
	        $sql = "SELECT id, kuerzel2, kuerzel3, name_en as name_de FROM ".$this->db->gt('lw_countries')." ORDER BY name_en";
	    }
	    return $this->db->select($sql);
	}
	
	public function getCountryByShortcut($shortcut, $lang = "de")
	{
	    $sql = "SELECT name_de, name_en FROM ".$this->db->gt('lw_countries')." WHERE kuerzel3 = '".$this->db->quote($shortcut)."'";
	    $result = $this->db->select1($sql);
        if ($lang == "de") {
    	    return $result['name_de'];
        }
        else {
    	    return $result['name_en'];
        }
	}
	
}
