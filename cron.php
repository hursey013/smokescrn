<?php
require_once 'common.php';

$logger->debug('Cron job initiated.');

// Delete expired messages
if($use_orchestrate){
	// Check if Orchestrate is enabled
	$items = $client->search(ORCHESTRATE_COLLECTION, 'expiration_date:[' . time() . ' TO *]');
	$array =  (array) $items->getResults();
	foreach ($items as $item) {
		$client->purge(ORCHESTRATE_COLLECTION, $item->getKey());
		$logger->info('Message ID: ' . $item->getKey() . ' has expired and was deleted.');
	}	
} else {
	// Fallback to Flywheel
	$items = $repo->query()
		->where('expiration_date', '<', time())
		->execute();
	$array =  (array) $items;
	foreach($items as $item) {
		$repo->delete($item);
		$logger->info('Message ID: ' . $item->getID() . ' has expired and was deleted.');
	}
}

$logger->debug('Cron job finished.');