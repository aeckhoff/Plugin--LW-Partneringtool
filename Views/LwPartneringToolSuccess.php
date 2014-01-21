<?php

class LwPartneringToolSuccess extends lw_object
{
    public function __construct($config)
    {
        $this->configuration = $config;
	    if ($this->configuration['lw_partneringtool']['bmbf'] == 1) {
	        $this->view = new lw_view(dirname(__FILE__).'/BmbfTemplates/Success.phtml');
	    }
	    else {
	        $this->view = new lw_view(dirname(__FILE__).'/Templates/Success.phtml');
	    }
    }

    public function setTranslationData($i18n)
    {
        $this->view->i18n = $i18n;
    }
 
    public function render()
    {
        $this->view->index = $this->configuration['general']['index'];
        return $this->view->render();
    }
}
