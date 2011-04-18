<?php
	$Country = Doctrine_Core::getTable('Countries')->find((int)$_GET['cID']);
	$success = false;
	if ($Country){
		$Country->delete();
		$success = true;
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>