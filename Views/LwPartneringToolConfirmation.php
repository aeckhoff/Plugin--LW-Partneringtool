<?php

class LwPartneringToolConfirmation extends lw_object
{
    public function __construct($config)
    {
        $this->configuration = $config;
	    if ($this->configuration['lw_partneringtool']['bmbf'] == 1) {
	        $this->view = new lw_view(dirname(__FILE__).'/BmbfTemplates/Confirmation.phtml');
	    }
	    else {
	        $this->view = new lw_view(dirname(__FILE__).'/Templates/Confirmation.phtml');
	    }
	    $this->setData();
    }

    public function setTranslationData($i18n)
    {
        $this->view->i18n = $i18n;
    }
 
    public function setData()
    {
        $data = $_SESSION['lwse_partnering_temp_data'];
        foreach($data as $key=>$value) {
            $data[$key] = htmlentities($data[$key],ENT_QUOTES,'UTF-8');
        }
        $this->view->data = $data;
    }
    
    public function setErrors($errors)
    {
        $this->view->errors = $errors;
    }
    
    public function setCountry($country)
    {
        $this->view->country = $country;
    }
    
    public function setOrganisationTypes($orgatypes)
    {
        $this->orgatypes = $orgatypes;
    }
    
    public function setTopics($topics)
    {
        $this->topics = $topics;
    }
    
    public function render()
    {
        $this->view->topic = $this->topics[$this->view->data['topic']];
        $this->view->orgatype = $this->orgatypes[$this->view->data['orgatype']];
        $this->view->actionUrl = lw_page::getInstance()->getUrl(array("lwse_pt_cmd"=>"save"));
        $this->view->backUrl   = lw_page::getInstance()->getUrl();
        return $this->view->render();
    }
}
