<?php
require_once 'common.php';

// Initial validation state
$errors = false;

// Validation: check if it's a post request
if($_SERVER['REQUEST_METHOD'] != "POST") {
	$errors = true;
	die('Access Denied.');
}

// Validation: check if it's an ajax request
if((!isset($_SERVER['HTTP_X_REQUESTED_WITH']))
   AND (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest')
  ){
	$errors = true;
	die('Access Denied.');
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
if($use_orchestrate){
	if(strlen($password) > 16) {
		$errors = true;
		response(VALIDATION_MESSAGE_LENGTH, $errors);
	}	
} else {
	if(strlen($password) > 8) {
		$errors = true;
		response(VALIDATION_MESSAGE_LENGTH, $errors);
	}	
}

// Validation: check if message exists
if($use_orchestrate){
	$item = $client->get(ORCHESTRATE_COLLECTION, $id);
	if ($item->isSuccess()) {
		$salt = base64_decode($item->salt);
		$data_encrypted = base64_decode($item->secret);
	} else {
		$errors = true;
		response(VALIDATION_MESSAGE_NOTFOUND, $errors);
		//response($item->getStatus(), $errors);
	}
} else {
	if(!$repo->findById($id)) {
		$errors = true;
		response(VALIDATION_MESSAGE_NOTFOUND, $errors);
	} else {
		$item = $repo->findById($id);
		$salt = base64_decode($item->salt);
		$data_encrypted = base64_decode($item->secret);
	}
}

// If all of the above validation checks pass, continue on
if (!$errors) {

	// Create decryption key
	$length = 16;
	$iterations = 10000;
	$key = hash_pbkdf2("sha256", $password, $salt . PASSWORD_PEPPER, $iterations, $length);	

	// Decrypt data, reference: https://github.com/defuse/php-encryption/
	try {
		$data_decrypted = Crypto::Decrypt($data_encrypted, $key);
	} catch (InvalidCiphertextException $ex) { // VERY IMPORTANT
		response(DECRYPTION_PASSWORD_WRONG, true);
	} catch (CryptoTestFailedException $ex) {
		response(ENCRYPTION_UNSAFE, true);
	} catch (CannotPerformOperationException $ex) {
		response(DECRYPTION_UNSAFE, true);
	}			

	// Delete message
	if($use_orchestrate){
		// Check if Orchestrate is enabled
		$item = $client->purge(ORCHESTRATE_COLLECTION, $id);
	} else {
		// Fallback to Flywheel
		$repo->delete($id);
	}

	$data = unserialize($data_decrypted);
	
	// Send email to sender
	if(!empty($data["email_sender"])){

		// Email body
		$email_content = '<p>' . EMAIL_BODY_VIEWED . '</p>';
		$email_content .= '<p>---</p><p>Thank you,<br />' . SITE_NAME . '</p>';

		if($use_sendgrid){
			
		// Check if SendGrid is enabled
			$sendemail->addTo($data["email_sender"])
				->setFrom(EMAIL_FROM_ADDRESS)
				->setSubject(EMAIL_SUBJECT_VIEWED)
				->setHtml($email_content);

			$sendgrid->send($sendemail);
			
		} else {	
			
			// Fallback to PHP Mail
			mail($data["email_sender"], EMAIL_SUBJECT_VIEWED, $email_content, $email_headers);
			
		}

	}	
		
	// Provide response
	response($data["message"], false);

} else {
	die('Access Denied.');
}