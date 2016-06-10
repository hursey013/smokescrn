<?php
require_once 'common.php';
use \Defuse\Crypto\Crypto;
use \Defuse\Crypto\Exception as Ex;

// Initial validation state
$errors = false;

// Validation: check if it's a post request
if($_SERVER['REQUEST_METHOD'] != "POST") {
	$errors = true;
	response(VALIDATION_POST, $errors);
}

// Validation: check if it's an ajax request
if((!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
   AND (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
  ){
	$errors = true;
	response(VALIDATION_AJAX, $errors);
} 

// Validation: check if any of the fields aren't set
if((!isset($_POST['message']))
   || (!isset($_POST['encrypt_password']))
   || (!isset($_POST['encrypt_password_confirm']))
   || (!isset($_POST['email_recipient']))
   || (!isset($_POST['email_password_hint']))
   || (!isset($_POST['email_sender']))
   || (!isset($_POST['expiration_date']))
  ){
	$errors = true;
	response(VALIDATION_REQUIRED_FIELDS, $errors);
} else {
	$message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
	$password = $_POST['encrypt_password'];
	$password_confirm = $_POST['encrypt_password_confirm'];
	$email_recipient = filter_var($_POST['email_recipient'], FILTER_SANITIZE_EMAIL);
	$email_password_hint = filter_var($_POST['email_password_hint'], FILTER_SANITIZE_STRING);
	$email_sender = filter_var($_POST['email_sender'], FILTER_SANITIZE_EMAIL);
	$expiration_date = filter_var($_POST['expiration_date'], FILTER_SANITIZE_STRING);
}		  

// Validation: check if any of the required fields are blank
if((empty($message))
   || (empty($password))
   || (empty($password_confirm))
   || (empty($expiration_date))
  ){
	$errors = true;
	response(VALIDATION_REQUIRED_FIELDS, $errors);
}					   

// Validation: check if textarea is too long
if(strlen($message) > 1000) {
	$errors = true;
	response(VALIDATION_TEXTAREA_LENGTH, $errors);
}

// Validation: check if passwords is long enough
if(strlen($password) < 8) {
	$errors = true;
	response(VALIDATION_PASSWORD_LENGTH, $errors);
}

// Validation: check if passwords match
if($password !== $password_confirm) {
	$errors = true;
	response(VALIDATION_PASSWORD_MISMATCH, $errors);
}		

// Validation: check for valid email format
if((!empty($email_recipient)) && (!filter_var($email_recipient, FILTER_VALIDATE_EMAIL))
   || (!empty($email_sender)) && (!filter_var($email_sender, FILTER_VALIDATE_EMAIL))
  ){
	$errors = true;
	response(VALIDATION_EMAIL_INVALID, $errors);
}

// Validation: check if password hint is too long
if(strlen($email_password_hint) > 200) {
	$errors = true;
	response(VALIDATION_PASSWORD_HINT_LENGTH, $errors);
}

// Validation: check for valid expiration date format
$date = DateTime::createFromFormat('m/d/Y', $expiration_date);
$date_errors = DateTime::getLastErrors();
if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {
	$errors = true;
	response(VALIDATION_DATE_INVALID, $errors);
}
if (strtotime($expiration_date) > strtotime("today +30 days")){
	$errors = true;
	response(VALIDATION_DATE_INVALID, $errors);
}

// If all of the above validation checks pass, continue on
if (!$errors) {

	// Create an array of data to be encrypted
	$data = serialize(array(
		"message" => $message,
		"email_sender" => $email_sender
	));

	// Encrypt data, reference: https://github.com/defuse/php-encryption/
	try {
		$data_encrypted = Crypto::encryptWithPassword($data, $password);
	} catch (Ex\EnvironmentIsBrokenException $ex) {
		response(ENCRYPTION_UNSAFE, true);
	}
	
	// Store the encrypted data
	$array = array(
		'secret' => bin2hex($data_encrypted),
		'expiration_date' => strtotime($expiration_date . ' +1 day')
	);

	$item = $collection->item();
	
	if ($item->post($array)) {
		$item->event('log')->post(['action' => 'created']);
		$id = $item->getKey();
	} else {
		$logger->error($item->getStatus());
		response($item->getStatus(), true);
	}
	
	// Send email to recipient
	if(!empty($email_recipient)){

		// Email body
		$email_content = '<p>' . EMAIL_BODY_SENT . '</p>';
		$email_content .= '<p>Access it at: <a href="' . SITE_URL . '/' . $id . '" target="_blank">' . SITE_URL . '/' . $id . '</a></p>';
		if(!empty($email_password_hint)){
			$email_content .= '<p><strong>Password hint: </strong>' . $email_password_hint;
		}
		$email_content .= '<p>---</p><p>Thank you,<br />' . SITE_NAME . '</p>';

		$sendemail = new SendGrid\Email();
		$sendemail->addTo($email_recipient)
			->setFrom(EMAIL_FROM_ADDRESS)
			->setSubject(EMAIL_SUBJECT_SENT)
			->setHtml($email_content);

		// Check for email errors and provide a response
		try {
			$sendgrid->send($sendemail);
			response($id, false);
		} catch(\SendGrid\Exception $e) {
			foreach($e->getErrors() as $er) {
				$logger->error($er);
				response($er, true);
			}
		}

	} else {
		// Provide response
		response($id, false);
	}

} else {
	// Unknown error
	$logger->error(LOG_UNKNOWN_ERROR);
	die();
}