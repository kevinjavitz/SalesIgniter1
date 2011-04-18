<?php
	if (isset($_GET['invStore'])){
		$invStore = (int)$_GET['invStore'];
		$Qcheck = Doctrine_Query::create()
		->select('inventory_store_id')
		->from('ProductsInventoryBarcodesToStores')
		->where('barcode_id = ?', $barcodeID)
		->execute();
		if ($Qcheck->count() > 0){
			if ($Qcheck[0]['inventory_store_id'] != $invStore){
				if ($invStore == ''){
					$invStore = $Qcheck[0]['inventory_store_id'];
					Doctrine_Query::create()
					->delete('ProductsInventoryBarcodesToStores')
					->where('barcode_id = ?', $barcodeID)
					->andWhere('inventory_store_id = ?', $invStore)
					->execute();
				}else{
					Doctrine_Query::create()
					->update('ProductsInventoryBarcodesToStores')
					->set('inventory_store_id', '?', $invStore)
					->where('barcode_id = ?', $barcodeID)
					->execute();
				}
			}
		}else{
			if ($invStore != ''){
				$newCenter = new ProductsInventoryBarcodesToStores();
				$newCenter->barcode_id = $barcodeID;
				$newCenter->inventory_store_id = $invStore;
				$newCenter->save();
			}
		}
	}
?>