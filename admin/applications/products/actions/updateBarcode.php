<?php
	$barcodeID = (int)$_GET['barcode_id'];
    $supplier_id = $_GET['suppliersId'];

    $Qupdate = Doctrine_Query::create()
        ->update('ProductsInventoryBarcodes')
        ->set('suppliers_id', $supplier_id)
        ->where('barcode_id = ?', $barcodeID);

    $Qupdate->execute();

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>