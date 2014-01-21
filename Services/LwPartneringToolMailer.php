<?php

class LwPartneringToolMailer extends lw_object
{

    public function __construct()
    {
    }

    public function setPluginData($pluginData)
    {
        $this->pluginData = $pluginData;
    }

    protected function prepareMail()
    {
        $this->data = $_SESSION['lwse_partnering_temp_data'];
        $this->userMailSender = $this->pluginData['parameter']['mailsender'];
        $this->adminMailSubject = $this->pluginData['parameter']['adminmailsubject'];
        $this->adminMailRecipients = $this->pluginData['parameter']['adminmailrecipients'];
        $this->adminMailTemplate = $this->pluginData['parameter']['adminmailtemplate'];

        if (strlen(trim($this->adminMailSubject)) == 0) {
            $this->adminMailSubject = "Partnering Tool Admin Notification";
        }

        if (strlen(trim($this->adminMailTemplate)) == 0) {
            $this->adminMailTemplate = "Dear Admin,\n\na new search/offer was issued by %%- contactname -%%\n\nPlease publish the request if valid.";
        }
        
        $adminEmails = array();
        $adminMailRecipients = explode(';',$this->adminMailRecipients);
        foreach($adminMailRecipients as $amr) {
            if (filter_var(trim($amr), FILTER_VALIDATE_EMAIL)) {
                $this->adminEmails[] = trim($amr);
            }
        }
        
        if (empty($this->adminEmails)) {
            $this->EmailsAvailable = false;
        }
        else {
            $this->EmailsAvailable = true;
        }
        
        foreach($this->data as $key => $value) {
            $this->adminMailTemplate = str_replace('%%- '.$key.' -%%', $value, $this->adminMailTemplate);
        }
        $this->adminMailTemplate = str_replace('%%- url -%%', $value, $this->adminMailTemplate);
    }

    protected function setMailHeader($sender)
    {
        $this->header = 'From: '.$sender."\r\n" .
		        'Reply-To: '.$sender."\r\n" .
		        'Content-Type: text/plain;charset=iso-8859-1'. "\r\n" .
		        'Content-Transfer-Encoding: 8bit'. "\r\n" .
		        'X-Mailer: PHP-Mail/' . phpversion();
    }

    public function sendEmail()
    {
        $this->prepareMail();
        if ($this->EmailsAvailable) {
            $mailText = utf8_decode($this->adminMailTemplate);
            $this->setMailHeader($this->userMailSender);
            foreach($this->adminEmails as $email) {
                mail($email, $this->adminMailSubject, $mailText, $this->header);
            }
        }
    }
    
    public function sendPublishMail($data) 
    {
        $userMailSubject = $this->pluginData['parameter']['mailsubject'];
        $userMailSender = $this->pluginData['parameter']['mailsender'];
        $userMailTemplate = $this->pluginData['parameter']['mailtemplate'];
         
        if (strlen(trim($userMailSubject)) == 0) {
            $userMailSubject = "Partnering Tool Notification";
        }
        
        if (strlen(trim($userMailTemplate)) == 0) {
            $userMailTemplate = "Dear %%- contactname -%%,\n\nyour search/offer was published.\n\nDescription:\n%%- description -%%\n\n--\nThe partnering tool";
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return;
        }
        
        foreach($data as $key => $value) {
            $userMailTemplate = str_replace('%%- '.$key.' -%%', $value, $userMailTemplate);
        }
        
        $mailText = str_replace('%%- id -%%', $data['id'], $userMailTemplate);
        $mailText = utf8_decode($mailText);
        
        $this->setMailHeader($userMailSender);
        mail($data['email'], $userMailSubject, $mailText, $this->header);
    }
}
