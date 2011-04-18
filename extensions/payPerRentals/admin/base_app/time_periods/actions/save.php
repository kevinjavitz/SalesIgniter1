<?php
/*
	PPR Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$periodName = $_POST['period_name'];
	$startDate = $_POST['period_start_date'];
 	$endDate = $_POST['period_end_date'];
	$periodDetails = $_POST['period_details'];

	$Periods = Doctrine_Core::getTable('PayPerRentalPeriods');
	if (isset($_GET['pID'])){
		$Period = $Periods->find((int) $_GET['pID']);
	}else{
		$Period = $Periods->getRecord();
	}
	
	$Period->period_name = $periodName;
	$Period->period_start_date = $startDate;
 	$Period->period_end_date = $endDate;
	$Period->period_details = $periodDetails;

	$Period->save();
    EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'pID=' . $Period->period_id, null, 'default'), 'redirect');
?>