<?php

    $term = $_GET['term'];
	$id = $_POST['mId'];
    $purchaseType = $_POST['purchaseType'];
    $barcodeQty = $_POST['barcodeQty'];
    $productId = $_POST['pID'];

    //$_POST['aID_string'] = attributesUtil::getAttributeString($attribute)

	$OrderedProduct = $Editor->ProductManager->get((int)$id);
	$OrderedProduct->setPurchaseType($purchaseType);
    $product = new Product($OrderedProduct);
    $purchaseTypeClass = $product->getPurchaseType($purchaseType);

    $already = array();
    for($i=0;$i<$barcodeQty;$i++){
        $already[$i] = $_POST['barcode'.$i];
    }

    $QBarcodes = Doctrine_Query::create()
        ->from('ProductsInventoryBarcodes b')
        ->leftJoin('b.ProductsInventory pi')
        ->leftJoin('b.ProductsInventoryBarcodesToStores pis')
        ->andWhere('pi.products_id = ?', $productId)
        ->andWhere('pi.type = ?', $purchaseType)
        ->andWhere('FIND_IN_SET(pis.inventory_store_id,"'.implode(',',Session::get('admin_showing_stores')).'") > 0 OR pis.inventory_store_id is null' );

    $QBarcodes = $QBarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    $jsonData = array();

    $start = date('m/d/Y H:i:s');

	foreach($QBarcodes as $barcode){
            $QBarcode = Doctrine_Query::create()
                ->from('ProductsInventoryBarcodes')
                ->where('barcode_id = ?', $barcode['barcode_id'])
                ->andWhere('status = ?', 'A')
                ->andWhereNotIn('barcode_id ', $already)
                ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

                if($QBarcode){
                    $jsonData[] = array(
                        'value' => $barcode['barcode_id'],
                        'label' => $QBarcode[0]['barcode'],
                        'date' => $start
                    );
                }

	}



	EventManager::attachActionResponse($jsonData, 'json');
?>