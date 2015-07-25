<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
use andrefelipe\Orchestrate\Client;

// PHP debugging
if (DEBUG_MODE == true){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// Determine if a message is being accessed from link
$referral = (isset($_GET["id"]) && (!empty($_GET["id"])));

// Configure logging
$logger = new Katzgrau\KLogger\Logger(LOGGING_BASE_DIR, Psr\Log\LogLevel::DEBUG);

// Configure data store
if(defined('ORCHESTRATE_API_KEY')){
	// Determine if Orchestrate.io is being used
	$use_orchestrate = true;
	$client = new Client(ORCHESTRATE_API_KEY);	
} else {
	// Fallback on FlyWheel
	$use_orchestrate = false;
	$config = new \JamesMoss\Flywheel\Config(FLYWHEEL_BASE_DIR);
	$repo = new \JamesMoss\Flywheel\Repository(FLYWHEEL_REPO_DIR, $config);
}

// Configure email settings
if(defined('SENDGRID_API_KEY')){
	// Determine if SendGrid is being used
	$use_sendgrid = true;
	$sendgrid = new SendGrid(SENDGRID_API_KEY);
	$sendemail = new SendGrid\Email();
} else {
	// Fallback on PHP Mail
	$use_sendgrid = false;
	$email_headers = "From: " . EMAIL_FROM_ADDRESS . "\r\n";
	$email_headers .= "Reply-To: ". EMAIL_FROM_ADDRESS . "\r\n";
	$email_headers .= "MIME-Version: 1.0\r\n";
	$email_headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";		
}

// Function to return JSON to the requesting page
function response($msg, $errors, $logger = null){
	if ($errors){
		$logger->error($msg);
	}
	$response_array = array(
		'msg' => $msg,
		'errors' => $errors
	);
	header('Content-type: application/json');
	die(json_encode($response_array));
}