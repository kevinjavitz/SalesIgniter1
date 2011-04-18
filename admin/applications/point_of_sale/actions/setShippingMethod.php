<?php
	$pointOfSale->setShippingMethod($_GET['method']);
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>