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
if((!isset($_POST['id']))
   || (!isset($_POST['decrypt_password']))
  ){
	$errors = true;
	response(VALIDATION_REQUIRED_FIELDS, $errors);
} else {
	$id = $_POST['id'];
	$password = $_POST['decrypt_password'];
}		   

// Validation: check if any of the fields are blank
if((empty($id))
   || (empty($password))
  ){
	$errors = true;
	response(VALIDATION_REQUIRED_FIELDS, $errors);
}

// Validation: check if message ID is too long
if(strlen($id) > 16) {
	$errors = true;
	response(VALIDATION_MESSAGE_LENGTH, $errors);
}	

// Validation: check if message exists
$item = $collection->item($id);
if (!$item->get()) {
	$errors = true;
	response(VALIDATION_MESSAGE_NOTFOUND, $errors);
}

// Validation: check if the message is disabled
$now = strtotime("now") * 1000;
$past = strtotime("-10 min") * 1000;
$events = $item->events('log');
$events->search('value.action:disabled AND @path.timestamp:[' . $past .' TO ' . $now .']');
$disabled = $events->getTotalCount();
if ($disabled > 0) {
	$errors = true;
	response(VALIDATION_TOO_MANY_ATTEMPTS, $errors);
}

// Validation: check if this is a brute force attempt
$past = strtotime("-5 min") * 1000;
$events->search('value.action:failed AND @path.timestamp:[' . $past .' TO ' . $now .']');
$fail_total = $events->getTotalCount();
if ($fail_total >= 3) {
	$item->event('log')->post(['action' => 'disabled']);
	$errors = true;
	response(VALIDATION_TOO_MANY_ATTEMPTS, $errors);
}

// If all of the above validation checks pass, continue on
if (!$errors) {

	$data_encrypted = hex2bin($item->secret);	

	// Decrypt data, reference: https://github.com/defuse/php-encryption/
	try {
		$data_decrypted = Crypto::decryptWithPassword($data_encrypted, $password);
	} catch (Ex\WrongKeyOrModifiedCiphertextException $ex) {
		$item->event('log')->post(['action' => 'failed']);
		response(DECRYPTION_PASSWORD_WRONG, true);
	} catch (Ex\EnvironmentIsBrokenException $ex) {
		response(ENCRYPTION_UNSAFE, true);
	}
	
	// Delete message
	if ($item->delete()) {
		$item->event('log')->post(['action' => 'deleted']);
	} else {
		response($item->getStatus(), true);
	}	
	
	$data = unserialize($data_decrypted);
	
	// Send email to sender
	if(!empty($data["email_sender"])){

		$email_content = '<p>' . EMAIL_BODY_VIEWED . '</p>';
		$email_content .= '<p>---</p><p>Thank you,<br />' . SITE_NAME . '</p>';

		$sendemail = new SendGrid\Email();
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
	response(INTERNAL_ERROR, true);
}