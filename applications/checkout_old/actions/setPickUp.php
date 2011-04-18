<?php
	$prefix = 'billing_';
	if (!empty($_POST['pickup_postcode'])){
		$prefix = 'pickup_';
	}

	$addressArray = array(
		'entry_gender'         => (isset($_POST[$prefix . 'gender']) ? $_POST[$prefix . 'gender'] : 'm'),
		'entry_company'        => $_POST[$prefix . 'company'],
		'entry_firstname'      => $_POST[$prefix . 'firstname'],
		'entry_lastname'       => $_POST[$prefix . 'lastname'],
		'entry_street_address' => $_POST[$prefix . 'street_address'],
		'entry_suburb'         => (isset($_POST[$prefix . 'suburb']) ? $_POST[$prefix . 'suburb'] : ''),
		'entry_postcode'       => $_POST[$prefix . 'postcode'],
		'entry_city'           => $_POST[$prefix . 'city'],
		'entry_state'          => $_POST[$prefix . 'state'],
		'entry_country_id'     => $_POST[$prefix . 'country'],
		'entry_zone_id'        => $_POST[$prefix . 'state']
	);
	$userAccount->plugins['addressBook']->addAddressEntry('pickup', $addressArray);
		
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>