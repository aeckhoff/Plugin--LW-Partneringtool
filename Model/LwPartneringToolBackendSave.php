<?php

class LwPartneringToolBackendSave extends lw_object
{
    public function __construct($repository, $request)
    {
        $this->pluginRepository = $repository;
        $this->request = $request;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
    
    protected function prepareData()
    {
        $this->mode = $this->request->getRaw('mode');
	    $this->description = $this->request->getRaw('description');
	    
	    $this->mailsubject = $this->request->getRaw('mailsubject');
	    $this->mailsender = $this->request->getRaw('mailsender');
	    $this->mailtemplate = $this->request->getRaw('mailtemplate');
	    
	    $this->adminmailsubject  = $this->request->getRaw('adminmailsubject');
	    $this->adminmailrecipients   = $this->request->getRaw('adminmailrecipients');
	    $this->adminmailtemplate = $this->request->getRaw('adminmailtemplate');

        $this->validate();
    }
    
    protected function validate()
    {
	    if (!in_array($this->mode, array('search','offer','list','admin'))) {
	        $this->errors['mode'] = "Not a valid mode";
	    }
	    
	    if ($this->mode != 'list') {
	        if (strlen(trim($this->description)) > 2000) {
	            $this->errors['description'] = "2000 characters maximum";
	        }
	        if (strlen(trim($this->mailsubject)) > 2000) {
	            $this->errors['mailsubject'] = "2000 characters maximum";
	        }
	        if (strlen(trim($this->mailsender)) > 255) {
	            $this->errors['mailsender'] = "255 characters maximum";
	        }
	        if (strlen(trim($this->mailtemplate)) == 0) {
	            $this->errors['mailtemplate'] = "Please fill in this field";
	        }
	        if (strlen(trim($this->mailsubject)) == 0) {
	            $this->errors['mailsubject'] = "Please fill in this field";
	        }
	        if (strlen(trim($this->mailsender)) == 0) {
	            $this->errors['mailsender'] = "Please fill in this field";
	        }
	        if (strlen(trim($this->adminmailtemplate)) == 0) {
	            $this->errors['adminmailtemplate'] = "Please fill in this field";
	        }
	        if (strlen(trim($this->adminmailsubject)) == 0)  {
	            $this->errors['adminmailsubject'] = "Please fill in this field";
	        }
	        if (strlen(trim($this->adminmailrecipients)) == 0) {
	            $this->errors['adminmailrecipients'] = "Please fill in this field";
	        }
	        if (!filter_var($this->mailsender, FILTER_VALIDATE_EMAIL)) {
	            $this->errors['mailsender'] = "Not a valid e-mail address";
	        }
	        if (!filter_var($this->adminmailrecipient, FILTER_VALIDATE_EMAIL)) {
	            $emailAdresses = explode($this->adminmailrecipient,';');
	            foreach($emailAdresses as $emailAdress) {
	                if (!filter_var($emailAdress, FILTER_VALIDATE_EMAIL)) {
	                    $this->errors['adminmailrecipient'] = "Not a valid e-mail address";
	                }
	            }
	        }
	    }	    
    
    }
    
    public function execute()
    {
        $this->prepareData();
	    
	    if (!empty($this->errors)) {
	        return;
	    }
	    
	    $parameters = array(
	        'mode' => $this->mode,
	        'description' => $this->description,
	        'mailsubject' => $this->mailsubject,
	        'mailsender' => $this->mailsender,
	        'mailtemplate' => $this->mailtemplate,
	        'adminmailsubject'  => $this->adminmailsubject,
	        'adminmailrecipients'   => $this->adminmailrecipients,
	        'adminmailtemplate' => $this->adminmailtemplate
	    );
	    $this->pluginRepository->savePluginData('lwPartneringTool', $this->request->getInt('oid'), $parameters, false);
        return true;	    
    }
}
