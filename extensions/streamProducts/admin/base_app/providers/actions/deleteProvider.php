<?php
	$Provider = Doctrine_Core::getTable('ProductsStreamProviders')->find((int) $_GET['pID']);
	$success = false;
	if ($Provider){
		$success = true;
		$Provider->delete();
	}
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>