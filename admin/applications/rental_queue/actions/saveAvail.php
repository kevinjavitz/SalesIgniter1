<?php


	$Ratio = $_POST['ratio'];
	$RentalAvailability = Doctrine_Core::getTable('RentalAvailability');

	if (isset($_GET['arID'])){
		$Avail = $RentalAvailability->find((int)$_GET['arID']);
	}else{
		$Avail = $RentalAvailability->create();
	}

	$Description = $Avail->RentalAvailabilityDescription;
	foreach($_POST['name'] as $langId => $Name){
		$Description[$langId]->language_id = $langId;
		$Description[$langId]->name = $Name;
	}

	$Avail->ratio = $Ratio;
	$Avail->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')) . '&arID=' . $Avail->rental_availability_id), 'redirect');

?>