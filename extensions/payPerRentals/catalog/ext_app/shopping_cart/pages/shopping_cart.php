<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_catalog_shopping_cart extends Extension_payPerRentals {
	
	public function __construct(){
		parent::__construct();
		
		if (basename($_SERVER['PHP_SELF']) != 'shopping_cart.php'){
			$this->enabled = false;
		}
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		if (EXTENSION_PAY_PER_RENTALS_DATE_COVERAGE == 'Order'){
			EventManager::attachEvent('ShoppingCartListingBeforeListing', null, $this);
		}
		EventManager::attachEvent('PageContentLoad', null, $this);
	}
	
	public function PageContentLoad(){
		global $App;
		//$App->addJavascriptFile('extensions/payPerRentals/catalog/ext_app/shopping_cart/javascript/shopping_cart.js');
	}
	
	public function ShoppingCartListingBeforeListing(&$cartBox){
		global $ShoppingCart;
		
		if ($this->orderClassFunction_hasReservation(false) === false || EXTENSION_PAY_PER_RENTALS_DATE_SELECTION == 'Before') return;
		
		$selectorBox = htmlBase::newElement('div')
		->addClass('ui-widget ui-widget-content')
		->css(array(
			'padding' => '.3em',
			'margin-bottom' => '1em',
			'text-align' => 'left',
			'position' => 'relative'
		));
		
		$header = htmlBase::newElement('div')->addClass('main')->css(array(
			'font-size' => '1.3em',
			'padding' => '.1em'
		))->html('<b>My Rental Dates</b>');
		
		if (isset($ShoppingCart->reservationInfo) && !empty($ShoppingCart->reservationInfo['start_date'])){
			$startDate = $ShoppingCart->reservationInfo['start_date'];
			$endDate = $ShoppingCart->reservationInfo['end_date'];
			
			$startDateArr = date_parse($startDate);
			$endDateArr = date_parse($endDate);
			
			$startTime = mktime(0,0,0,$startDateArr['month'],$startDateArr['day'],$startDateArr['year']);
			$endTime = mktime(0,0,0,$endDateArr['month'],$endDateArr['day'],$endDateArr['year']);
		}
		
		$startDate = htmlBase::newElement('div')->css('padding', '.3em')->addClass('main')->html('Rental Start Date: <input type="text" name="start_date" class="startDate" ' . (isset($startDate) ? 'value="' . $startDate . '" ' : '') . 'readonly="readonly" /><span class="startDateLong" style="padding-left:.5em;">' . (isset($startTime) ? date('D, d F Y', $startTime) : '') . '</span>');
		$endDate = htmlBase::newElement('div')->css('padding', '.3em')->addClass('main')->html('Rental End Date: <input type="text" name="end_date" class="endDate" ' . (isset($endDate) ? 'value="' . $endDate . '" ' : '') . 'readonly="readonly" /><span class="endDateLong" style="padding-left:.5em;">' . (isset($endTime) ? date('D, d F Y', $endTime) : '') . '</span>');
		
		$selectorBox->append($header)->append($startDate)->append($endDate);				
		$cartBox->append($selectorBox);
	}        
}
?>