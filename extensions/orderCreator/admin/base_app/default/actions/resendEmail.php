<?php
	$Orders = Doctrine_Core::getTable('Orders');
	$success = false;
	if (isset($_POST['oID']) && $_POST['oID'] != 'undefined'){
		require(sysConfig::getDirFsCatalog(). 'includes/classes/order.php');
		$Qorder = Doctrine_Query::create()
			->select('customers_id')
			->from('Orders')
			->where('orders_id = ?', $_POST['oID'])
			->fetchOne();
		$userAccount = new rentalStoreUser($Qorder['customers_id']);
		$userAccount->loadPlugins();
		$order = new OrderProcessor($_POST['oID']);
		$order->sendNewOrderEmail();
		$success = true;

	}

	EventManager::attachActionResponse(array(
		'success' => $success,
	), 'json');

?>