<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class ShoppingCartDatabaseActions {
		
		public function __construct(){
		}
		
		public function runAction($action, ShoppingCartProduct $cartProduct){

			$userAccount =& Session::getReference('userAccount');
			if ($userAccount->isLoggedIn() === true){
				$pID_string = $cartProduct->getIdString();
				$purchaseType = $cartProduct->getPurchaseType();
				$pInfo = $cartProduct->getInfo();
				switch($action){
					case 'delete':
						$this->deleteFromDatabase(array('customers_id'=> $userAccount->getCustomerId(), 'products_id' => $pID_string, 'purchase_type' => $purchaseType));
						break;
					case 'update':
					case 'insert':
						if(!isset($pInfo['is_queue'])){
							$this->insertBasket($pID_string, $pInfo, $action);
						}
						break;
				}
			}
		}
		
		function insertBasket($pID_string, $pInfo, $action = null){
			$userAccount =& Session::getReference('userAccount');

				$insert = Doctrine_Query::create()
				->select('customers_basket_id')
				->from('CustomersBasket')
				->where('customers_id = ?', $userAccount->getCustomerId())
				->andWhere('products_id = ?', $pID_string)
				->andWhere('purchase_type = ?', $pInfo['purchase_type'])
				->fetchOne();
				if ($insert === false){
					$action = 'insert';
				}else{
					$action = 'update';
				}


			if ($action == 'insert'){
				$insert = new CustomersBasket;
				$insert->customers_id = $userAccount->getCustomerId();
				$insert->products_id = $pID_string;
				$insert->purchase_type = $pInfo['purchase_type'];
			}
			/*move into extension*/
			if(Session::exists('current_store_id')){
				$insert->stores_id = Session::get('current_store_id');
			}
			$insert->customers_basket_quantity = (int)$pInfo['quantity'];

			EventManager::notify('ShoppingCartDatabase\InsertBasketBeforeProcess', &$insert, &$pInfo);

			$insert->save();

			EventManager::notify('ShoppingCartDatabase\InsertBasketAfterProcess', &$insert);
		}
		
		function deleteFromDatabase($settings){
			$userAccount =& Session::getReference('userAccount');
	    	$Qdelete = Doctrine_Query::create()
			->delete('CustomersBasket c')
			->where('c.customers_id = ?', $settings['customers_id']);

			if (is_null($settings['products_id']) === false){
				$Qdelete->andWhere('c.products_id = ?', $settings['products_id']);
			}

			if (is_null($settings['purchase_type']) === false){
				$Qdelete->andWhere('c.purchase_type = ?', $settings['purchase_type']);
			}
			if(Session::exists('current_store_id')){
				$Qdelete->andWhere('c.stores_id = ?', Session::get('current_store_id'));
			}

			EventManager::notify('ShoppingCartDatabase\DeleteBasketBeforeProcess', &$Qdelete);

			$Qdelete->execute();

			EventManager::notify('ShoppingCartDatabase\DeleteBasketAfterProcess');
		}
		
		public function getCartFromDatabase(){
			$userAccount =& Session::getReference('userAccount');
			$Qproduct = Doctrine_Query::create()
			->from('CustomersBasket c')
			->where('c.customers_id = ?', $userAccount->getCustomerId());
			if(Session::exists('current_store_id')){
				$Qproduct->andWhere('stores_id = ?', Session::get('current_store_id'));
			}
			EventManager::notify('ShoppingCartDatabase\GetCartFromDatabaseBeforeExecute');

			$products = $Qproduct->execute();
			$contents = array();
			if ($products){
				foreach($products->toArray() as $product){
					$basketId = $product['customers_basket_id'];
					$pID_string = $product['products_id'];
					$purchaseType = $product['purchase_type'];
					$quantity = $product['customers_basket_quantity'];

					$pInfo = array(
						'id_string'     => $pID_string,
						'uniqID' => uniqid(),
						'quantity'      => $quantity,
						'purchase_type' => $purchaseType
					);

					EventManager::notify('ShoppingCartDatabase\GetCartFromDatabase',
						&$pInfo,
						$basketId,
						$product
					);
					
					$contents[] = new ShoppingCartProduct($pInfo);
				}
			}
			return $contents;
		}
	}
?>