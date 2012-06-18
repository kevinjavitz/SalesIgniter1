<?php
$pID = $_POST['pID'];

$Qbarcodes = Doctrine_Query::create()
					->select('i.inventory_id, b.barcode_id, b.barcode')
					->from('ProductsInventory i')
					->leftJoin('i.ProductsInventoryBarcodes b')
					->where('i.products_id = ?', $pID)
					->andWhere('i.type = "reservation"')
					->andWhere('i.track_method = "barcode"');

					/* If at some point filters for stores or Inventory centers will be added
					if (isset($storeId)){
						$Qbarcodes->leftJoin('b.ProductsInventoryBarcodesToStores b2s')
						->andWhere('b2s.inventory_store_id = ?', $storeId);
					}
					if (isset($inventoryCenterId)){
						$Qbarcodes->leftJoin('b.ProductsInventoryBarcodesToInventoryCenters b2c')
						->andWhere('b2c.inventory_center_id = ?', $inventoryCenterId);
					}*/

$Result = $Qbarcodes->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
$html = '<option value="'."0".'">'."Select Barcode".'</option>';

	foreach ($Result as $pr){		
		foreach($pr['ProductsInventoryBarcodes'] as $bar){
			$html.='<option value="'.$bar['barcode_id'].'">'.$bar['barcode'].'</option>';
		}
	}


EventManager::attachActionResponse(array(
'success' => true,
'data'     => $html
), 'json');


?>