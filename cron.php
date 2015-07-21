<?php
require_once 'common.php';

// Delete expired messages
if($use_orchestrate){
	// Check if Orchestrate is enabled
	$items = $client->search(ORCHESTRATE_COLLECTION, 'expiration_date:[' . time() . ' TO *]');
	foreach ($items as $item) {
		$client->purge(ORCHESTRATE_COLLECTION, $item->getId());
	}	
} else {
	// Fallback to Flywheel
	$items = $repo->query()
		->where('expiration_date', '<', time())
		->execute();
	foreach($items as $item) {
		$repo->delete($item);
	}
}