<?php
/*
	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	


	$appContent = $App->getAppContentFile();
	
	if ($App->getPageName() == 'history_inventory_info'){

		if (!isset($_GET['order_id']) || (isset($_GET['order_id']) && !is_numeric($_GET['order_id']))){
			tep_redirect(itw_app_link('appExt=inventoryCenters', 'account_addon', 'view_orders_inventory', 'SSL'));
		}

		$pickupz = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.OrdersProductsReservation ops')
		->where('o.orders_id =?', (int)$_GET['order_id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$Qinv = Doctrine_Core::getTable('ProductsInventoryCenters')->findOneByInventoryCenterId($pickupz[0]['OrdersProducts'][0]['OrdersProductsReservation'][0]['inventory_center_pickup']);
		$customer_id = $Qinv->inventory_center_customer;

        if ($customer_id != $userAccount->getCustomerId()) {
			tep_redirect(itw_app_link('appExt=inventoryCenters', 'account_addon', 'view_orders_inventory', 'SSL'));
		}

		$custInfo = Doctrine_Core::getTable('Customers')->findOneByCustomersEmailAddress($pickupz[0]['customers_email_address']);

		require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
		$Order = new Order($_GET['order_id']);

		$breadcrumb->add(sprintf(sysLanguage::get('NAVBAR_TITLE_HISTORY'), $_GET['order_id']), itw_app_link('appExt=inventoryCenters', 'account_addon', 'view_orders_inventory', 'SSL'));
		$breadcrumb->add(sprintf(sysLanguage::get('NAVBAR_TITLE_HISTORY_INFO'), $_GET['order_id']), itw_app_link('appExt=inventoryCenters&order_id=' . $_GET['order_id'], 'account_addon', 'history_inventory_info', 'SSL'));
	}

?>