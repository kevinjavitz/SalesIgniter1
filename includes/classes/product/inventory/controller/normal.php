<?php
	class productInventory_normal {
		function __construct($invData){
			$trackerDir = sysConfig::getDirFsCatalog() . 'includes/classes/product/inventory/track_method/normal/';

			$trackMethod = 'quantity';
			if ($invData['track_method'] == 'barcode'){
				$trackMethod = $invData['track_method'];
			}

			if (file_exists($trackerDir . $trackMethod . '.php')){
				$className = 'productInventoryNormal_' . $trackMethod;
				if (!class_exists($className)){
					require($trackerDir . $trackMethod . '.php');
				}
				$this->trackMethod = new $className($invData);
			}
		}

		function hasInventory(){
			return $this->trackMethod->hasInventory();
		}

		function getTotalInventory(){
			return $this->trackMethod->getInventoryItemCount();
		}

		function updateStock($orderId, $orderProductId, $cartProduct){
			return $this->trackMethod->updateStock($orderId, $orderProductId, $cartProduct);
		}
		
		public function addStockToCollection(&$ProductObj, &$CollectionObj){
			return $this->trackMethod->addStockToCollection($ProductObj, $CollectionObj);
		}

		function getInventoryItems(){
			return $this->trackMethod->getInventoryItems();
		}
	}
?>