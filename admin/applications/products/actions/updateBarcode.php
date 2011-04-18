<?php
	$barcodeID = (int)$_GET['barcode_id'];

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>