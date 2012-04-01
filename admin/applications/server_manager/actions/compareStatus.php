<?php
	$Progress = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select message, percentage from progress_bar where name = "fileCompare"');
	
	EventManager::attachActionResponse(array(
		'message' => $Progress[0]['message'],
		'percent' => $Progress[0]['percentage']
	), 'json');
?>