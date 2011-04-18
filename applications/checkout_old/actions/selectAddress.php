<?php
	switch($_POST['address_type']){
		case 'billing':
			$sessVar = 'billing';
			break;
		case 'shipping':
			$sessVar = 'delivery';
			break;
		case 'pickup':
			$sessVar = 'pickup';
			break;
	}

	$onePageCheckout->onePage[$sessVar . 'AddressId'] = $_POST['address'];

	$address = $userAccount->plugins['addressBook']->getAddress($_POST['address']);
	$userAccount->plugins['addressBook']->addAddressEntry($sessVar, $address);
		
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>