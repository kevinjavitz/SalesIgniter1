<?php
	$QlastOrder = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersAddresses oa')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation ops')
			->leftJoin('o.OrdersTotal ot')
			->where('o.customers_id = ?', (int)$userAccount->getCustomerId())
			->andWhereIn('ot.module_type', array('ot_total','total'))
			->andWhere('oa.address_type = ?','billing')
			->orderBy('o.orders_id desc')
			->limit(1)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$pageContents = '';
	$contents = EventManager::notifyWithReturn('CheckoutSuccessFinishOutside', $QlastOrder[0], &$pageContents);

	$pageContent->set('pageTitle', '');
	$pageContent->set('pageContent', $pageContents);
?>