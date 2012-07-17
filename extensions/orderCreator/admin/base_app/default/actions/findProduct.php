<?php
	$jsonData = array();

	$QProductsInventoryBarcodes = Doctrine_Query::create()
	->from('Products p')
	->leftJoin('p.ProductsInventory pi')
    ->leftJoin('pi.ProductsInventoryBarcodes pib')
    ->leftJoin('p.ProductsPayPerRental ppr')
    ->where('pib.barcode = ?', $_GET['term']);

    EventManager::notify('AdminOrdersListingBeforeExecuteReportConsumptionBarcodes', $QProductsInventoryBarcodes);

$QProductsInventoryBarcodes = $QProductsInventoryBarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$QproductName = Doctrine_Query::create()
	->from('ProductsDescription pd')
    ->leftJoin('pd.Products p');

    EventManager::notify('AdminProductsToStores', $QproductName);

$prtype = 'none';
    if($QProductsInventoryBarcodes){
        $barcode = $_GET['term'];
        $QproductName->andWhere('pd.products_id =?', $QProductsInventoryBarcodes[0]['products_id']);
	    $prtype = $QProductsInventoryBarcodes[0]['ProductsInventory'][0]['type'];
        $barcode_id = $QProductsInventoryBarcodes[0]['ProductsInventory'][0]['ProductsInventoryBarcodes'][0]['barcode_id'];
        $consumption = $QProductsInventoryBarcodes[0]['ProductsPayPerRental']['consumption'];
    }else{
		$QproductName->andWhere('pd.products_name LIKE ?', $_GET['term'] . '%')
		->andWhere('pd.language_id = ?', Session::get('languages_id'));
    }
    $start = date('m/d/Y H:i:s');

	$QproductName = $QproductName->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($QproductName){
		foreach($QproductName as $pInfo){
			$jsonData[] = array(
				'value' => $pInfo['products_id'],
				'label' => $pInfo['products_name'],
				'prtype' => $prtype,
                'barcode' => $barcode,
                'bar_id' => $barcode_id,
                'date' => $start,
                'consumption' => $consumption
			);
		}
	}
	
	EventManager::attachActionResponse($jsonData, 'json');
?>