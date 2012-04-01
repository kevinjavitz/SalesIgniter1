<?php
	$Progress = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select message, percentage from progress_bar where name = "snapshotStatus"');
	
	EventManager::attachActionResponse(array(
		'message' => $Progress[0]['message'],
		'percent' => $Progress[0]['percentage']
	), 'json');
?>