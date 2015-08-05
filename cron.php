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

// Update stats in Numerous
if(defined('NUMEROUS_API_KEY')) {
	require_once 'numerous.php';
	$events = $collection->events();
	$events->setType('log');
	$events->search('value.action:created', null, 'value.action:top_values');
	$count = $events->getTotalCount();
	$n = new GX\Numerous(NUMEROUS_API_KEY);
	$n->createEvent(NUMEROUS_METRIC_ID, $count);
}