<?php

class LwPartneringToolAdmin extends LwPartneringToolController
{
    public function __construct($oid)
    {
        parent::__construct($oid);
        if (!lw_registry::getInstance()->getEntry('auth')->isLoggedIn()) {
            die("NO AUTHORIZATION");
        }
    }
 
    public function setOfferAndSearchBoxIds($offerId, $searchId)
    {
        $this->offerBoxId = $offerId;
        $this->searchBoxId = $searchId;
    }
 
    public function execute()
    {
        switch ($this->request->getRaw('lwse_pt_cmd')) {
            case 'delete':
                $this->delete();
                break;

            case 'publish':
                $this->publish();
                break;

            case 'unpublish':
                $this->unpublish();
                break;

            case 'edit':
                if ($this->request->getInt('save') == 1) {
                    $this->saveData();
                }
                $this->buildEdit();
                break;

            default:
            case 'list':
                $this->buildList();
                break;
        }
    }
    
    protected function delete()
    {
        $id = $this->request->getInt('deleteid');
        if ($id > 0) {
            $this->repository->delete($id);
        }
        $this->pageReload(lw_page::getInstance()->getUrl());
    }
    
    protected function publish()
    {
        $id = $this->request->getInt('publishid');
        if ($id > 0) {
            $this->repository->publish($id);
            $this->sendPublishMail($id);
        }
        $this->pageReload(lw_page::getInstance()->getUrl());
    }
    
    protected function sendPublishMail($id)
    {
        $data = $this->repository->getEntry($id);
        $requestType = $data['request_type'];
        
        if ($requestType == 'search') {
            $pluginData = $this->repository->loadPluginDataForOid($this->searchBoxId);
        } else {
            $pluginData = $this->repository->loadPluginDataForOid($this->offerBoxId);
        }
          
        require_once(dirname(__FILE__).'/../Services/LwPartneringToolMailer.php');
        $mailer = new LwPartneringToolMailer();
        $mailer->setPluginData($pluginData);
        $mailer->sendPublishMail($data);
    }
    
    protected function unpublish()
    {
        $id = $this->request->getInt('publishid');
        if ($id > 0) {
            $this->repository->unpublish($id);
        }
        $this->pageReload(lw_page::getInstance()->getUrl());
    }
    
    protected function saveData()
    {
        $this->validate();
        
        if (empty($this->errors)) {
            $this->repository->update($this->request->getInt('id'),$this->data);
            $this->pageReload(lw_page::getInstance()->getUrl());
        }
        
    }    
    
    protected function buildList()
    {
        if ($this->request->getRaw('changefilter') == '1') {
            $f = $this->request->getRaw('filter');
            if (in_array($f,array('all','unpublished','published'))) {
                $_SESSION['lwse_partnering_list_adminfilter'] = $f; 
            }
        }
    
        if (!isset($_SESSION['lwse_partnering_list_adminfilter'])||empty($_SESSION['lwse_partnering_list_adminfilter'])) {
            $filter = 'unpublished';
        } 
        else {
            $filter = $_SESSION['lwse_partnering_list_adminfilter'];
        }

        require_once(dirname(__FILE__).'/../Views/LwPartneringToolAdminList.php');
        $view = new LwPartneringToolAdminList($this->configuration);
        $view->setTranslationData($this->getTranslationData());
        $view->setFilter($filter);
        $view->setEntries($this->repository->getEntries($filter));
        $this->output = $view->render();
    }
    
    protected function buildEdit()
    {
        $_SESSION['lwse_partnering_data_okay'] = false;

        require_once(dirname(__FILE__).'/../Views/LwPartneringToolForm.php');
        $view = new LwPartneringToolForm($this->configuration);
        $view->setTranslationData($this->getTranslationData());
        if (empty($this->errors)) {
            $view->setData($this->repository->getEntry($this->request->getInt('id')));
        }
        else {
            $view->setData($this->data);
        }

        $view->setId($this->request->getInt('id'));
        $view->setErrors($this->errors);
        $view->setTopics($this->repository->getTopics());
        $view->setOrganisationTypes($this->repository->getOrganisationtypes());
        $view->setCountries($this->repository->getCountries($this->language));
        $view->setAdmin(true);
        $this->output = $view->render();     
    }
}
