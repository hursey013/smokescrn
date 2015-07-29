<?php
require_once 'common.php';

$logger->debug('Cron job initiated.');

// Delete expired messages
$items = $client->search(ORCHESTRATE_COLLECTION, 'expiration_date:[* TO ' . time() .']');
$array =  (array) $items->getResults();
foreach ($items as $item) {
	$client->purge(ORCHESTRATE_COLLECTION, $item->getKey());
	$logger->info('Message ID: ' . $item->getKey() . ', expired and was deleted.');
}	

$logger->debug('Cron job finished.');