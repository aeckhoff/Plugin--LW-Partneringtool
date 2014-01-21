<?php

class LwPartneringToolList extends lw_object
{
    public function __construct($config)
    {
        $this->configuration = $config;
	    if ($this->configuration['lw_partneringtool']['bmbf'] == 1) {
	        $this->view = new lw_view(dirname(__FILE__).'/BmbfTemplates/List.phtml');
	    }
	    else {
	        $this->view = new lw_view(dirname(__FILE__).'/Templates/List.phtml');
	    }
    }
    
    public function setTranslationData($i18n)
    {
        $this->view->i18n = $i18n;
    }
 
    public function setFilter($filter, $country, $topic, $orgatype, $freetext)
    {
        $this->view->filter = $filter;
        $this->view->country = $country;
        $this->view->topic = $topic;
        $this->view->orgatype = $orgatype;
        $this->view->freetext = $freetext;
    }
    
    public function setEntries($entries)
    {
        $this->view->entries = $entries;
    }
    
    public function setCountries($countries)
    {
        $this->view->countries = $countries;
    }
    
    public function setOrganisationTypes($orgatypes)
    {
        $this->view->orgatypes = $orgatypes;
    }
    
    public function setTopics($topics)
    {
        $this->view->topics = $topics;
    }
    
    public function setIntroduction($text)
    {
        $this->view->introduction = nl2br($text);
    }

    public function render()
    {
        $this->view->viewUrl = lw_page::getInstance()->getUrl(array('lwse_pt_cmd'=>'view'));
        $this->view->actionUrl = lw_page::getInstance()->getUrl();
        return $this->view->render();
    }
}
