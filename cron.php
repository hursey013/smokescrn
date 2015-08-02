<?php
require_once 'common.php';

$logger->info('Cron job initiated.');

// Delete expired messages
$collection->search('expiration_date:[* TO ' . time() .']');
foreach ($collection as $item) {
	$item->delete();	
	// Log event
	if ($item->delete()) {
		$item->event('log')->post(['action' => 'expired']);
	} else {
		$logger->error($item->getStatus());
	}	
}