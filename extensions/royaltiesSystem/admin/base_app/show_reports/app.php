<?php
require(DIR_WS_CLASSES . 'currencies.php');
$currencies = new currencies();

$appContent = $App->getAppContentFile();
$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');

$RoyaltiesSystemRoyaltiesEarnedOrders = Doctrine_Query::create()
		->select('orders_id')
		->from('RoyaltiesSystemRoyaltiesEarned')
		->addWhere('orders_id > 0')
		->addGroupBy('orders_id')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
$orders = false;
//var_dump($RoyaltiesSystemRoyaltiesEarnedOrders);
foreach($RoyaltiesSystemRoyaltiesEarnedOrders as $ordersExisting){
	$orders[] = $ordersExisting['orders_id'];
}
$ordersQuery = Doctrine_Query::create()
		->select('o.orders_id, o.customers_id, o.orders_status, o.date_purchased, pr.content_provider_id, pr.royalty_fee, op.products_id, op.purchase_type, op.final_price, op.products_price')
		->from('Orders o')
		->leftJoin('o.RoyaltiesSystemOrderStatuses os')
		->leftJoin('o.OrdersProducts op')
		->leftJoin('op.RoyaltiesSystemProductsRoyalties pr')
		->addWhere('o.orders_status = os.orders_status_id')
		->addWhere('op.orders_id = o.orders_id')
		->andWhere('op.purchase_type = pr.purchase_type')
		->andWhere('pr.content_provider_id > 0')
		->andWhere('op.products_id = pr.products_id');
if($orders && count($orders)){
	//$ordersQuery->andWhere('o.orders_id not in (" ? ")',implode(',',$orders));
}
EventManager::notify('OrdersListingBeforeExecute', &$ordersQuery);

$Qorders = $ordersQuery->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
foreach($Qorders as $order){
	if($orders && in_array($order['orders_id'], $orders)) continue;
	foreach($order['OrdersProducts'] as $product){
		$productsPrice = $product['products_price'];
		$royaltiesSystemOrderTotals = Doctrine_Core::getTable('OrdersTotal')->findOneByOrdersIdAndModuleType($order['orders_id'], 'coupon');

		if($royaltiesSystemOrderTotals != false){
			$coupon_code = explode(':', $royaltiesSystemOrderTotals->title);
			$coupon_code = $coupon_code[1];
			$coupon = Doctrine_Core::getTable('Coupons')->findOneByCouponCode($coupon_code);
			if($coupon != false){
				if($coupon->coupon_type == 'P'){
					$discountPrice = $productsPrice * $coupon->coupon_amount / 100;
					$productsPrice -= $discountPrice;
				}
			}
		}
		if(strpos($product['RoyaltiesSystemProductsRoyalties'][0]['royalty_fee'], '%') === false){
			$royaltyFee = $product['RoyaltiesSystemProductsRoyalties'][0]['royalty_fee'];
		} else {
			$royaltyFee = $productsPrice * ($product['RoyaltiesSystemProductsRoyalties'][0]['royalty_fee'] / 100);
		}
		$RoyaltiesSystemRoyaltiesEarnedNew = new RoyaltiesSystemRoyaltiesEarned();
		$RoyaltiesSystemRoyaltiesEarnedNew->content_provider_id = $product['RoyaltiesSystemProductsRoyalties'][0]['content_provider_id'];
		$RoyaltiesSystemRoyaltiesEarnedNew->royalty = $royaltyFee;
		$RoyaltiesSystemRoyaltiesEarnedNew->products_id = $product['products_id'];
		$RoyaltiesSystemRoyaltiesEarnedNew->purchase_type = $product['purchase_type'];
		$RoyaltiesSystemRoyaltiesEarnedNew->orders_id = $order['orders_id'];
		$RoyaltiesSystemRoyaltiesEarnedNew->customers_id = $order['customers_id'];
		$RoyaltiesSystemRoyaltiesEarnedNew->date_added = $order['date_purchased'];
		$RoyaltiesSystemRoyaltiesEarnedNew->save();
	}
}

$rentals = false;
$RoyaltiesSystemRoyaltiesEarnedRented = Doctrine_Query::create()
		->from('RoyaltiesSystemRoyaltiesEarned')
		->addWhere('rented_products_id > 0')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
foreach($RoyaltiesSystemRoyaltiesEarnedRented as $rentalsExisting){
	$rentals[] = $rentalsExisting['rented_products_id'];
}
$rentedProducts = Doctrine_Query::create()
		->from('RentedProducts rp')
		->leftJoin('rp.RoyaltiesSystemProductsRoyalties pr')
		->where('pr.purchase_type = ?', 'rental')
		->andWhere('pr.content_provider_id > ?', 0)
		->andWhere('rp.products_id = pr.products_id')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
foreach($rentedProducts as $product){
	if(($rentals && in_array($product['rented_products_id'], $rentals)) || $product['RoyaltiesSystemProductsRoyalties'][0]['content_provider_id']<=0)
		continue;
	$productsPrice = $product['products_price_rental'];
	if(strpos($product['RoyaltiesSystemProductsRoyalties'][0]['royalty_fee'], '%') === false){
		$royaltyFee = $product['RoyaltiesSystemProductsRoyalties'][0]['royalty_fee'];
	} else {
		$royaltyFee = $productsPrice * ($product['RoyaltiesSystemProductsRoyalties'][0]['royalty_fee'] / 100);
	}
	$RoyaltiesSystemRoyaltiesEarnedNew = new RoyaltiesSystemRoyaltiesEarned();
	$RoyaltiesSystemRoyaltiesEarnedNew->content_provider_id = $product['RoyaltiesSystemProductsRoyalties'][0]['content_provider_id'];
	$RoyaltiesSystemRoyaltiesEarnedNew->royalty = $royaltyFee;
	$RoyaltiesSystemRoyaltiesEarnedNew->products_id = $product['products_id'];
	$RoyaltiesSystemRoyaltiesEarnedNew->rented_products_id = $product['rented_products_id'];
	$RoyaltiesSystemRoyaltiesEarnedNew->purchase_type = 'rental';
	$RoyaltiesSystemRoyaltiesEarnedNew->customers_id = $product['customers_id'];
	$RoyaltiesSystemRoyaltiesEarnedNew->date_added = $product['date_added'];
	$RoyaltiesSystemRoyaltiesEarnedNew->products_barcode = $product['products_barcode'];
	$RoyaltiesSystemRoyaltiesEarnedNew->save();
}
?>