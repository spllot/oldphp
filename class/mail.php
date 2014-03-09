<?php
class Mail{
    var $recipients = "";
    var $subject = "No Subject";
    var $context = "No Context";
    var $headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n";
    var $sender = "";
    function addRecipient($new_recipient){
        $this->recipients .= $new_recipient . ",";
    }//addRecipient

	function setFrom($new_email){
        $this->sender = "From: " . $new_email ."\r\n";
	}

    function setSender($new_name, $new_address){
        $this->sender = "From: " . $new_name . "<" . $new_address . ">\r\n";
    }//setSender

    function setRecipient($new_recipient){
        $this->recipients = $new_recipient;
    }//setRecipient

    function setSubject($new_subject){
        $this->subject = $new_subject;
    }//setSubject

    function setContext($new_context){
        $this->context = $new_context;
    }//setContext

    function Send(){
//        $this->headers .= "Bcc: mp3order@gmail.com\r\n";
        mail($this->recipients, $this->subject, $this->context, $this->headers . $this->sender);
    }//Send
}//mail
?>