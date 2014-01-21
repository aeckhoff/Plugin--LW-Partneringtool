<?php

class LwPartneringToolConfigurationView extends lw_object
{
    public function __construct($config)
    {
        $this->configuration = $config;
	    $this->view = new lw_view(dirname(__FILE__).'/Templates/ConfigurationForm.phtml');
    }
    
    public function setTranslationData($i18n)
    {
        $this->view->i18n = $i18n;
    }
 
    public function setData($data)
    {
	    $this->view->mode = htmlentities($data['parameter']['mode']);
	    $this->view->description = htmlentities($data['parameter']['description']);
	    $this->view->mailsubject = htmlentities($data['parameter']['mailsubject']);
	    $this->view->mailsender = htmlentities($data['parameter']['mailsender']);
	    $this->view->mailtemplate = htmlentities($data['parameter']['mailtemplate']);
	    $this->view->adminmailsubject = htmlentities($data['parameter']['adminmailsubject']);
	    $this->view->adminmailrecipients = htmlentities($data['parameter']['adminmailrecipients']);
	    $this->view->adminmailtemplate = htmlentities($data['parameter']['adminmailtemplate']);
    }
    
    public function setErrors($errors)
    {
        $this->view->errors = $errors;
    }
    
    public function render()
    {
	    $this->view->mediaUrl = $this->configuration['url']['media'];
	    $this->view->actionUrl = $this->buildUrl();
	    $this->view->cancelUrl = $this->buildUrl(array(),array('cmd','oid'));
        return $this->view->render();
    }
}
