<?php

class LwPartneringToolAdminList extends lw_object
{
    public function __construct($config)
    {
        $this->configuration = $config;
	    if ($this->configuration['lw_partneringtool']['bmbf'] == 1) {
	        $this->view = new lw_view(dirname(__FILE__).'/BmbfTemplates/AdminList.phtml');
	    }
	    else {
	        $this->view = new lw_view(dirname(__FILE__).'/Templates/AdminList.phtml');
	    }
    }
    
    public function setFilter($filter)
    {
        $this->view->filter = $filter;
    }
    
    public function setEntries($entries)
    {
        $this->view->entries = $entries;
    }
    
    public function setTopics($topics)
    {
        $this->view->topics = $topics;
    }
    
    public function setTranslationData($i18n)
    {
        $this->view->i18n = $i18n;
    }
 
    public function render()
    {
        $this->view->editUrl = lw_page::getInstance()->getUrl(array('lwse_pt_cmd'=>'edit'));
        return $this->view->render();
    }
}
