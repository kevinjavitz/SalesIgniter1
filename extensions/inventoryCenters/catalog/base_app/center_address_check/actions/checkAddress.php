<?php
	$serviceAreas = getServiceAreas();

	$countryName = tep_get_country_name($_POST['country']);
	$point = array(
		'entry_street_address' => $_POST['street_address'],
		'entry_city'           => $_POST['city'],
		'entry_postcode'       => $_POST['postcode'],
		'entry_country_name'   => $countryName,
		'entry_state'          => $_POST['state']
	);
	$coordinates = getGoogleCoordinates($point);

	if (isset($navigation->snapshot) && sizeof($navigation->snapshot) > 0){
		$snap = $navigation->snapshot;
		$redirectUrl = itw_app_link($snap['get']);
	}else{
		$redirectUrl = itw_app_link(null, 'index', 'default');
	}

	$json = array(
		'success'   => true,
		'inService' => false,
		'msgStack'  => $messageStack->parseTemplate('pageStack', 'Sorry we currently do not service your area, we are working to expand to service more locations.', 'error')
	);
	
	for($i=0; $i<sizeof($serviceAreas); $i++){
		if (polygonContains($serviceAreas[$i]['decoded'], $coordinates['lng'], $coordinates['lat']) === true){
			Session::set('addressCheck', array(
				'address' => array(
					'street_address' => $_POST['street_address'],
					'city'           => $_POST['city'],
					'state'          => $_POST['state'],
					'country'        => $_POST['country'],
					'suburb'         => $_POST['suburb'],
					'postcode'       => $_POST['postcode']
				),
				'systemSelected' => $serviceAreas[$i]['id']
			));

			$messageStack->addSession('pageStack', 'You are in one of our service areas, your address and service area has been saved for this visit.', 'success');
			if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
				$json = array(
					'success'     => true,
					'inService'   => true,
					'redirectUrl' => $redirectUrl
				);
				break;
			}
		}
	}

	if ($json['inService'] === false){
		Session::set('addressCheck', false);
	}
	
	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse($json, 'json');
	}else{
		$messageStack->addSession('pageStack', $errorMessage, 'error');
		EventManager::attachActionResponse(itw_app_link('appExt=inventoryCenters', 'center_address_check', 'default'), 'redirect');
	}
?>