<?php
/*
	Related Products Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_customerGroups extends ExtensionBase {

	public function __construct(){
		parent::__construct('customerGroups');
	}

	public function init(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'PurchaseTypeAfterSetup',
			'ProductQueryAfterExecute',
			'ReservationPriceBeforeSetup',
			'ProductListingQueryBeforeExecute'
		), null, $this);
	}

	public function ProductListingQueryBeforeExecute(&$productQuery){
		global $userAccount;
		$cID = '-1';
		if ($userAccount->isLoggedIn() === false){
			$cID = '-1';
		}else{

			$custID = $userAccount->getCustomerId();


			if(isset($cID)){
				$QCustomers = Doctrine_Query::create()
					->from('CustomerGroups c')
					->leftJoin('c.CustomersToCustomerGroups cg')
					->where('cg.customers_id=?',(int)$custID)
					->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

				if(count($QCustomers) > 0){
					$cID = $QCustomers[0]['customer_groups_id'];
				}
			}
		}

		$productQuery->leftJoin('p.ProductsToCustomerGroups p2cg')
		->andWhere('p2cg.customer_groups_id != '.$cID.' or p2cg.customer_groups_id is null');
	}

	public function PurchaseTypeAfterSetup(&$productInfo){
		global $userAccount, $appExtension, $currencies;

		if($appExtension->isCatalog()){
			$cID = $userAccount->getCustomerId();
		}else{
			global $Editor;
			if(isset($Editor)){
				$cID = $Editor->getCustomerId();
			}
		}

		if(isset($cID)){
			$QCustomers = Doctrine_Query::create()
			->from('CustomerGroups c')
			->leftJoin('c.CustomersToCustomerGroups cg')
			->where('cg.customers_id=?',(int)$cID)
			->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

			if(count($QCustomers) > 0){
				$discount = $QCustomers[0]['customer_groups_discount'];

				$productInfo['price'] -= $productInfo['price']*$discount/100;
				if(isset($productInfo['message'])){
					$productInfo['message'].= '-'. $currencies->format($productInfo['price']*$discount/100). sysLanguage::get('TEXT_DISCOUNT_BASED');
				}
				if (isset($productInfo['special_price'])){
					$productInfo['special_price'] -= $productInfo['special_price']*$discount/100;
				}
			}
		}
	}


	public function ProductQueryAfterExecute(&$productInfo){
		global $userAccount, $appExtension;

		if($appExtension->isCatalog()){
			$cID = $userAccount->getCustomerId();
		}else{
			global $Editor;
			if(isset($Editor)){
				$cID = $Editor->getCustomerId();
			}
		}
		if(isset($cID)){
			$QCustomers = Doctrine_Query::create()
			->from('CustomerGroups c')
			->leftJoin('c.CustomersToCustomerGroups cg')
			->where('cg.customers_id=?',(int)$cID)
			->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

			if(count($QCustomers) > 0){
				$discount = $QCustomers[0]['customer_groups_discount'];
				$productInfo['price'] -= $productInfo['price']*$discount/100;
				if (isset($productInfo['special_price'])){
					$productInfo['special_price'] -= $productInfo['special_price']*$discount/100;
				}
			}
		}
	}

	public function ReservationPriceBeforeSetup(&$price){
		global $userAccount, $appExtension;

		if($appExtension->isCatalog()){
			$cID = $userAccount->getCustomerId();
		}else{
			global $Editor;
			if(isset($Editor)){
				$cID = $Editor->getCustomerId();
			}
		}
		if(isset($cID)){
			$QCustomers = Doctrine_Query::create()
			->from('CustomerGroups c')
			->leftJoin('c.CustomersToCustomerGroups cg')
			->where('cg.customers_id=?',(int)$cID)
			->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

			if(count($QCustomers) > 0){
				$discount = $QCustomers[0]['customer_groups_discount'];
				$price -= $price*$discount/100;
			}
		}
	}




}
?>