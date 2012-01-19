<?php
	$Admin = Doctrine_Core::getTable('Admin')->findByAdminId((int)$_GET['mID']);
	if ($Admin){
		$Admin->delete();
	}

	EventManager::attachActionResponse(array(
			'success' => true
		), 'json');
?>