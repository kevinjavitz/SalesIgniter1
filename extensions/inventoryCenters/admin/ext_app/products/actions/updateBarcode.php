<?php
	if (isset($_GET['invCenter'])){
		$invCenter = (int)$_GET['invCenter'];
		$Qcheck = Doctrine_Query::create()
		->select('inventory_center_id')
		->from('ProductsInventoryBarcodesToInventoryCenters')
		->where('barcode_id = ?', $barcodeID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck){
			if ($Qcheck[0]['inventory_center_id'] != $invCenter){
				if ($invCenter == ''){
					$invCenter = $Qcheck[0]['inventory_center_id'];
					Doctrine_Query::create()
					->delete('ProductsInventoryBarcodesToInventoryCenters')
					->where('barcode_id = ?', $barcodeID)
					->andWhere('inventory_center_id = ?', $invCenter)
					->execute();
				}else{
					Doctrine_Query::create()
					->update('ProductsInventoryBarcodesToInventoryCenters')
					->set('inventory_center_id', '?', $invCenter)
					->where('barcode_id = ?', $barcodeID)
					->execute();
				}
			}
		}else{
			if ($invCenter != ''){
				$newCenter = new ProductsInventoryBarcodesToInventoryCenters();
				$newCenter->barcode_id = $barcodeID;
				$newCenter->inventory_center_id = $invCenter;
				$newCenter->save();
			}
		}
	}
?>