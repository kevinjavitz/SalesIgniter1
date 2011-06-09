<?php
	$address_format = $_POST['address_format'];
	$address_summary = $_POST['address_summary'];

	$addressFormat = Doctrine_Core::getTable('AddressFormat');
	if (isset($_GET['fID'])){
		$addressFormat = $addressFormat->find((int)$_GET['fID']);
	}else{
		$addressFormat = new AddressFormat();
	}
	$addressFormat->address_format = $address_format;
	$addressFormat->address_summary = $address_summary;

	$addressFormat->save();
	
	EventManager::attachActionResponse(itw_app_link('fID=' . $addressFormat->address_format_id, 'address_format', 'default'), 'redirect');
?>