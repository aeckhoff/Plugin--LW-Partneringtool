<?php

class lw_partneringtool extends lw_plugin
{
	
	public function __construct()
	{	
		parent::__construct();
        require_once(dirname(__FILE__).'/Controller/LwPartneringToolController.php');
	}
    
    public function deleteEntry()
    {
        return die("delete has to be implemented!!!!!!");
    }
    
    function getOutput() 
    {
        require_once(dirname(__FILE__).'/Controller/LwPartneringToolBackend.php');
        $controller = new LwPartneringToolBackend();
        $controller->execute();
        return $controller->getOutput();
	}
    
    public function buildPageOutput()
	{
	    if ($this->params['oid']>0) {
			$this->setOid($this->params['oid']);
		}
		
		if ($this->params['mode'] == 'admin') {
            require_once(dirname(__FILE__).'/Controller/LwPartneringToolAdmin.php');
            $controller = new LwPartneringToolAdmin($this->getOid());
            $controller->setOfferAndSearchBoxIds($this->params['offer_id'], $this->params['search_id']);
            $controller->execute();
		} else {
            require_once(dirname(__FILE__).'/Controller/LwPartneringToolFrontend.php');
            $controller = new LwPartneringToolFrontend($this->getOid());
            $controller->execute();
		}
        return $controller->getOutput();
    }
}
