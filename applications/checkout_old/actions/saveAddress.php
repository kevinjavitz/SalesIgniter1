<?php
	$country = (int)$_POST['country'];
	if (ACCOUNT_STATE == 'true') {
		if (isset($_POST['zone_id'])) {
			$zone_id = (int)$_POST['zone_id'];
		} else {
			$zone_id = false;
		}
		$state = $_POST['state'];

		$zone_id = 0;
		$Qcheck = dataAccess::setQuery('select count(*) as total from {zones} where zone_country_id = {country}');
		$Qcheck->setTable('{zones}', TABLE_ZONES);
		$Qcheck->setValue('{country}', $country);
		$Qcheck->runQuery();

		$entry_state_has_zones = ($Qcheck->getVal('total') > 0);
		if ($entry_state_has_zones == true) {
			$Qzone = dataAccess::setQuery('select distinct zone_id from {zones} where zone_country_id = {country} and (zone_name = {state} or zone_code = {state})');
			$Qzone->setTable('{zones}', TABLE_ZONES);
			$Qzone->setValue('{country}', (int)$country);
			$Qzone->setValue('{state}', $state);
			$Qzone->runQuery();
			if ($Qzone->numberOfRows() == 1) {
				$zone_id = $Qzone->getVal('zone_id');
			}
		}
	}

	$addressArray = array(
		'entry_firstname'      => $_POST['firstname'],
		'entry_lastname'       => $_POST['lastname'],
		'entry_street_address' => $_POST['street_address'],
		'entry_postcode'       => $_POST['postcode'],
		'entry_city'           => $_POST['city'],
		'entry_country_id'     => $_POST['country'],
		'entry_gender'         => $_POST['gender'],
		'entry_company'        => $_POST['company'],
		'entry_suburb'         => $_POST['suburb'],
		'entry_zone_id'        => $zone_id,
		'entry_state'          => $state
	);

	$addressBook =& $userAccount->plugins['addressBook'];
	$addressBook->updateAddress($_POST['address_id'], $addressArray);

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>