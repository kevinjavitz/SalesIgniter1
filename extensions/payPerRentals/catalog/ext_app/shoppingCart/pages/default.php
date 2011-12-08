<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_catalog_shoppingCart_default extends Extension_payPerRentals {
	
	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		EventManager::attachEvent('ShoppingCartAddFields', null, $this);
	}
	
	public function ShoppingCartAddFields(&$qty, $purchaseType, $cartProduct){
		global $ShoppingCart;
		if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_CHANGE_DATES_BUTTON') == 'True' && Session::exists('isppr_selected') && Session::get('isppr_selected') == true && $purchaseType == 'reservation'){
			$pInfo = $cartProduct->getInfo('reservationInfo');
			$startDate = htmlBase::newElement('input')
			->setType('hidden')
			->addClass('start_date_shop')
			->setName('cart_quantity['.$cartProduct->getUniqID().']['.$purchaseType.'][start_date]')
			->setValue($pInfo['start_date']);
			$endDate = htmlBase::newElement('input')
				->setType('hidden')
				->addClass('end_date_shop')
				->setName('cart_quantity['.$cartProduct->getUniqID().']['.$purchaseType.'][end_date]')
				->setValue($pInfo['end_date']);
			$qty .= $startDate->draw().$endDate->draw();

		}
	}        
}
?>