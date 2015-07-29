<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
use andrefelipe\Orchestrate\Client;

// PHP debugging
if (DEBUG_MODE == true){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// Determine if user is on the homepage
$homepage = (strpos($_SERVER['SCRIPT_NAME'], 'index.php') !== false);

// Determine if a message is being accessed from link
$referral = (isset($_GET["id"]) && (!empty($_GET["id"])));

// Configure logging
$logger = new Katzgrau\KLogger\Logger(LOGGING_BASE_DIR, Psr\Log\LogLevel::DEBUG);

// Configure data store
$client = new Client(ORCHESTRATE_API_KEY);	

// Configure email settings
$sendgrid = new SendGrid(SENDGRID_API_KEY);
$sendemail = new SendGrid\Email();

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