<?php
	$TaxClass = Doctrine_Core::getTable('TaxClasses')->find((int) $_GET['cID']);
	$success = false;
	if ($TaxClass){
		$TaxClass->delete();
		$success = true;
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>