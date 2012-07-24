<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	class ShoppingCartContents implements Countable, Iterator, Serializable {
		private $contents = array();
		private $position;
		private $dbUtil;
		
		public function __construct(){
			$this->position = 0;
			$this->dbUtil = new ShoppingCartDatabaseActions;
		}
		
		public function count(){
			return sizeof($this->contents);
		}
		
		public function rewind(){
			$this->position = 0;
		}
		
		public function current(){
			return $this->contents[$this->position];
		}
		
		public function key(){
			return $this->position;
		}
		
		public function next(){
			++$this->position;
		}
		
		public function valid(){
			return (isset($this->contents[$this->position]));
		}
		
		public function serialize(){
			return serialize($this->contents);
		}
		
		public function unserialize($data){
			$this->contents = unserialize($data);
			$this->dbUtil = new ShoppingCartDatabaseActions;
		}
		
		public function getContents(){
			return $this->contents;
		}
		
		public function remove($pID_string, $purchaseType){
			$key = $this->findKey($pID_string, $purchaseType);
			if ($key !== false){
				$this->removeKey($key);
			}
		}
		
		public function add(ShoppingCartProduct &$cartProduct, $runDbAction = true){
			global $userAccount, $messageStack;
			$pID_string = $cartProduct->getIdString();
			$purchaseType = $cartProduct->getPurchaseType();
			$pInfo = $cartProduct->getInfo();
			
			$check = $this->findProductAsKey($cartProduct, $purchaseType);
			$canAdd = true;
			if ($check !== false){
				$action = 'update';
				$this->contents[$check] =& $cartProduct;
			}else{
				$action = 'insert';
				if(isset($pInfo['is_queue'])){
					if ($userAccount->isLoggedIn() === true){
						if ($userAccount->isRentalMember()){
							if ($userAccount->membershipIsActivated()){
								$membership =& $userAccount->plugins['membership'];
								if($membership->isPastDue()){
									$customerCanRent =  'pastdue';
								}else{
									$customerCanRent = true;
								}
							}else{
								$customerCanRent = 'inactive';
							}
						}else{
							$customerCanRent = 'membership';
						}
						$membership =& $userAccount->plugins['membership'];
						$errorMsg = '';
						if ($customerCanRent !== true){
							switch($customerCanRent){
								case 'membership':
									if (Session::exists('account_action') === true){
										Session::remove('account_action');
									}
									$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default','SSL'), itw_app_link(null,'account','login'));
									break;
								case 'inactive':
									$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_ACTIVE_CUSTOMER'), ($membership->isPastDue()?itw_app_link((isset($membership)?'edit='.$membership->getRentalAddressId():''),'account','billing_address_book','SSL'):itw_app_link('checkoutType=rental','checkout','default','SSL')));
									break;
								case 'pastdue':
									$errorMsg = sprintf(sysLanguage::get('RENTAL_CUSTOMER_IS_PAST_DUE'), itw_app_link((isset($membership)?'edit='.$membership->getRentalAddressId():''),'account','billing_address_book','SSL'));//
									break;
							}
							$messageStack->addSession('pageStack', $errorMsg, 'warning');
							tep_redirect(itw_app_link(tep_get_all_get_params(array('action')), 'product', 'info'));
						}
						//check product group
						EventManager::notify('CanAddToQueueProduct', &$pID_string, $cartProduct, &$canAdd);
						if($pInfo['already_queue'] == false && $canAdd){
							$cartProduct->purchaseTypeClass->onInsertQueueProduct(&$cartProduct);
							EventManager::notify('AddToQueueProduct', &$pID_string, $cartProduct);
						}
					}else{
						$canAdd = false;
						Session::set('add_to_queue_ppr_product', $cartProduct);
						$messageStack->addSession('pageStack',sysLanguage::get('TO_ADD_TO_QUEUE_PPR_MESSAGE'),'warning');
						tep_redirect(itw_app_link('checkoutType=rental','checkout','default','SSL'));
					}
				}
				if($canAdd){
				$this->contents[] =& $cartProduct;
				}
			}
			
			EventManager::notify('AddToContentsBeforeProcess', &$pID_string, $cartProduct);
		
			if ($runDbAction === true && $canAdd){
				$this->dbUtil->runAction($action, $cartProduct);
			}

			EventManager::notify('AddToContentsAfterProcess', &$pID_string, $cartProduct);

			$this->cleanUp();
		}
		
		private function removeKey($key){
			$cartProduct = $this->contents[$key];
			if(is_object($cartProduct)){
				$pID_string = $cartProduct->getIdString();
				$purchaseType = $cartProduct->getPurchaseType();

				if($cartProduct->hasInfo('is_queue')){
					$cartProduct->purchaseTypeClass->onRemoveQueueProduct($cartProduct);
					EventManager::notify('RemoveFromQueueProduct', &$pID_string, $cartProduct);
				}
				$cartProduct->purchaseTypeClass->processRemoveFromCart();

				unset($this->contents[$key]);
				$this->contents = array_values($this->contents);

				$this->dbUtil->runAction('delete', $cartProduct);
			}
		}
		
		private function cleanUp(){
			foreach($this->contents as $key => $cartProduct){
				if ($cartProduct->getQuantity() < 1){
					$this->removeKey($key);
				}
			}
		}
		
		public function &find($pID_string, $purchaseType = null){
			foreach($this->contents as $cartProduct){
				if($cartProduct->getUniqID() == $pID_string){
					return $cartProduct;
				}
			}
			return false;
		}

		public function findProductAsKey($cartProduct, $purchaseType = false){
			foreach($this->contents as $key => $iProduct){
				if ($iProduct->getIdString() == $cartProduct->getIdString()){
					if (($purchaseType == false)){
						return $key;
					}elseif ($iProduct->getPurchaseType() == $purchaseType){
						$returnVal = true;
						EventManager::notify('ShoppingCartFindKey', $iProduct, &$cartProduct, &$returnVal);
						if($returnVal){
							return $key;
						}
					}
				}
			}
			return false;

		}

		public function findProduct($pid, $purchaseType = false){

			foreach($this->contents as $key => $iProduct){
				if ($iProduct->getIdString() == $pid){
					if (($purchaseType == false)){
						return $iProduct;
					}elseif ($iProduct->getPurchaseType() == $purchaseType){
						$returnVal = true;
						EventManager::notify('ShoppingCartFind', $iProduct, &$returnVal);
						if($returnVal){
							return $iProduct;
						}
					}
				}
			}
			return false;

		}


		public function findProductByUniqID($pid, $purchaseType = false){

			foreach($this->contents as $key => $iProduct){
				if ($iProduct->getUniqID() == $pid){
					if (($purchaseType == false)){
						return $iProduct;
					}elseif ($iProduct->getPurchaseType() == $purchaseType){
						$returnVal = true;
						EventManager::notify('ShoppingCartFind', $iProduct, &$returnVal);
						if($returnVal){
							return $iProduct;
						}
					}
				}
			}
			return false;

		}
		
		private function findKey($pID_string, $purchaseType = false){
			foreach($this->contents as $key => $cartProduct){
				if($cartProduct->getUniqID() == $pID_string){
					return $key;
				}
			}
			return false;
		}
	}
?>