<?php
/*
	PPR Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$periodName = $_POST['block_name'];

	$startDate = date('Y-m-d H:i:s',strtotime(trim($_POST['block_start_date'])));
 	$endDate = date('Y-m-d H:i:s',strtotime(trim($_POST['block_end_date'])));
	$reccuring_year = (isset($_POST['reccuring_year'])?1:0);
	$reccuring_month = (isset($_POST['reccuring_month'])?1:0);
	$reccuring_day = (isset($_POST['reccuring_day'])?1:0);

	$Periods = Doctrine_Core::getTable('PayPerRentalBlockedDates');
	if (isset($_GET['pID'])){
		$Period = $Periods->find((int) $_GET['pID']);
	}else{
		$Period = $Periods->getRecord();
	}
	
	$Period->block_name = $periodName;
	$Period->block_start_date = $startDate;
 	$Period->block_end_date = $endDate;
	$Period->recurring_year = $reccuring_year;
	$Period->recurring_month = $reccuring_month;
	$Period->recurring_day = $reccuring_day;

	$Period->save();
    EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'pID=' . $Period->block_dates_id, null, 'default'), 'redirect');
?>