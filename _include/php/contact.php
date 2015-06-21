<?php
/*
* Contact Form Class
*/


header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');

$admin_email = 'info@7entertainment.in'; // Your Email
$message_min_length = 10; // Min Message Length


class Contact_Form{
	function __construct($details, $email_admin, $message_min_length){
		
		$this->name = stripslashes($details['name']);
		$this->email = trim($details['email']);
		$this->subject = 'Contact from Your Website'; // Subject 
		$this->message = stripslashes($details['message']);

		$this->phone = stripslashes($details['phone']);
		$this->eventdate = stripslashes($details['eventdate']);
		$this->eventvenue = stripslashes($details['eventvenue']);
		$this->eventbudget = stripslashes($details['eventbudget']);
		$this->additionalinfo = "Event Date: ". $this->eventdate. "\r\n". "Event Venue: ". $this->eventvenue. "\r\n". 
								"Event Budget: ". $this->eventbudget. "\r\n\r\n". "Phone Number: ". $this->phone. "\r\n". "Name: ". $this->name. "\r\n\r\n";
			
		$this->finalmessage =  $this->additionalinfo . "Message: ". $this->message;

		$this->email_admin = $email_admin;
		$this->message_min_length = $message_min_length;
		
		$this->response_status = 1;
		$this->response_html = '';
	}


	private function validateEmail(){
		$regex = '/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i';
	
		if($this->email == '') { 
			return false;
		} else {
			$string = preg_replace($regex, '', $this->email);
		}
	
		return empty($string) ? true : false;
	}

	private function validateFields(){
		// Check name
		if(!$this->name)
		{
			$this->response_html .= '<p>Please enter your name</p>';
			$this->response_status = 0;
		}

		// Check email
		if(!$this->email)
		{
			$this->response_html .= '<p>Please enter an e-mail address</p>';
			$this->response_status = 0;
		}
		
		// Check valid email
		if($this->email && !$this->validateEmail())
		{
			$this->response_html .= '<p>Please enter a valid e-mail address</p>';
			$this->response_status = 0;
		}

		// Check phone
		if(!$this->phone)
		{
			$this->response_html .= '<p>Please enter your phone number</p>';
			$this->response_status = 0;
		}

		// Check event date
		if(!$this->eventdate)
		{
			$this->response_html .= '<p>Please enter the event date</p>';
			$this->response_status = 0;
		}

		// Check event venue
		if(!$this->eventvenue)
		{
			$this->response_html .= '<p>Please enter the event venue</p>';
			$this->response_status = 0;
		}

		// Check event budget
		if(!$this->eventbudget)
		{
			$this->response_html .= '<p>Please enter the event budget</p>';
			$this->response_status = 0;
		}
		
		// Check message length
		if(!$this->message || strlen($this->message) < $this->message_min_length)
		{
			$this->response_html .= '<p>Please enter your message. It should have at least '.$this->message_min_length.' characters</p>';
			$this->response_status = 0;
		}
	}

	private function sendEmail(){
		$mail = mail($this->email_admin, $this->subject, $this->finalmessage,
			 "From: ".$this->name." <".$this->email.">\r\n"
			."Reply-To: ".$this->email."\r\n"
		."X-Mailer: PHP/" . phpversion());
	
		if($mail)
		{
			$this->response_status = 1;
			$this->response_html = '<p>Thank You!</p>';
		}
	}


	function sendRequest(){
		$this->validateFields();
		if($this->response_status)
		{
			$this->sendEmail();
		}

		$response = array();
		$response['status'] = $this->response_status;	
		$response['html'] = $this->response_html;
		
		echo json_encode($response);
	}
}


$contact_form = new Contact_Form($_POST, $admin_email, $message_min_length);
$contact_form->sendRequest();

?>