<?php
	class productInventory {
		
		function __construct($pId, $purchaseType, $controller, $allowOverbooking = false, $aID = false){
			$Qcheck = Doctrine_Query::create()
			->from('ProductsInventory')
			->where('products_id = ?', $pId)
			->andWhere('type = ?', $purchaseType)
			->andWhere('controller = ?', $controller);

			EventManager::notify('ProductInventoryQueryBeforeExecute', &$Qcheck);

			$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$this->invData = null;
			$this->invMethod = null;
			$this->allowOverbooking = $allowOverbooking || (sysConfig::get('CHECK_STOCK_NEW_USED') == 'false');

			if (count($Result) > 0){
				$this->invData = $Result[count($Result)-1];
				$invController = $this->invData['controller'];
				$controllerDir = sysConfig::getDirFsCatalog() . 'includes/classes/product/inventory/controller/';

				if (file_exists($controllerDir . $invController . '.php')){
					$className = 'productInventory_' . $invController;
					if (!class_exists($className)){
						require($controllerDir . $invController . '.php');
					}
					$this->invMethod = new $className($this->invData, $aID);

					EventManager::notify(
						'ProductInventorySetMethod',
						&$this->invData,
						&$this->invMethod,
						$invController
					);
				}
			}
		}

		function getTrackMethod(){
			if (is_null($this->invData) === false){
				return $this->invData['track_method'];
			}
			return false;
		}

		function getControllerName(){
			if (is_null($this->invData) === false){
				return $this->invData['controller'];
			}
			return false;
		}

		function getPurchaseType(){
			if (is_null($this->invData) === false){
				return $this->invData['type'];
			}
			return false;
		}

		function getController(){
			if (is_null($this->invMethod) === false){
				return $this->invMethod;
			}
			return false;
		}

		function hasInventory(){
			if($this->allowOverbooking){
				return true;
			}
			if (is_null($this->invMethod) === false){
				return ($this->invMethod->hasInventory() > 0);
			}
			return false;
		}

		function getCurrentStock(){
			if (is_null($this->invMethod) === false){
				return $this->invMethod->getTotalInventory();
			}
			return false;
		}

		function getInventoryItems(){
			if (is_null($this->invMethod) === false){
				return $this->invMethod->getInventoryItems();
			}
			return false;
		}

		function updateStock($orderId, $orderProductId, &$cartProduct){
			if (is_null($this->invMethod) === false){
				return $this->invMethod->updateStock($orderId, $orderProductId, &$cartProduct);
			}
			return false;
		}
		
		public function addStockToCollection(&$ProductObj, &$CollectionObj){
			if (is_null($this->invMethod) === false){
				return $this->invMethod->addStockToCollection($ProductObj, $CollectionObj);
			}
			return false;
		}
	}
?>