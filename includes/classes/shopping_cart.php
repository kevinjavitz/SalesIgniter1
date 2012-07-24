<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	require(dirname(__FILE__) . '/shoppingCart/Database.php');
	require(dirname(__FILE__) . '/shoppingCart/Contents.php');
	require(dirname(__FILE__) . '/shoppingCart/Product.php');
	
	class ShoppingCart {
		
		public function __construct(){
			$this->emptyCart();
		}
		
		public function initContents(){
			$this->dbUtil = new ShoppingCartDatabaseActions;
			foreach($this->contents as $cartProduct){
				$cartProduct->init();
			}
		}
		public function loadQueueContents(){
			EventManager::notify('ShoppingCart\OnConstruct');
		}
		
		private function _notify($pID){
			Session::set('new_products_id_in_cart', $pID);
		}
		
		public function generateCartId($length = 5) {
			return tep_create_random_value($length, 'digits');
		}

		public function emptyCart($reset_database = false){
			$this->contents = new ShoppingCartContents();
			$this->total = 0;
			$this->weight = 0;
			$this->content_type = false;
			
			$userAccount =& Session::getReference('userAccount');
			if ($userAccount && $userAccount->isLoggedIn() === true && ($reset_database == true)){
				$this->dbUtil->deleteFromDatabase(array('customers_id' => $userAccount->getCustomerId(), 'products_id' => null, 'purchase_type' => null));
			}
			unset($this->cartID);
			if (Session::exists('cartID') === true) Session::remove('cartID');
		}
		
		public function getProductIdList(){
			$product_id_list = '';

			$this->contents->rewind();
			foreach($this->contents as $cartProduct){
				if($cartProduct->hasInfo('is_queue') == false){
				$product_id_list .= ', ' . $cartProduct->getIdString();
				}
			}

			return substr($product_id_list, 2);
		}
		
		private function _productIsEnabled($pID){
			$Qcheck = Doctrine_Query::create()
			->select('products_status')
			->from('Products')
			->where('products_id = ?', $pID)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck){
				return ($Qcheck[0]['products_status'] == '1');
			}
			return false;
		}
		
		private function _allowAction($products_id, $qty, $purchaseType){
			$return = EventManager::notifyWithReturn('ShoppingCart\AddToCartAllow', $products_id, $qty, $purchaseType);
			foreach($return as $Result){
				if ($Result === false){
					return false;
				}
			}
		
			if (is_numeric($products_id) && is_numeric($qty) &&	$this->_productIsEnabled((int)$products_id)){
				return true;
			}
			return false;
		}
		
		public function addProduct($pID, $purchaseType = 'new', $qty = 0, $pInfo = null, $isQueue = false, $alreadyInQueue = false){
			global $messageStack;
			$pID_strings = array(array(
				'id'           => $pID,
				'purchaseType' => $purchaseType,
				'qty'          => $qty
			));
			
			EventManager::notify('ShoppingCart\AddToCartPrepare', &$pID_strings);

			if ($this->_allowAction($pID, $qty, $purchaseType)){
				foreach($pID_strings as $pID_info){
					$qty = $pID_info['qty'];
					if ($qty > 0){
						$curQty = 0;


						$cartProduct = $this->contents->findProduct($pID_info['id'], $purchaseType);

						if ($cartProduct){
							$curQty = $cartProduct->getQuantity();
						}
			
						if ($qty > 0){
							$curQty += (int)$qty;
						}elseif ($qty == 0){
							$curQty += 1;
						}

				        if($pInfo == null){
							$pInfo = array(
								'id_string'     => $pID,
								'uniqID'    => uniqid(),
								'purchase_type' => $purchaseType,
								'quantity'      => $curQty
							);
				        }
				
						EventManager::notify('ShoppingCart\AddToCartBeforeAction', $pID_info, &$pInfo, &$cartProduct);
			
						$this->_notify((int)$pInfo['id_string']);

						if ($cartProduct && $alreadyInQueue == false){
							$cartProduct->updateInfo($pInfo);
						}else{
							if($isQueue){
								$pInfo['is_queue'] = true;
								$pInfo['already_queue'] = $alreadyInQueue;
							}
							$this->contents->add(new ShoppingCartProduct($pInfo));
						}
						EventManager::notify('ShoppingCart\AddToCartAfterAction', $pID_info, &$pInfo, &$cartProduct); 
					}
				}
				
				$this->cartID = $this->generateCartId();
			}
		}
		
		function updateProduct($pID_string, $pInfo){
			$cartProduct = $this->contents->findProductByUniqID($pID_string, $pInfo['purchase_type']);
			if(is_object($cartProduct)){
				EventManager::notify('ShoppingCart\UpdateProductPrepare', $cartProduct->getIdString(), $pInfo['purchase_type']);

				$new_pInfo = $cartProduct->getInfo();
				foreach($pInfo as $key => $val){
					$new_pInfo[$key] = $val;
				}
				$pID_string = array(
					'id' => $cartProduct->getIdString(),
					'purchaseType' => $pInfo['purchase_type']
				);

				EventManager::notify('ShoppingCart\UpdateProductBeforeAction', $pID_string, &$new_pInfo);
				$cartProduct->updateInfo($new_pInfo);
				$this->contents->add($cartProduct);

				EventManager::notify('ShoppingCart\UpdateProductAfterAction', $pID_string, &$new_pInfo);
			}
		}
		
		function removeProduct($pID_string, $purchaseType = false){
			$this->contents->remove($pID_string, $purchaseType);

			// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
			$this->cartID = $this->generateCartId();
		}
		
		public function getProducts(){
			$contArr = array();
			foreach($this->contents as $cartProduct){
				if($cartProduct->hasInfo('is_queue') == false){
					$contArr[] = $cartProduct;
				}
			}
			return $contArr;
		}
		public function getProductsQueue(){
			$contArr = array();
			foreach($this->contents as $cartProduct){
				if($cartProduct->hasInfo('is_queue') == true){
					$contArr[] = $cartProduct;
				}
			}
			return $contArr;
		}

		public function calculate() {
			$this->total = 0;
			$this->total_virtual = 0;
			$this->weight = 0;
			$this->weight_virtual = 0;

			if ($this->contents->count() <= 0) return 0;

			// @TODO: Get into pay per rental extension
			$noWeightPurchaseTypes = array(
				'download',
				'stream'
			);

			$this->contents->rewind();
			foreach($this->contents as $cartProduct){
				$qty = $cartProduct->getQuantity();

				$no_count = 1;
				if (preg_match('/^GIFT/', $cartProduct->productClass->getModel())) {
					$no_count = 0;
				}

				$products_tax = $cartProduct->productClass->getTaxRate();
				$products_price = $cartProduct->getFinalPrice();
				$products_weight = $cartProduct->productClass->getWeight();

				if (in_array($cartProduct->getPurchaseType(), $noWeightPurchaseTypes) === false){
					$this->weight_virtual += ($qty * $products_weight) * $no_count;// ICW CREDIT CLASS;
					$this->weight += ($qty * $products_weight);
				}
				$this->total_virtual += tep_add_tax($products_price, $products_tax) * $qty * $no_count;// ICW CREDIT CLASS;
				$this->total += tep_add_tax($products_price, $products_tax) * $qty;
			}
		}
		
		public function countContents() {
			$totalItems = 0;
			
			$this->contents->rewind();
			foreach($this->contents as $cartProduct){
				if($cartProduct->hasInfo('is_queue') == false){
				$totalItems += $cartProduct->getQuantity();
				}
			}

			EventManager::notify('ShoppingCart\CountContents', &$totalItems);
			return $totalItems;
		}
		public function countContentsQueue() {
			$totalItems = 0;
			$this->contents->rewind();
			foreach($this->contents as $cartProduct){
				if($cartProduct->hasInfo('is_queue') == true){
					$totalItems += $cartProduct->getQuantity();
				}
			}
			EventManager::notify('ShoppingCart\CountContentsQueue', &$totalItems);
			return $totalItems;
		}
		
		public function getQuantity($pID_string, $purchaseType){
			$cartProduct = $this->contents->find($pID_string, $purchaseType);
			if ($cartProduct){
				return $cartProduct->getQuantity();
			} else {
				return 0;
			}
		}
		
		public function inCart($pID_string, $purchaseType = 'new') {
			$cartProduct = $this->contents->find($pID_string, $purchaseType);
			if ($cartProduct){
				return true;
			}
			return false;
		}
		
		public function getProduct($pID_string, $purchaseType = 'new'){
			$cartProduct = $this->contents->find($pID_string, $purchaseType);
			if ($cartProduct){
				return $cartProduct;
			}
			return null;
		}
		
		public function restoreContents(){
			$contents = $this->dbUtil->getCartFromDatabase();
			foreach($contents as $cartProduct){
				//$this->addProduct($cartProduct->getIdString(),$cartProduct->getPurchaseType(),$cartProduct->getQuantity(), $cartProduct->getInfo());
				$this->contents->add(new ShoppingCartProduct($cartProduct->getInfo()));
			}
		}
		
		public function showTotal() {
			$this->calculate();
			return $this->total;
		}

		public function showWeight() {
			$this->calculate();
			return $this->weight;
		}
		
		public function getContentType() {
			$this->content_type = false;
			if ($this->countContents() > 0){
				if ($this->showWeight() == 0) {
					$this->contents->rewind();
					foreach($this->contents as $cartProduct){
						// @TODO: Get into pay per rental extension
						if ($cartProduct->getWeight() == 0 || $cartProduct->getPurchaseType() == 'reservation'){
							switch ($this->content_type) {
								case 'physical':
									$this->content_type = 'mixed';
									return $this->content_type;
									break;
								default:
									$this->content_type = 'virtual';
									break;
							}
						}else{
							switch ($this->content_type) {
								case 'virtual':
									$this->content_type = 'mixed';
									return $this->content_type;
									break;
								default:
									$this->content_type = 'physical';
									break;
							}
						}
					}
				} else {
					switch ($this->content_type) {
						case 'virtual':
							$this->content_type = 'mixed';
							return $this->content_type;
							break;
						default:
							$this->content_type = 'physical';
							break;
					}
				}
			} else {
				$this->content_type = 'physical';
			}
			return $this->content_type;
		}
	}
?>