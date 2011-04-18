<?php
	class productInventory_attribute {
		public function __construct($invData){
			$trackerDir = sysConfig::getDirFsCatalog() . 'includes/classes/product/inventory/track_method/attribute/';

			$trackMethod = 'quantity';
			if ($invData['track_method'] == 'barcode'){
				$trackMethod = $invData['track_method'];
			}

			if (file_exists($trackerDir . $trackMethod . '.php')){
				$className = 'productInventoryAttribute_' . $trackMethod;
				if (!class_exists($className)){
					require($trackerDir . $trackMethod . '.php');
				}
				$this->trackMethod = new $className($invData);
			}
		}

		public function setIdString($aID_string){
			$this->trackMethod->aID_string = $aID_string;
		}

		public function hasInventory(){
			return $this->trackMethod->hasInventory();
		}

		public function getTotalInventory(){
			return $this->trackMethod->getInventoryItemCount();
		}

		public function updateStock($orderId, $orderProductId, &$cartProduct){
			return $this->trackMethod->updateStock($orderId, $orderProductId, &$cartProduct);
		}
		
		public function addStockToCollection(&$ProductObj, &$CollectionObj){
			return $this->trackMethod->addStockToCollection($ProductObj, $CollectionObj);
		}

		public function getInventoryItems(){
			return $this->trackMethod->getInventoryItems();
		}
	}
?>