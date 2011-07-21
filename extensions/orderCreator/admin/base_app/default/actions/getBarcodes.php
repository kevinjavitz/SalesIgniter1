<?php

    $term = $_GET['term'];
	$id = $_POST['mId'];
    $purchaseType = $_POST['purchaseType'];
    //$_POST['aID_string'] = attributesUtil::getAttributeString($attribute)

	$OrderedProduct = $Editor->ProductManager->get((int)$id);
	$OrderedProduct->setPurchaseType($purchaseType);

    $jsonData = array();
	foreach($OrderedProduct->getProductsBarcode() as $barcode){
		$QBarcode = Doctrine_Query::create()
		->from('ProductsInventoryBarcodes')
		->where('barcode_id = ?', $barcode['id'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$jsonData[] = array(
			'value' => $barcode['id'],
			'label' => $QBarcode[0]['barcode']
		);
	}



	EventManager::attachActionResponse($jsonData, 'json');
?>