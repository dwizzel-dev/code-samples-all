<?php
/**
@auth:	Amjad
@date:	17-03-2016
@info:	Mail Class
@  

*/


// Pear Mail Library
require_once 'Mail.php';
require_once 'Mail/mime.php';

class Email {
	var $from       = "";
	var $to         = "";
	var $email_subject = "";
	var $email_text = "";
	var $email_html = "";
	var $smtp       = null;
	var $headers    = array();
	var $body       = "";

	private function connectSMTP() {
		$this->smtp = Mail::factory('smtp', array(
			'host' => 'ssl://smtp.mandrillapp.com',
			'port' => '587',
			'auth' => true,
			'username' => '',
			'password' => ''
		));
	}

	private function sendMail() {
		$this->connectSMTP();
		$mail = $this->smtp->send( $this->to, $this->headers, $this->body);
		if (PEAR::isError($mail)) {
			echo('<p>' . $mail->getMessage() . '</p>');
		} else {
			echo('<p>Message successfully sent!</p>');
		}
	}

	private function prepareMail() {
		$message = new Mail_mime();
		$message->setTXTBody($this->email_text);
		$message->setHTMLBody($this->email_html);
		$this->body = $message->get();
		$extraheaders = array(
			'From'    => $this->from,
			'Subject' => $this->email_subject
		);
		var_dump($extraheaders); die();
		$this->headers = $message->headers($extraheaders);
	}

	public function email() {
		echo "=>" . $this->email_subject;
		die();
		$html_template    = file_get_contents('/var/www/email_template/email.html');
		$this->email_html = str_replace("%%email_content%%", $this->email_html, $html_template);
		$this->prepareMail();
		//$this->sendMail();
	}
}
?>