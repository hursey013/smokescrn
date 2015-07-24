<?php
require_once 'vendor/autoload.php';
require_once 'config.php';
use andrefelipe\Orchestrate\Client;

// Determine if a message is being accessed from link
$referral = (isset($_GET["id"]) && (!empty($_GET["id"])));

// Configure logging
$ip_address = get_client_ip();
$logFormat = "[{date}] [$ip_address] [{level}] {message}";
$logger = new Katzgrau\KLogger\Logger(LOGGING_BASE_DIR, Psr\Log\LogLevel::DEBUG, array (
    'logFormat' => $logFormat
));

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

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}