<?php

class LwPartneringToolEntry extends lw_object
{
    public function __construct($config)
    {
        $this->configuration = $config;
	    if ($this->configuration['lw_partneringtool']['bmbf'] == 1) {
	        $this->view = new lw_view(dirname(__FILE__).'/BmbfTemplates/Entry.phtml');
	    }
	    else {
	        $this->view = new lw_view(dirname(__FILE__).'/Templates/Entry.phtml');
	    }
    }
    
    public function setTranslationData($i18n)
    {
        $this->view->i18n = $i18n;
    }
 
    public function setEntry($data)
    {
        $this->data = $data;
    }
    
    public function setCountry($country)
    {
        $this->view->country = $country;
    }
    
    public function setOrganisationType($orgatype)
    {
        $this->view->orgatype = $orgatype;
    }
    
    public function setTopic($topic)
    {
        $this->view->topic = $topic;
    }
    
    public function getHumanReadableTopic($number)
    {
        $topics = $this->view->topics;
        return $topics[$number];
    }    
    
    public function render()
    {
        foreach($this->data as $key=>$value) {
            //$data[$key] = htmlentities($this->data[$key], ENT_QUOTES, 'UTF-8');
            $data[$key] = htmlentities($this->data[$key], ENT_QUOTES);
        }
        $this->view->data = $data;
        return $this->view->render();
    }
}
