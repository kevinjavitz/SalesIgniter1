<?php
	$response = array('success' => false);

	$Qinventory = Doctrine_Query::create()
	->select('inventory_id')
	->from('ProductsInventory')
	->where('controller = ?', 'attribute')
	->andWhere('type = ?', $_GET['purchaseType'])
	//->andWhere('track_method = ?', $_GET['track_method'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qinventory){
		$extAttributes = $appExtension->getExtension('attributes');
		$attributePermutations = $extAttributes->permutateAttributesFromString($_GET['aID_string']);
		if ($_GET['trackMethod'] == 'barcode'){
			$Qcheck = Doctrine_Query::create()
			->select('inventory_id')
			->from('ProductsInventoryBarcodes')
			->where('inventory_id = ?', $Qinventory[0]['inventory_id'])
			->andWhereIn('attributes', $attributePermutations)
			->andWhereIn('status', array('O', 'P'))
			->execute();
			if ($Qcheck->count() > 0){
				$response = array(
					'success' => true,
					'errorMsg' => $Qcheck->count() . ' Barcodes have been purchased or are currenly out, cannot delete them.'
				);
			}else{
				$response = array('success' => true);
			}
			
			Doctrine_Query::create()
			->delete('ProductsInventoryBarcodes')
			->where('inventory_id = ?', $Qinventory[0]['inventory_id'])
			->andWhereIn('attributes', $attributePermutations)
			->andWhereNotIn('status', array('O', 'P'))
			->execute();
		}elseif ($_GET['trackMethod'] == 'quantity'){
			$Qcheck = Doctrine_Query::create()
			->select('inventory_id')
			->from('ProductsInventoryQuantity')
			->where('inventory_id = ?', $Qinventory[0]['inventory_id'])
			->andWhereIn('attributes', $attributePermutations)
			->andWhere('(purchased > 0 OR reserved > 0 OR qty_out > 0)')
			->execute();
			if ($Qcheck->count() > 0){
				$response = array(
					'success' => true,
					'errorMsg' => 'Quantity is either purchased, reserved or currently out, cannot delete.'
				);
			}else{
				Doctrine_Query::create()
				->delete('ProductsInventoryQuantity')
				->where('inventory_id = ?', $Qinventory[0]['inventory_id'])
				->andWhereIn('attributes', $attributePermutations)
				->execute();
				$response = array('success' => true);
			}
		}
	}
	EventManager::attachActionResponse($response, 'json');
?>