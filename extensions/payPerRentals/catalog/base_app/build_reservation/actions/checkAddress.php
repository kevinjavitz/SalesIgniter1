<?php

	$countryName = tep_get_country_name($_POST['country']);
	$point = array(
		'entry_street_address' => $_POST['street_address'],
		'entry_city'           => $_POST['city'],
		'entry_postcode'       => $_POST['postcode'],
		'entry_country_name'   => $countryName,
		'entry_state'          => $_POST['state']
	);
	$coordinates = getPPRGoogleCoordinates($point);

	$json = array(
		'success'   => false,
		'message'  => sysLanguage::get('EXTENSION_PAY_PER_RENTALS_ADDRESS_CHECK_ERROR')
	);

	$shipMethodsIn = getShippingMethods($coordinates['lng'], $coordinates['lat']);
	$shippingMethodsIds = array();
	$pprCheck = array();
	for($i=0; $i<sizeof($shipMethodsIn); $i++){
			if ($i == 0){
				$pprCheck['address'] = array(
						'street_address' => $_POST['street_address'],
						'city'           => $_POST['city'],
						'state'          => $_POST['state'],
						'country'        => $_POST['country'],
						'postcode'       => $_POST['postcode']
				);
			}
		$shippingMethodsIds[] = $shipMethodsIn[$i]['id'];
	}

	if (count($shippingMethodsIds) <= 0){
		Session::remove('PPRaddressCheck');
		$json = array(
			'success'   => false,
			'message'  => sysLanguage::get('EXTENSION_PAY_PER_RENTALS_ADDRESS_CHECK_ERROR')
		);
	}else{
		$pprCheck['shippingMethodsIds'] = $shippingMethodsIds;

		Session::set('PPRaddressCheck', $pprCheck);
		$json = array(
			'success'   => true,
			'methods'   => $shippingMethodsIds,
			'message'  => sysLanguage::get('EXTENSION_PAY_PER_RENTALS_ADDRESS_CHECK_SUCCESS')
		);
	}

	EventManager::attachActionResponse($json, 'json');
?>