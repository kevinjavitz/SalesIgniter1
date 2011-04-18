<?php
	$errMsg = '';
	$success = false;
	
	$Qcheck = Doctrine_Query::create()
	->select('locked')
	->from('Newsletters')
	->where('newsletters_id = ?', (int) $_GET['nID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qcheck[0]['locked'] < 1){
		$errMsg = sysLanguage::get('ERROR_REMOVE_UNLOCKED_NEWSLETTER');
	}else{
		$Newsletter = Doctrine_Core::getTable('Newsletters')->find((int) $_GET['nID']);
		if ($Newsletter){
			$Newsletter->delete();
			$success = true;
		}
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success,
		'errorMessage' => $errMsg
	), 'json');
?>