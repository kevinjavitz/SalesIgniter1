<?php
	$TaxRate = Doctrine_Core::getTable('TaxRates')->find((int) $_GET['rID']);
	$success = false;
	if ($TaxRate){
		$TaxRate->delete();
		$success = true;
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>