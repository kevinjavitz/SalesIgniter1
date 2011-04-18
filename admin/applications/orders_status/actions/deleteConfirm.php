<?php
	$OrdersStatus = Doctrine_Core::getTable('OrdersStatus')->find((int) $_GET['sID']);
	if ($OrdersStatus){
		$OrdersStatus->delete();
	}

	EventManager::attachActionResponse(array(
		'success' => true,
	), 'json');
?>