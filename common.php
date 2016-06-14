<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

// PHP debugging
if (DEBUG_MODE == true){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

// Determine if user is on the homepage
$homepage = (strpos($_SERVER['SCRIPT_NAME'], 'index.php') !== false);

// Determine if a message is being accessed from link
$referral = (isset($_GET["id"]) && (!empty($_GET["id"])));

// Configure data store
$application = new andrefelipe\Orchestrate\Application(ORCHESTRATE_API_KEY);
$collection = $application->collection(ORCHESTRATE_COLLECTION);

// Configure email settings
$sendgrid = new SendGrid(SENDGRID_API_KEY);

// Function to return JSON to the requesting page
function response($msg, $errors){
	$response_array = array(
		'msg' => $msg,
		'errors' => $errors
	);
	header('Content-type: application/json');
	die(json_encode($response_array));
}