<?php

	$tfName = $_POST['timefees_name'];
	$tfDesc = $_POST['timefees_description'];
	$tfFee = $_POST['timefees_fee'];
	$tfMandatory = 0;
	if(isset($_POST['timefees_mandatory'])){
		$tfMandatory = 1;
	}
	$tfHours = $_POST['timefees_hours'];

	$timefees = Doctrine_Core::getTable('PayPerRentalExtraFees');
	if (isset($_GET['tfID'])){
		$timefees = $timefees->find((int)$_GET['tfID']);
	}else{
		$timefees = new PayPerRentalExtraFees();
	}


	$timefees->timefees_name = $tfName;
	$timefees->timefees_description = $tfDesc;
	$timefees->timefees_fee = $tfFee;
	$timefees->timefees_hours = $tfHours;
	$timefees->timefees_mandatory = $tfMandatory;
	$timefees->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'tfID')) . 'tfID=' . $timefees->timefees_id, null, 'default'), 'redirect');
?>