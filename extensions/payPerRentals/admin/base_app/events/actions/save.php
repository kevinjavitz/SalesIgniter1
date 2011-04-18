<?php
/*
	PPR Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$eventName = $_POST['events_name'];
	$eventDate = $_POST['events_date'];
	$eventDetails = $_POST['events_details'];
	$countryId = $_POST['events_country'];
	$state = $_POST['events_state'];
	
	if (!is_numeric($_POST['events_state'])){
		$Qcheck = Doctrine_Query::create()
		->select('zone_id')
		->from('Zones')
		->where('zone_name = ?', $_POST['events_state'])
		->orWhere('zone_code = ?', $_POST['events_state'])
		->execute(array(), Doctrine::HYDRATE_ARRAY);
		if ($Qcheck){
			$zoneId = (int)$Qcheck[0]['zone_id'];
		}
	}

	$shipping = implode(',', $_POST['ppr_shipping']);
	$Events = Doctrine_Core::getTable('PayPerRentalEvents');
	if (isset($_GET['eID'])){
		$Event = $Events->find((int) $_GET['eID']);
	}else{
		$Event = $Events->getRecord();
	}
	
	$Event->events_name = $eventName;
	$Event->events_date = $eventDate;
	$Event->events_details = $eventDetails;
	$Event->shipping = $shipping;
	$Event->events_state = $state;
	$Event->events_country_id = $countryId;
	$Event->events_zone_id = $zoneId;
	$Event->save();		
        EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'eID')) . 'eID=' . $Event->events_id, null, 'default'), 'redirect');
?>