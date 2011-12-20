<?php
	$Progress = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select message from progress_bar where name = "newLanguage"');
	
	EventManager::attachActionResponse($Progress[0]['message'], 'html');
?>