<?php
	$Customers = Doctrine::getTable('Customers')->find((int)$_GET['cID']);
	if ($Customers){
		$Customers->delete();
	}
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>