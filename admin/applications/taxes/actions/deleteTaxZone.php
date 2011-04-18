<?php
	$Zone = Doctrine_Core::getTable('GeoZones')->find((int)$_GET['zID']);
	$success = false;
	if ($Zone){
		$Zone->delete();
		$success = true;
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>