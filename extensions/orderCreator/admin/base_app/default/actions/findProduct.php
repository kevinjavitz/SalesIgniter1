<?php
	$jsonData = array();

	$QProductsInventoryBarcodes = Doctrine_Query::create()
	->from('Products p')
	->leftJoin('p.ProductsInventory pi')
	->leftJoin('pi.ProductsInventoryBarcodes pib')
	->where('pib.barcode = ?', $_GET['term'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$QproductName = Doctrine_Query::create()
	->from('ProductsDescription');
    $prtype = 'none';
    if(count($QProductsInventoryBarcodes) > 0){
	    $QproductName->where('products_id =?', $QProductsInventoryBarcodes[0]['products_id']);
	    $prtype = $QProductsInventoryBarcodes[0]['ProductsInventory'][0]['type'];
    }else{
		$QproductName->where('products_name LIKE ?', $_GET['term'] . '%')
		->andWhere('language_id = ?', Session::get('languages_id'));
    }
	$QproductName = $QproductName->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($QproductName){
		foreach($QproductName as $pInfo){
			$jsonData[] = array(
				'value' => $pInfo['products_id'],
				'label' => $pInfo['products_name'],
				'prtype' => $prtype
			);
		}
	}
	
	EventManager::attachActionResponse($jsonData, 'json');
?>