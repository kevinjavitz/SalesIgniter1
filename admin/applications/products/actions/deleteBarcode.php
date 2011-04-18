<?php
	$Barcode = Doctrine_Core::getTable('ProductsInventoryBarcodes')->findOneByBarcodeId((int)$_GET['bID']);
	if ($Barcode){
		if ($Barcode->status == 'O'){
			$response = array(
				'success' => true,
				'errorMsg' => 'That barcode is currently rented out, please return it before deleting.'
			);
		}elseif ($Barcode->status == 'P'){
			$response = array(
				'success' => true,
				'errorMsg' => 'That barcode has been purchased, it cannot be deleted.'
			);
		}else{
			$Barcode->delete();
			$response = array('success' => true);
		}
	}else{
		$response = array('success' => false);
	}
	EventManager::attachActionResponse($response, 'json');
?>