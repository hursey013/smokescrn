<?php
require_once 'common.php';
use \Defuse\Crypto\Crypto;
use \Defuse\Crypto\Exception as Ex;

// Initial validation state
$errors = false;

// Validation: check if it's a post request
if($_SERVER['REQUEST_METHOD'] != "POST") {
	$errors = true;
	response(VALIDATION_POST, $errors, $logger);
}

// Validation: check if it's an ajax request
if((!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
   AND (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
  ){
	$errors = true;
	response(VALIDATION_AJAX, $errors, $logger);
} 

// Validation: check if any of the fields aren't set
if((!isset($_POST['id']))
   || (!isset($_POST['decrypt_password']))
  ){
	$errors = true;
	response(VALIDATION_REQUIRED_FIELDS, $errors, $logger);
} else {
	$id = $_POST['id'];
	$password = $_POST['decrypt_password'];
}		   

// Validation: check if any of the fields are blank
if((empty($id))
   || (empty($password))
  ){
	$errors = true;
	response(VALIDATION_REQUIRED_FIELDS, $errors, $logger);
}

// Validation: check if message ID is too long
if(strlen($password) > 16) {
	$errors = true;
	response(VALIDATION_MESSAGE_LENGTH, $errors, $logger);
}	

// Validation: check if message exists
$item = $collection->item($id);
if ($item->get()) {
	$salt = Crypto::hexToBin($item->salt);
	$data_encrypted = Crypto::hexToBin($item->secret);
} else {
	$errors = true;
	response(VALIDATION_MESSAGE_NOTFOUND, $errors, $logger);
}

// If all of the above validation checks pass, continue on
if (!$errors) {

	// Create decryption key
	$length = 16;
	$iterations = PASSWORD_ITERATIONS;
	$key = hash_pbkdf2("sha256", $password, $salt, $iterations, $length);	

	// Decrypt data, reference: https://github.com/defuse/php-encryption/
	try {
		$data_decrypted = Crypto::Decrypt($data_encrypted, $key);
	} catch (Ex\InvalidCiphertextException $ex) { // VERY IMPORTANT
		// Log event
		$item->event('log')->post(['action' => 'failed']);
		response(DECRYPTION_PASSWORD_WRONG, true, $logger);
	} catch (Ex\CryptoTestFailedException $ex) {
		response(ENCRYPTION_UNSAFE, true, $logger);
	} catch (Ex\CannotPerformOperationException $ex) {
		response(DECRYPTION_UNSAFE, true, $logger);
	}			

	// Delete message
	$item->delete();	
	
	// Log event
	if ($item->delete()) {
		$item->event('log')->post(['action' => 'deleted']);
	} else {
		response($item->getStatus(), true, $logger);
	}	
	
	$data = unserialize($data_decrypted);
	
	// Send email to sender
	if(!empty($data["email_sender"])){

		$email_content = '<p>' . EMAIL_BODY_VIEWED . '</p>';
		$email_content .= '<p>---</p><p>Thank you,<br />' . SITE_NAME . '</p>';

		$sendemail->addTo($data["email_sender"])
			->setFrom(EMAIL_FROM_ADDRESS)
			->setSubject(EMAIL_SUBJECT_VIEWED)
			->setHtml($email_content);

		$sendgrid->send($sendemail);

	}	
		
	// Provide response
	response($data["message"], false);

} else {
	// Unknown error
	$logger->alert(LOG_UNKNOWN_ERROR);
	die();
}