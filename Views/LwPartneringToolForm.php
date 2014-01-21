<?php

class LwPartneringToolForm extends lw_object
{
    public function __construct($config)
    {
        $this->configuration = $config;
	    if ($this->configuration['lw_partneringtool']['bmbf'] == 1) {
	        $this->view = new lw_view(dirname(__FILE__).'/BmbfTemplates/Form.phtml');
	    }
	    else {
	        $this->view = new lw_view(dirname(__FILE__).'/Templates/Form.phtml');
	    }
        $this->view->isAdmin = false;
    }

    public function setSearchOrOffer($SearchOrOffer)
    {
        $this->view->SearchOrOffer = $SearchOrOffer;
    }

    public function setErrors($errors)
    {
        $this->view->errors = $errors;
    }
    
    public function setData($data)
    {
        $this->view->data = $data;
    }
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function setCountries($countries)
    {
        $this->view->countries = $countries;
    }
    
    public function setOrganisationTypes($orgatypes)
    {
        $this->orgatypes = $orgatypes;
    }
    
    public function setTopics($topics)
    {
        $this->topics = $topics;
    }

    public function setAdmin($bool)
    {
        $this->view->isAdmin = $bool;
    }

    public function setIntroduction($text)
    {
        $this->view->introduction = nl2br($text);
    }

    protected function getTopicOptions($selectedTopicsString)
	{
        $str = '<table style="width:480px">';
		foreach($this->topics as $key => $value) {
		    $selected = ($selectedTopicsString == $key) ? "checked='checked'" : "";
		    $str.='<tr width="400px"><td width="20px;"><input '.$selected.' type="radio" name="topic" value="'.$key.'" /></td>';
		    $str.='<td width="380px">'.$value.'</td></tr>'; 
		}
        $str.= "</table>";
		return $str;
	}
	
    protected function getOrganisationtypeOptions($selectedOrgatypeString)
	{
		foreach($this->orgatypes as $key => $value) {
		    $selected = ($selectedOrgatypeString == $key) ? "checked='checked'" : "";
		    $str.="<input $selected type='radio' name='orgatype' value='$key' />$value<br />"; 
		}
		return $str;
	}	
	
    public function setTranslationData($i18n)
    {
        $this->view->i18n = $i18n;
    }
 
    public function render()
    {
        $this->view->topicOptions = $this->getTopicOptions($this->view->data['topic']);
        $this->view->organistationtypeOptions = $this->getOrganisationtypeOptions($this->view->data['orgatype']);
        if ($this->view->isAdmin) {
            $this->view->backUrl = lw_page::getInstance()->getUrl();
            $this->view->actionUrl = lw_page::getInstance()->getUrl(array("lwse_pt_cmd"=>"edit", "id"=>$this->id));
            $this->view->deleteUrl = lw_page::getInstance()->getUrl(array("lwse_pt_cmd"=>"delete", "id"=>$this->id));
            $this->view->publishUrl = lw_page::getInstance()->getUrl(array("lwse_pt_cmd"=>"publish", "id"=>$this->id));
            $this->view->unpublishUrl = lw_page::getInstance()->getUrl(array("lwse_pt_cmd"=>"unpublish", "id"=>$this->id));
        }
        else {
            $this->view->actionUrl = lw_page::getInstance()->getUrl();
        }
        return $this->view->render(); 
    }
}
