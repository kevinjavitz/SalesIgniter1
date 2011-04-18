<?php
	$customerID = $pointOfSale->createCustomerAccount();
	
	EventManager::attachActionResponse(array(
		'success'      => true,
		'customerID'   => $customerID,
		'customerName' => $_POST['lastname'] . ', ' . $_POST['firstname']
	), 'json');
?>