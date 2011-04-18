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
		'success'   => true,
		'msgStack'  => $messageStack->parseTemplate('pageStack', 'Sorry we currently do not have any shipping methods for your area.', 'error')
	);

	$shipMethodsIn = getShippingMethods($coordinates['lng'], $coordinates['lat']);
	$shippingMethodsIds = array();
	for($i=0; $i<sizeof($shipMethodsIn); $i++){
			if ($i == 0){
				Session::set('PPRaddressCheck', array(
					'address' => array(
						'street_address' => $_POST['street_address'],
						'city'           => $_POST['city'],
						'state'          => $_POST['state'],
						'country'        => $_POST['country'],
						'postcode'       => $_POST['postcode']
					)
				));
			}
		$shippingMethodsIds[] = $shipMethodsIn[$i]['id'];
	}

	if (count($shippingMethodsIds) <= 0){
		Session::set('PPRaddressCheck', false);
	}else{
		Session::set('PPRaddressCheck', array(
			'shippingMethodsIds' => $shippingMethodsIds
			)
		);
		$json = array(
			'success'   => true,
			'msgStack'  => $messageStack->parseTemplate('pageStack', 'We have selected the shipping methods available for your area. They will be available to you for the whole session.', 'success')
		);
	}
	
	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse($json, 'json');
	}else{
		$messageStack->addSession('pageStack', 'Page not accesible', 'error');
		EventManager::attachActionResponse(itw_app_link('appExt=payPerRentals', 'address_check', 'default'), 'redirect');
	}
?>