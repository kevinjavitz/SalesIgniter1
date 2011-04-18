<?php
	$Admin = Doctrine_Core::getTable('Admin')->findByAdminId((int)$_GET['mID']);
	if ($Admin){
		$Admin->delete();
	}

	EventManager::attachActionResponse(itw_app_link(null, null, 'default'), 'redirect');
?>