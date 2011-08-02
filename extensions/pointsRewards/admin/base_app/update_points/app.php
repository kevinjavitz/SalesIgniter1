<?php
	$appContent = $App->getAppContentFile();

	require(DIR_WS_CLASSES . 'currencies.php');
	$currencies = new currencies();

	$appContent = $App->getAppContentFile();

	$pointsRewardsPointsEarnedOrders = Doctrine_Query::create()
			->select('orders_id')
			->from('pointsRewardsPointsEarned')
			->addWhere('orders_id > 0')
			->addGroupBy('orders_id')
			->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
	$orders = false;
	foreach($pointsRewardsPointsEarnedOrders as $ordersExisting){
		$orders[] = $ordersExisting['orders_id'];
	}

	$ordersQuery = Doctrine_Query::create()
			->select('o.orders_id, o.customers_id, o.orders_status, o.date_purchased,  pr.purchase_type, pr.percentage, op.products_id, op.purchase_type, op.final_price, op.products_price, op.products_quantity')
			->from('Orders o')
			->leftJoin('o.pointsRewardsOrderStatuses os')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.pointsRewardsPurchaseTypes pr')
			->addWhere('op.orders_id = o.orders_id');
	if($orders && count($orders)){
		$ordersQuery->andWhere('o.orders_id not in (" ? ")',implode(',',$orders));
	}
	$Qorders = $ordersQuery->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qorders as $order)	{
		if($orders && in_array($order['orders_id'],$orders))
			continue;


		foreach($order['OrdersProducts'] as $product){
			if($product['pointsRewardsPurchaseTypes'][0]['percentage'] <= 0)
				continue;
			$productsPrice = $product['products_price'];
			$royaltiesSystemOrderTotals =  Doctrine_Core::getTable('OrdersTotal')->findOneByOrdersIdAndModuleType($order['orders_id'],'coupon');
			if($royaltiesSystemOrderTotals != false) {
				$coupon_code = explode(':',$royaltiesSystemOrderTotals->title);
				$coupon_code = $coupon_code[1];

				$coupon =  Doctrine_Core::getTable('Coupons')->findOneByCouponCode($coupon_code);
				if($coupon != false) {
					if($coupon->coupon_type == 'P') {
						$discountPrice = $productsPrice *  $coupon->coupon_amount / 100;
						$productsPrice -= $discountPrice;
					}
				}
			}
			
			$pointsEarned = $productsPrice * $product['pointsRewardsPurchaseTypes'][0]['percentage'] / 100 * $product['products_quantity'];
			$pointsRewardsPointsEarnedNew = new pointsRewardsPointsEarned;
			$pointsRewardsPointsEarnedNew->products_id = $product['products_id'];
			$pointsRewardsPointsEarnedNew->purchase_type = $product['purchase_type'];
			$pointsRewardsPointsEarnedNew->orders_id = $order['orders_id'];
			$pointsRewardsPointsEarnedNew->points = $pointsEarned;
			$pointsRewardsPointsEarnedNew->customers_id = $order['customers_id'];
			$pointsRewardsPointsEarnedNew->date = $order['date_purchased'];
			$pointsRewardsPointsEarnedNew->save();
		}

	}

?>