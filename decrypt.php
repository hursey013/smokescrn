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

// Validation: make sure this isn't a brute force attempt
$now = strtotime("now") * 1000;
$past = strtotime("-5 min") * 1000;
$events = $collection->events($id, 'log');
$events->search('value.action:failed AND @path.timestamp:[' . $past .' TO ' . $now .']', '@path.timestamp:desc');
$fail_total = $events->getTotalCount();
$fail_array = $events->toArray();
$fail_last = $fail_array["results"][0]["path"]["timestamp"];
$fail_good = strtotime("+5 min", ($fail_last/1000)) * 1000;		  
if (($fail_total >= 3) && ($now < $fail_good)) {
	$errors = true;
	response(VALIDATION_TOO_MANY_ATTEMPTS, $errors);
}

// If all of the above validation checks pass, continue on
if (!$errors) {

	$salt = Crypto::hexToBin($item->salt);
	$data_encrypted = Crypto::hexToBin($item->secret);	
	
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
		response(DECRYPTION_PASSWORD_WRONG, true);
	} catch (Ex\CryptoTestFailedException $ex) {
		response(ENCRYPTION_UNSAFE, true);
	} catch (Ex\CannotPerformOperationException $ex) {
		response(DECRYPTION_UNSAFE, true);
	}			

	// Delete message
	$item->delete();	
	
	// Log event
	if ($item->delete()) {
		$item->event('log')->post(['action' => 'deleted']);
	} else {
		$logger->error($item->getStatus());
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

		try {
			$sendgrid->send($sendemail);
		} catch(\SendGrid\Exception $e) {
			foreach($e->getErrors() as $er) {
				$logger->error($er);
			}
		}		
		
	}	
		
	// Provide response
	response($data["message"], false);

} else {
	// Unknown error
	$logger->error(LOG_UNKNOWN_ERROR);
	die();
}