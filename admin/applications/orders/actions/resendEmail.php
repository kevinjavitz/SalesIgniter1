<?php
	$Orders = Doctrine_Core::getTable('Orders');
	$success = false;
	if (isset($_POST['oID']) && $_POST['oID'] != 'undefined'){
		$Qcustomer = Doctrine_Query::create()
		->from('Orders')
		->where('orders_id = ?', $_POST['oID'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$userAccount = new RentalStoreUser($Qcustomer[0]['customers_id']);
		$userAccount->loadPlugins();
		//$userAccount->loadCustomersInfo()
		require(sysConfig::getDirFsCatalog(). 'includes/classes/order.php');
		$order = new OrderProcessor($_POST['oID']);
		$order->sendNewOrderEmail();
		$success = true;

	}

EventManager::attachActionResponse(array(
		'success' => $success,
	), 'json');


?>