<?php
	$QProducts = Doctrine_Query::create()
    ->from('OrdersProductsReservations opr')
	->leftJoin('opr.ordersProducts op')
	->where('op.orders_products_id is null')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    print_r($QProducts);
	foreach($QProducts as $product){

	}
?>
<h1>Extra Reservations Deleted</h1>