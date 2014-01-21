<?php

class LwPartneringToolBackend extends lw_object
{
    public function __construct()
    {
		$reg = lw_registry::getInstance();
	    $this->configuration = $reg->getEntry('config');
	    $this->db = $reg->getEntry('db');
	    $this->request = $reg->getEntry('request');
	    $this->response = $reg->getEntry('response');
	    $this->auth = $reg->getEntry('auth');
        $this->pluginRepository = lw_registry::getInstance()->getEntry("repository")->plugins();
    }
 
    public function execute()
    {
        if ($this->request->getRaw('save') == 1) {
            require_once(dirname(__FILE__).'/../Model/LwPartneringToolBackendSave.php');
	        $ModelCommand = new LwPartneringToolBackendSave($this->pluginRepository, $this->request);
	        $ok = $ModelCommand->execute();
            if ($ok) {
                $this->pageReload($this->buildUrl(array(),array('cmd','oid')));
                exit();
            }
	        $this->errors = $ModelCommand->getErrors();
	    } 
	    else {
	        $data = $this->pluginRepository->loadPluginData('lwPartneringTool', $this->request->getInt('oid'));
	    }
        require_once(dirname(__FILE__).'/../Views/LwPartneringToolConfigurationView.php');
        $view = new LwPartneringToolConfigurationView($this->configuration);
        $view->setData($data);
        $view->setErrors($this->errors);
		$this->output = $view->render();
    }

    public function getOutput()
    {
        return $this->output;
    }
}
