<?php
require_once 'common.php';

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
if((!isset($_POST['message']))
   || (!isset($_POST['encrypt_password']))
   || (!isset($_POST['encrypt_password_confirm']))
   || (!isset($_POST['email_recipient']))
   || (!isset($_POST['email_password_hint']))
   || (!isset($_POST['email_sender']))
   || (!isset($_POST['expiration_date']))
  ){
	$errors = true;
	response(VALIDATION_REQUIRED_FIELDS, $errors, $logger);
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
	response(VALIDATION_REQUIRED_FIELDS, $errors, $logger);
}					   

// Validation: check if textarea is too long
if(strlen($message) > 1000) {
	$errors = true;
	response(VALIDATION_TEXTAREA_LENGTH, $errors, $logger);
}

// Validation: check if passwords is long enough
if(strlen($password) < 8) {
	$errors = true;
	response(VALIDATION_PASSWORD_LENGTH, $errors, $logger);
}

// Validation: check if passwords match
if($password !== $password_confirm) {
	$errors = true;
	response(VALIDATION_PASSWORD_MISMATCH, $errors, $logger);
}		

// Validation: check for valid email format
if((!empty($email_recipient)) && (!filter_var($email_recipient, FILTER_VALIDATE_EMAIL))
   || (!empty($email_sender)) && (!filter_var($email_sender, FILTER_VALIDATE_EMAIL))
  ){
	$errors = true;
	response(VALIDATION_EMAIL_INVALID, $errors, $logger);
}

// Validation: check if password hint is too long
if(strlen($email_password_hint) > 200) {
	$errors = true;
	response(VALIDATION_PASSWORD_HINT_LENGTH, $errors, $logger);
}

// Validation: check for valid expiration date format
$date = DateTime::createFromFormat('m/d/Y', $expiration_date);
$date_errors = DateTime::getLastErrors();
if ($date_errors['warning_count'] + $date_errors['error_count'] > 0) {
	$errors = true;
	response(VALIDATION_DATE_INVALID, $errors, $logger);
}
if (strtotime($expiration_date) > strtotime("today +30 days")){
	$errors = true;
	response(VALIDATION_DATE_INVALID, $errors, $logger);
}

// If all of the above validation checks pass, continue on
if (!$errors) {

	// Create encryption key
	$length = 16;
	$iterations = 100000;
	$salt = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
	$key = hash_pbkdf2("sha256", $password, $salt . PASSWORD_PEPPER, $iterations, $length);

	// Create an array of data to be encrypted
	$data = serialize(array(
		"message" => $message,
		"email_sender" => $email_sender
	));

	// Encrypt data, reference: https://github.com/defuse/php-encryption/
	try {
		$data_encrypted = Crypto::Encrypt($data, $key);
	} catch (CryptoTestFailedException $ex) {
		response(ENCRYPTION_UNSAFE, true, $logger);
	} catch (CannotPerformOperationException $ex) {
		response(DECRYPTION_UNSAFE, true, $logger);
	}		

	// Store the encrypted data
	$array = array(
		'salt' => base64_encode($salt),
		'secret' => base64_encode($data_encrypted),
		'expiration_date' => strtotime($expiration_date . ' +1 day')
	);
	
	if($use_orchestrate){
		// Check if Orchestrate is enabled
		$item = $client->post(ORCHESTRATE_COLLECTION, $array);
		$id = $item->getKey();
		$logger->info('Message ID: ' . $id . ', ' . LOG_ORCHESTRATE_POST . ' Expiration date: ' . $expiration_date);
	} else {
		// Fallback to Flywheel
		$item = new \JamesMoss\Flywheel\Document($array);
		$repo->store($item);
		$id = $item->getId();
		$logger->info('Message ID: ' . $id . ', ' . LOG_FLYWHEEL_POST . ' Expiration date: ' . $expiration_date);
	}

	// Send email to recipient
	if(!empty($email_recipient)){

		// Email body
		$email_content = '<p>' . EMAIL_BODY_SENT . '</p>';
		$email_content .= '<p>Access it at: <a href="' . SITE_URL . '/?id=' . $id . '" target="_blank">' . SITE_URL . '/?id=' . $id . '</a></p>';
		if(!empty($email_password_hint)){
			$email_content .= '<p><strong>Password hint: </strong>' . $email_password_hint;
		}
		$email_content .= '<p>---</p><p>Thank you,<br />' . SITE_NAME . '</p>';

		if($use_sendgrid){
			
			// Check if SendGrid is enabled
			$sendemail->addTo($email_recipient)
				->setFrom(EMAIL_FROM_ADDRESS)
				->setSubject(EMAIL_SUBJECT_SENT)
				->setHtml($email_content);

			// Check for email errors and provide a response
			try {
				$sendgrid->send($sendemail);
				$logger->info('Message ID: ' . $id . ', ' . LOG_EMAIL_SENDGRID);
				$logger->info('Message ID: ' . $id . ', ' . LOG_MESSAGE_CREATED);
				response($id, false);
			} catch(\SendGrid\Exception $e) {
				foreach($e->getErrors() as $er) {
					response($er, true, $logger);
				}
			}
			
		} else {	
			
			// Fallback to PHP Mail
			$email = mail($email_recipient, EMAIL_SUBJECT_SENT, $email_content, $email_headers);
			
			// Check for email errors and provide a response
			if($email){
				$logger->info('Message ID: ' . $id . ', ' . LOG_EMAIL_PHP);
				$logger->info('Message ID: ' . $id . ', ' . LOG_MESSAGE_CREATED);
				response($id, false);
			} else {
				response(EMAIL_ERROR, true, $logger);		
			}
			
		}

	} else {
		// Provide response
		$logger->info('Message ID: ' . $id . ', ' . LOG_EMAIL_NONE);
		$logger->info('Message ID: ' . $id . ', ' . LOG_MESSAGE_CREATED);
		response($id, false);
	}

} else {
	// Unknown error
	$logger->alert(LOG_UNKNOWN_ERROR);
	die();
}