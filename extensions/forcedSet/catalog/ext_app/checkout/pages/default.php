<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class forcedSet_catalog_checkout_default extends Extension_forcedSet {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'CheckoutPreInit'
		), null, $this);
	}
	
	public function CheckoutPreInit(){
		global $ShoppingCart, $messageStack;
		$userAccount = &Session::getReference('userAccount');
		$arrRelation = array();
		$shoppingProducts = $ShoppingCart->getProducts()->getContents();
		for($i=0;$i< sizeof($shoppingProducts);$i++){
			$cartProduct = $shoppingProducts[$i];
			if ($cartProduct->getPurchaseType() == 'reservation'){
				$pID = $cartProduct->getIdString();
				if (!in_array($pID, $arrRelation)){
					//test for categories.
					$QcategoryCartProduct = Doctrine_Query::create()
								->from('ProductsToCategories')
								->where('products_id = ?', $pID)
								->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

					$cID = $QcategoryCartProduct[0]['categories_id'];

					$Qrelation = Doctrine_Query::create()
					->from('ForcedSetCategories')
					->where('forced_set_category_one_id = ?', $cID)
					->orWhere('forced_set_category_two_id = ?', $cID)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

					if (count($Qrelation) > 0){
						if ($Qrelation[0]['forced_set_category_one_id'] == $cID){
							$cIDRelation = $Qrelation[0]['forced_set_category_two_id'];
						}else{
							$cIDRelation = $Qrelation[0]['forced_set_category_one_id'];
						}
					}

					for($j=0;$j<sizeof($shoppingProducts);$j++){
						$cartProductNew = $shoppingProducts[$j]; 
						if ($cartProductNew->getPurchaseType() == 'reservation'){
							$pIDTest = $cartProductNew->getIdString();
							$QcategoryCartProduct = Doctrine_Query::create()
								->from('ProductsToCategories')
								->where('products_id = ?', $pIDTest)
								->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

							$cIDTestRelation = $QcategoryCartProduct[0]['categories_id'];
							if ($cIDRelation == $cIDTestRelation){
								$arrRelation[] =  $pIDTest;
								$arrRelation[] =  $pID;								
							}
						}
					}
				}
			}
		}		
		foreach($ShoppingCart->getProducts() as $cartProduct){
				if ($cartProduct->getPurchaseType() == 'reservation'){
					$pID = $cartProduct->getIdString();
					if (array_search($pID, $arrRelation) === false){
						//check user account
						if ($userAccount->isloggedIn() === false) {
							$messageStack->addSession('pageStack','You must have pairs of front/back wheels from the same size inside your cart','error');
							Session::set('redirectUrl', itw_app_link(null, 'shoppingCart', 'default','NONSSL'));
						}else{
							$customerdId = $userAccount->getCustomerId();
							$Qcustomer = Doctrine_Core::getTable('Customers')->findOneByCustomersId($customerdId);
							if ($Qcustomer->allow_one){
								//double shipping
								$pInfo = $cartProduct->getInfo();
								$pInfo['reservationInfo']['shipping']['cost'] = (float)$pInfo['reservationInfo']['shipping']['cost'] * 2;
								$ShoppingCart->updateProduct($pID, $pInfo);
							}else{
								$messageStack->addSession('pageStack','You must have pairs of front/back wheels from the same size inside your cart','error');
								Session::set('redirectUrl', itw_app_link(null, 'shoppingCart', 'default','NONSSL'));
							}
						}
					}
				}
		}
	}

}
?>