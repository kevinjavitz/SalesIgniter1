<?php
	$addressArray = array(
		'entry_gender'         => (isset($_POST['billing_gender']) ? $_POST['billing_gender'] : 'm'),
		'entry_company'        => $_POST['billing_company'],
		'entry_firstname'      => $_POST['billing_firstname'],
		'entry_lastname'       => $_POST['billing_lastname'],
		'entry_street_address' => $_POST['billing_street_address'],
		'entry_suburb'         => (isset($_POST['billing_suburb']) ? $_POST['billing_suburb'] : ''),
		'entry_postcode'       => $_POST['billing_postcode'],
		'entry_city'           => $_POST['billing_city'],
		'entry_state'          => $_POST['billing_state'],
		'entry_country_id'     => $_POST['billing_country'],
		'entry_zone_id'        => $_POST['billing_state']
	);
	$userAccount->plugins['addressBook']->addAddressEntry('billing', $addressArray);
		
	$parsedAddress = $userAccount->plugins['addressBook']->getAddress('billing');
	$userAccount->setZoneId($parsedAddress['entry_zone_id']);
	$userAccount->setCountryId($parsedAddress['entry_country_id']);

	if (!empty($_POST['billing_telephone'])){
		$onePageCheckout->onePage['info']['telephone'] = $_POST['billing_telephone'];
	}

	if (!empty($_POST['password'])){
		$onePageCheckout->onePage['info']['password'] = $_POST['password'];
	}
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>