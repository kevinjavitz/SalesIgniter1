<?php
class productInventoryAttribute_quantity {
	public function __construct($invData, $aID = false){
		global $appExtension;
		$this->aID_string = null;
		if($aID !== false){
			$this->aID_string = attributesUtil::getAttributeString($aID);
		}
		if (isset($_POST['id'][$invData['type']])){
			$this->aID_string = attributesUtil::getAttributeString($_POST['id'][$invData['type']]);
		}
		$this->invCentersInstalled = false;
		$this->invData = $invData;
		
		$extPayPerRentals = $appExtension->getExtension('payPerRentals');
		if ($extPayPerRentals && $extPayPerRentals->isInstalled() === true){
			$this->payPerRentalsInstalled = true;
		}
	}

	public function hasInventory(){
		global $appExtension;
		$Qcheck = Doctrine_Query::create()
		->select('sum(available) as total')
		->from('ProductsInventoryQuantity')
		->where('inventory_id = ?', $this->invData['inventory_id']);
		
		EventManager::notify('ProductInventoryQuantityHasInventoryQueryBeforeExecute', $this->invData, &$Qcheck);
		
		if (is_null($this->aID_string) === false){
			$attributePermutations = attributesUtil::permutateAttributesFromString($this->aID_string);
			$Qcheck->andWhereIn('attributes', $attributePermutations);
		}
		$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		return ($Result[0]['total'] > 0);
	}

	public function getInventoryItemCount(){
		$count = 0;
		$today = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
		$plusFive = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d')+5, date('Y')));
		$invItems = $this->getInventoryItems();
		foreach($invItems as $invItem){
			if ($invItem['available'] <= 0) continue;

			$addTotal = true;
			EventManager::notify('ProductInventoryQuantityGetItemCount', &$this->invData, &$invItem, &$addTotal);

			if ($addTotal === true){
				$count += $invItem['available'];
			}
		}
		return $count;
	}

	public function updateStock($orderId, $orderProductId, $cartProduct){
		$aID_string = attributesUtil::getAttributeString($cartProduct->getInfo('attributes'));
		$attributePermutations = attributesUtil::permutateAttributesFromString($aID_string);
		
		$Qcheck = Doctrine_Query::create()
		->select('quantity_id')
		->from('ProductsInventoryQuantity')
		->where('inventory_id = ?', $this->invData['inventory_id'])
		->andWhereIn('attributes', $attributePermutations);
		
		EventManager::notify('ProductInventoryQuantityUpdateStockQueryBeforeExecute', $this->invData, &$Qcheck);

		$Record = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Record){
			$Stock = Doctrine_Core::getTable('ProductsInventoryQuantity')->find($Record[0]['quantity_id']);
			$Stock->available -= $cartProduct->getQuantity();
		
			/* @TODO: Get in the pay per rental extension */
			if ($this->payPerRentalsInstalled === true && $cartProduct->getPurchaseType() == 'reservation'){
				$Stock->reserved += $cartProduct->getQuantity();
			}else{
				$Stock->purchased += $cartProduct->getQuantity();
			}
			$Stock->save();
		}
	}

	public function addStockToCollection(&$Product, &$CollectionObj){
		global $Editor;
		$Qcheck = Doctrine_Query::create()
		->select('quantity_id')
		->from('ProductsInventoryQuantity')
		->where('inventory_id = ?', $this->invData['inventory_id']);
		if (is_null($this->aID_string) === false){
			$attributePermutations = attributesUtil::permutateAttributesFromString($this->aID_string);
			$Qcheck->andWhereIn('attributes', $attributePermutations);
		}
		EventManager::notify('ProductInventoryQuantityUpdateStockQueryBeforeExecute', $this->invData, &$Qcheck);

		$Record = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Record){
			$CollectionObj->quantity_id = $Record[0]['quantity_id'];
			$CollectionObj->ProductsInventoryQuantity->available -= $Product->getQuantity();

			/* @TODO: Get in the pay per rental extension */
			if ($this->payPerRentalsInstalled === true && $Product->getPurchaseType() == 'reservation'){
				$CollectionObj->ProductsInventoryQuantity->reserved += $Product->getQuantity();
			}else{
				$CollectionObj->ProductsInventoryQuantity->purchased += $Product->getQuantity();
			}
		}else{
			$Editor->addErrorMessage('There is no inventory for the estimate. Please reselect.');
		}
	}

	public function getInventoryItems(){
		global $appExtension;
		$qty = array();
		$Qcheck = Doctrine_Query::create()
		->from('ProductsInventoryQuantity')
		->where('inventory_id = ?', $this->invData['inventory_id']);

		EventManager::notify('ProductInventoryQuantityGetInventoryItemsQueryBeforeExecute', $this->invData, &$Qcheck);
		
		if (is_null($this->aID_string) === false){
			$attributePermutations = attributesUtil::permutateAttributesFromString($this->aID_string);
			$Qcheck->andWhereIn('attributes', $attributePermutations);
		}
		$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Result){
			foreach($Result as $qInfo){
				/* @TODO: Get in the pay per rental extension */
				$qty[] = array(
					'id'           => $qInfo['quantity_id'],
					'inventory_id' => $qInfo['inventory_id'],
					'available'    => $qInfo['available'],
					'reserved'     => $qInfo['reserved'],
					'center_id'    => $qInfo['inventory_center_id']
				);
			}
		}
		return $qty;
	}
}
?>