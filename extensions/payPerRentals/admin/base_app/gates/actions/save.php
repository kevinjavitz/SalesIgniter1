<?php

	$gName = $_POST['gate_name'];

	$gates = Doctrine_Core::getTable('PayPerRentalGates');
	if (isset($_GET['gID'])){
		$gates = $gates->find((int)$_GET['gID']);
	}else{
		$gates = new PayPerRentalGates();
	}


	$gates->gate_name = $gName;
	$gates->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $gates->gates_id, null, 'default'), 'redirect');
?>