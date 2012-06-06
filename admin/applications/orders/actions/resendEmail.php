<?php
	$Orders = Doctrine_Core::getTable('Orders');
	$success = false;
	if (isset($_POST['oID']) && $_POST['oID'] != 'undefined'){
		require(sysConfig::getDirFsCatalog(). 'includes/classes/order.php');
		$order = new OrderProcessor($_POST['oID']);
		$order->sendNewOrderEmail();
		$success = true;

	}

EventManager::attachActionResponse(array(
		'success' => $success,
	), 'json');


?>