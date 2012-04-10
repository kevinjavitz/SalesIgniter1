<?php

	$tfName = $_POST['timefees_name'];
	$tfFee = $_POST['timefees_fee'];
	$tfStart = $_POST['timefees_start'];
	$tfEnd = $_POST['timefees_end'];

	$timefees = Doctrine_Core::getTable('PayPerRentalTimeFees');
	if (isset($_GET['tfID'])){
		$timefees = $timefees->find((int)$_GET['tfID']);
	}else{
		$timefees = new PayPerRentalTimeFees();
	}


	$timefees->timefees_name = $tfName;
	$timefees->timefees_fee = $tfFee;
	$timefees->timefees_start = $tfStart;
	$timefees->timefees_end = $tfEnd;
	$timefees->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'tfID')) . 'tfID=' . $timefees->timefees_id, null, 'default'), 'redirect');
?>