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
	$eventDays = $_POST['events_days'];
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
	$gates = implode(',', $_POST['ppr_gates']);
	$default_gate = isset($_POST['default_gate'][0])?$_POST['default_gate'][0]:$_POST['ppr_gates'][0];

	$Events = Doctrine_Core::getTable('PayPerRentalEvents');
	if (isset($_GET['eID'])){
		$Event = $Events->find((int) $_GET['eID']);
	}else{
		$Event = $Events->getRecord();
	}
	
	$Event->events_name = $eventName;
	$Event->events_date = $eventDate;
	$Event->events_days = $eventDays;
	$Event->events_details = $eventDetails;
	$Event->shipping = $shipping;
	$Event->gates = $gates;
	$Event->default_gate = $default_gate;
	$Event->events_state = $state;
	$Event->events_country_id = $countryId;
	$Event->events_zone_id = $zoneId;
	$Event->save();

	$ProductQtyToEventsTable = Doctrine_Core::getTable('ProductQtyToEvents');
	Doctrine_Query::create()
	->delete('ProductQtyToEvents')
	//->whereNotIn('price_per_rental_per_products_id', $saveArray)
	->andWhere('events_id =?', $Event->events_id)
	->execute();

	if(isset($_POST['event_products'])){
		foreach($_POST['event_products'] as $prodevid => $iprodev){
			$ProductQtyToEvents = $ProductQtyToEventsTable->create();
			$ProductQtyToEvents->products_model = $iprodev['products_model'];
			$ProductQtyToEvents->qty = $iprodev['qty'];
			$ProductQtyToEvents->events_id = $Event->events_id;
			$ProductQtyToEvents->save();
		}
	}
        EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'eID')) . 'eID=' . $Event->events_id, null, 'default'), 'redirect');
?>