<?php
	$jsonData = array();

	$QProductsInventoryBarcodes = Doctrine_Query::create()
	->from('Products p')
	->leftJoin('p.ProductsInventory pi')
	->leftJoin('pi.ProductsInventoryBarcodes pib');
	if($appExtension->isInstalled('multiStore') && $appExtension->isEnabled('multiStore')){
    	$QProductsInventoryBarcodes->leftJoin('pib.ProductsInventoryBarcodesToStores pis');
	}
	$QProductsInventoryBarcodes->leftJoin('p.ProductsPayPerRental ppr')
	->where('pib.barcode = ?', $_GET['term']);
	if($appExtension->isInstalled('multiStore') && $appExtension->isEnabled('multiStore')){
    	$QProductsInventoryBarcodes->andWhere('FIND_IN_SET(pis.inventory_store_id,"'.implode(',',Session::get('admin_showing_stores')).'") > 0 OR pis.inventory_store_id is null' );
	}

	$QProductsInventoryBarcodes = $QProductsInventoryBarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$QproductName = Doctrine_Query::create()
	->from('ProductsDescription pd')
    ->leftJoin('pd.Products p');
	if($appExtension->isInstalled('multiStore') && $appExtension->isEnabled('multiStore')){
		$QproductName->leftJoin('p.ProductsToStores pts')
		->where('FIND_IN_SET(pts.stores_id,"'.implode(',',Session::get('admin_showing_stores')).'") > 0 OR pts.stores_id is null' );
	}

    $prtype = 'none';
    if(count($QProductsInventoryBarcodes) > 0){
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
			if($prtype == 'none'){
				$prArr = explode(',', $pInfo['Products']['products_type']);
				foreach($prArr as $iPr)
				$jsonData[] = array(
					'value' => $pInfo['products_name'],
					'label' => $pInfo['products_name'] . ' - '.ucfirst($iPr),
					'prtype' => $iPr,
					'pr_id' => $pInfo['products_id'],
					'barcode' => $barcode,
					'bar_id' => $barcode_id,
					'date' => $start,
					'consumption' => $consumption
				);
			} else{
				$jsonData[] = array(
					'value' => $pInfo['products_name'],
					'label' => $pInfo['products_name'],
					'prtype' => $prtype,
					'pr_id' => $pInfo['products_id'],
					'barcode' => $barcode,
					'bar_id' => $barcode_id,
					'date' => $start,
					'consumption' => $consumption
				);
			}
		}
	}
	
	EventManager::attachActionResponse($jsonData, 'json');
?>