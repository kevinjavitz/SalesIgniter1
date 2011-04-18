<?php
	$Membership = Doctrine_Core::getTable('Membership')->findOneByPlanId((int)$_GET['pID']);
	$success = false;
	if ($Membership){
		$Membership->delete();
		$success = true;
	}

	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>