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
		->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
$orders = false;
//var_dump($RoyaltiesSystemRoyaltiesEarnedOrders);
foreach($RoyaltiesSystemRoyaltiesEarnedOrders as $ordersExisting){
	$orders[] = $ordersExisting['orders_id'];
}

$ordersQuery = Doctrine_Query::create()
		->select('o.orders_id, o.customers_id, o.orders_status, o.date_purchased, pr.content_provider_id, pr.royalty_fee, op.products_id, op.purchase_type')
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
	$ordersQuery->andWhere('o.orders_id not in (" ? ")',implode(',',$orders));
}
$Qorders = $ordersQuery->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
foreach($Qorders as $order)	{
	if($orders && in_array($order['orders_id'],$orders))
		continue;

	foreach($order['OrdersProducts'] as $product){
		$RoyaltiesSystemRoyaltiesEarnedNew = new RoyaltiesSystemRoyaltiesEarned();
		$RoyaltiesSystemRoyaltiesEarnedNew->content_provider_id = $product['RoyaltiesSystemProductsRoyalties'][0]['content_provider_id'];
		$RoyaltiesSystemRoyaltiesEarnedNew->royalty = $product['RoyaltiesSystemProductsRoyalties'][0]['royalty_fee'];
		$RoyaltiesSystemRoyaltiesEarnedNew->products_id = $product['products_id'];
		$RoyaltiesSystemRoyaltiesEarnedNew->purchase_type = $product['purchase_type'];
		$RoyaltiesSystemRoyaltiesEarnedNew->orders_id = $order['orders_id'];
		$RoyaltiesSystemRoyaltiesEarnedNew->customers_id = $order['customers_id'];
		$RoyaltiesSystemRoyaltiesEarnedNew->date_added = $order['date_purchased'];
		$RoyaltiesSystemRoyaltiesEarnedNew->save();
	}

}

?>