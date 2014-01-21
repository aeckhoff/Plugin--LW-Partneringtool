<?php

class LwPartneringToolController extends lw_object
{
    public function __construct($oid)
    {
        $this->oid = $oid;
	    $this->configuration = lw_registry::getInstance()->getEntry('config');
	    $this->db = lw_registry::getInstance()->getEntry('db');
	    $this->request = lw_registry::getInstance()->getEntry('request');

        $this->pluginData = lw_registry::getInstance()->getEntry("repository")->plugins()->loadPluginData('lwPartneringTool', $this->oid);
        if ($this->pluginData['parameter']['mode'] == 'search') {
            $this->searchOrOffer = 'search';
        } 
        else {
            $this->searchOrOffer = 'offer';
        }

        $this->validKeys = explode(',','contactname,organisation,email,telephone,keywords,description,topic,orgatype,country,orgadesc');

        require_once(dirname(__FILE__).'/../Model/LwPartneringToolRepository.php');
        $this->repository = new LwPartneringToolRepository($this->db, $this->configuration, false);

        $this->setLanguage();
    }
    
    public function setLanguage()
    {
        $pageId = lw_page::getInstance()->getPageValue("id");
        $lang = $this->repository->getLanguageOfPageById($pageId);
        if ($lang == "DE") {
            $this->language = "de";
        }
        else {
            $this->language = "en";
        }
    }

    public function getOutput()
    {
        return $this->output;
    }

    protected function getTranslationData()
    {
        if (($handle = fopen(dirname(__FILE__)."/../I18N/".$this->language.".csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                    $i18n[$data[0]] = utf8_decode($data[1]);
            }
            fclose($handle);
        }
        return $i18n;
    }
    
    protected function validate()
    {
        $i18n = $this->getTranslationData();
        foreach($this->validKeys as $key) {
            $this->data[$key] = trim($this->request->getRaw($key));
            if ($key != 'telephone') {
                if (strlen($this->data[$key]) < 1) {
                    $this->errors[$key] = $i18n['error_mandatory'];
                }
            }
            if ($key == 'description' || $key == 'orgadesc') {
                if (strlen($this->data[$key]) > 3000) {
                    $this->errors[$key] = $i18n['error_inputsize_2500'];
                }
            } else {
                if (strlen($this->data[$key]) > 255) {
                    $this->errors[$key] = $i18n['error_inputsize_255'];
                }
            }
        }
        
        if (!filter_var($this->data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = $i18n['error_email'];
        }
    }
}
