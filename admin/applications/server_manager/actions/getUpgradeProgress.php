<?php
	$globalProgress = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select message, percentage from progress_bar where name = "upgradeCheckGlobal"');
	
	$processProgress = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select message, percentage from progress_bar where name = "upgradeCheckProcess"');
	
	EventManager::attachActionResponse(array(
		'globalMessage' => $globalProgress[0]['message'],
		'processMessage' => $processProgress[0]['message'],
		'globalPercent' => $globalProgress[0]['percentage'],
		'processPercent' => $processProgress[0]['percentage']
	), 'json');
?>