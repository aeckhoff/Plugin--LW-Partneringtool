<?php

class LwPartneringToolFrontend extends LwPartneringToolController
{
    public function __construct($oid)
    {
        parent::__construct($oid);
    }

    public function execute()
    {
        if ($this->pluginData['parameter']['mode'] == 'list') {
            if ($this->request->getRaw('lwse_pt_cmd') == "view") {
                $this->buildView($this->request->getInt('id'));
            }
            else {
                $this->buildList();
            }
            return;
        }
        switch ($this->request->getRaw('lwse_pt_cmd')) {
            case 'confirm':
                if (!isset($_SESSION['lwse_partnering_data_okay']) || $_SESSION['lwse_partnering_data_okay']==false) {
                    $this->buildForm();
                    return;
                }
                $this->confirm();
                break;
            
            case 'save':
                if (!isset($_SESSION['lwse_partnering_data_okay'])||$_SESSION['lwse_partnering_data_okay']==false) {
                    $this->buildForm();
                    return;
                }
                $this->save();
                break;
            
            default:
            case 'create':
                if ($this->request->getInt('save') == 1) {
                    $this->validateData();
                } 
                $this->buildForm();
                break;
        }    
    }

    protected function prepareFilter()
    {
        if ($this->request->getRaw('changefilter') == '1') {
            $f = $this->request->getRaw('filter');
            if (in_array($f,array('all','search','offer'))) {
                $_SESSION['lwse_partnering_list_filter'] = $f; 
            }
            $c = $this->request->getRaw('country');
            $_SESSION['lwse_partnering_list_country'] = $c;
            $t = $this->request->getRaw('topic');
            $_SESSION['lwse_partnering_list_topic'] = $t;
            $o = $this->request->getRaw('orgatype');
            $_SESSION['lwse_partnering_list_orgatyp'] = $o;
            $f = $this->request->getRaw('freetext');
            $_SESSION['lwse_partnering_list_freetext'] = $f;
        }
    
        if (!isset($_SESSION['lwse_partnering_list_filter'])||empty($_SESSION['lwse_partnering_list_filter'])) {
            $this->filter = 'all';
        } else {
            $this->filter = $_SESSION['lwse_partnering_list_filter'];
        }
        if (!isset($_SESSION['lwse_partnering_list_country'])||empty($_SESSION['lwse_partnering_list_country'])) {
            $this->country = 'all';
        } else {
            $this->country = $_SESSION['lwse_partnering_list_country'];
        }
        if (!isset($_SESSION['lwse_partnering_list_topic'])||empty($_SESSION['lwse_partnering_list_topic'])) {
            $this->topic = 'all';
        } else {
            $this->topic = $_SESSION['lwse_partnering_list_topic'];
        }
        if (!isset($_SESSION['lwse_partnering_list_orgatyp'])||empty($_SESSION['lwse_partnering_list_orgatyp'])) {
            $this->orgatype = 'all';
        } else {
            $this->orgatype = $_SESSION['lwse_partnering_list_orgatyp'];
        }
        if (!isset($_SESSION['lwse_partnering_list_freetext'])||empty($_SESSION['lwse_partnering_list_freetext'])) {
            $this->freetext = '';
        } else {
            $this->freetext = $_SESSION['lwse_partnering_list_freetext'];
        }    
    }

    protected function buildList()
    {
        $this->prepareFilter();
        
        require_once(dirname(__FILE__).'/../Views/LwPartneringToolList.php');
        $view = new LwPartneringToolList($this->configuration);
        $view->setTranslationData($this->getTranslationData());
        $view->setFilter($this->filter, $this->country, $this->topic, $this->orgatype, $this->freetext);

        $entries = $this->repository->getPublishedEntries($this->filter, $this->country, $this->topic, $this->orgatype, $this->freetext);
        $view->setEntries($entries);

        $view->setCountries($this->repository->getCountries($this->language));
        $view->setOrganisationTypes($this->repository->getOrganisationtypes());
        $view->setTopics($this->repository->getTopics());
        $view->setIntroduction($this->pluginData['parameter']['description']);
        $this->output = $view->render();
    }
    
    protected function buildView($id)
    {
        $id = intval($id);
        $data = $this->repository->getEntry($id);
        
        if ($data['published'] < 1) {
            $this->pageReload(lw_page::getInstance()->getUrl());
        }
        
        require_once(dirname(__FILE__).'/../Views/LwPartneringToolEntry.php');
        $view = new LwPartneringToolEntry($this->configuration);
        $view->setTranslationData($this->getTranslationData());
        $view->setEntry($data);
        $view->setCountry($this->repository->getCountryByShortcut($data['country'], $this->language));

        $topics = $this->repository->getTopics();
        $view->setTopic($topics[$data['topic']]);
        
        $orgatypes = $this->repository->getOrganisationtypes();
        $view->setOrganisationType($orgatypes[$data['orgatype']]);
        $this->output = $view->render();
    }
    
    protected function buildForm()
    {
        $_SESSION['lwse_partnering_data_okay'] = false;

        require_once(dirname(__FILE__).'/../Views/LwPartneringToolForm.php');
        $view = new LwPartneringToolForm($this->configuration);
        $view->setTranslationData($this->getTranslationData());
                
        if (isset($_SESSION['lwse_partnering_temp_data']) && !empty($_SESSION['lwse_partnering_temp_data'])) {
            $view->setData($_SESSION['lwse_partnering_temp_data']);
        } 
        else {
            $view->setData(array());
        }
        
        $view->setErrors($this->errors);
        $view->setTopics($this->repository->getTopics());
        $view->setOrganisationTypes($this->repository->getOrganisationtypes());
        $view->setCountries($this->repository->getCountries($this->language));
        $view->setIntroduction($this->pluginData['parameter']['description']);
        $view->setSearchOrOffer($this->searchOrOffer);
        $this->output = $view->render();        
    }
    
    protected function validateData()
    {
        $this->validate();
        
        $_SESSION['lwse_partnering_temp_data'] = $this->data;
        if (empty($this->errors)) {
            $_SESSION['lwse_partnering_data_okay'] = true;
            $this->pageReload(lw_page::getInstance()->getUrl(array("lwse_pt_cmd"=>"confirm")));
        }
    }
    
    protected function confirm()
    {
        require_once(dirname(__FILE__).'/../Views/LwPartneringToolConfirmation.php');
        $view = new LwPartneringToolConfirmation($this->configuration);
        $view->setTranslationData($this->getTranslationData());
        $view->setTopics($this->repository->getTopics());
        $view->setOrganisationTypes($this->repository->getOrganisationtypes());
        $view->setCountry($this->repository->getCountryByShortcut($_SESSION['lwse_partnering_temp_data']['country']));
        $view->setErrors($this->errors);
        $this->output = $view->render();        
    }    

    protected function save()
    {
        $confirmed = $this->request->getRaw('isconfirmed');
        $i18n = $this->getTranslationData();
        
        if ($confirmed != 'CONFIRMED') {
            $this->errors['isconfirmed'] = $i18n['error_confirm'];
            $this->confirm();
            return;
        }
        
        $ok = $this->repository->create($this->searchOrOffer,$_SESSION['lwse_partnering_temp_data']);
        if (!$ok) {
            $this->errors['unknown'] = $i18n['error_unknown'];
            $this->confirm();
            return;
        }

        require_once(dirname(__FILE__).'/../Services/LwPartneringToolMailer.php');
        $mailer = new LwPartneringToolMailer($this->configuration);
        $mailer->setPluginData($this->pluginData);
        $mailer->sendEmail();

        unset($_SESSION['lwse_partnering_data_okay']);
        unset($_SESSION['lwse_partnering_temp_data']);

        require_once(dirname(__FILE__).'/../Views/LwPartneringToolSuccess.php');
        $view = new LwPartneringToolSuccess($this->configuration);
        $view->setTranslationData($this->getTranslationData());
        $this->output = $view->render();        
    }
}
