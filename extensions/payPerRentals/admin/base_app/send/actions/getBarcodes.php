<?php
    $term = $_POST['term'];

	$Qres = Doctrine_Query::create()
	->from('OrdersProductsReservation')
	->where('orders_products_reservations_id = ?', $_POST['resid'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$Qprods = Doctrine_Query::create()
	->from('OrdersProducts')
	->where('orders_products_id =?', $Qres[0]['orders_products_id'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


	$product = new product($Qprods[0]['products_id']);

	$purchaseTypeClass = $product->getPurchaseType('reservation');

	$jsonData = array();
	foreach($purchaseTypeClass->getProductsBarcodes() as $barcode){
		$QBarcode = Doctrine_Query::create()
			->from('ProductsInventoryBarcodes')
			->where('barcode_id = ?', $barcode['id'])
			->andWhere('barcode LIKE ?', $term.'%')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if($QBarcode){
			$jsonData[] = array(
				'value1' => $barcode['id'],
				'value' => $QBarcode[0]['barcode'],
				'label' => $QBarcode[0]['barcode']
			);
		}
	}
	EventManager::attachActionResponse($jsonData, 'json');
?>