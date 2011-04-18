<?php
	$pointOfSale->setPaymentMethod($_GET['method']);
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>