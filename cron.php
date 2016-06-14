<?php
require_once 'common.php';

// Delete expired messages
$collection->search('expiration_date:[* TO ' . time() .']');
foreach ($collection as $item) {
	if ($item->delete()) {
		$item->event('log')->post(['action' => 'expired']);
	}
}