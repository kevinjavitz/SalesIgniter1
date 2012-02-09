<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	require(dirname(__FILE__) . '/Abstract.php');
	
	class OrderShippingModules extends SystemModulesLoader {
		public static $dir = 'orderShippingModules';
		public static $classPrefix = 'OrderShipping';
		private static $selectedModule = null;
		private static $selectedMethod = null;
		private static $deliveryAddress = null;

		public static function setSelected($module, $method){
			self::$selectedModule = $module;
			self::$selectedMethod = $method;
		}

		public static function getSelected(){
			return self::getModule(self::$selectedModule);
		}

		public static function getDropMenuArray($includeDisabled = false){
			$modules = self::getModules($includeDisabled);
			
			$dropMenuArray = array(array(
				'id' => '',
				'text' => 'Please Select A Shipping Method'
			));
			foreach($modules as $moduleName => $moduleClass){
				$dropMenuArray[] = array(
					'id'   => $moduleClass->code,
					'text' => $moduleClass->title
				);
			}
			return $dropMenuArray;
		}
		
		public static function quote($method = '', $module = ''){
			$quotes_array = array();
			if (self::hasModules() === true) {
				self::calculateWeight();
				
				$include_quotes = array();
				foreach(self::getModules() as $moduleName => $moduleClass){
					if (($module == '') || (($module != '') && $module == $moduleName)){
						$quotes = $moduleClass->quote($method);
						if (is_array($quotes)) $quotes_array[] = $quotes;
					}
				}
			}
			return $quotes_array;
		}
		
		public static function calculateWeight(){
			global $total_weight, $shipping_weight, $shipping_num_boxes;
			
			$boxWeight = sysConfig::get('SHIPPING_BOX_WEIGHT');
			$boxPadding = sysConfig::get('SHIPPING_BOX_PADDING');
			$boxMaxWeight = sysConfig::get('SHIPPING_MAX_WEIGHT');
			
			$shipping_quoted = '';
			$shipping_num_boxes = 1;
			$shipping_weight = $total_weight;
			
			if ($boxWeight >= $shipping_weight*$boxPadding/100){
				$shipping_weight = $shipping_weight+$boxWeight;
			}else{
				$shipping_weight = $shipping_weight + ($shipping_weight*$boxPadding/100);
			}
			
			if ($shipping_weight > $boxMaxWeight) { // Split into many boxes
				$shipping_num_boxes = ceil($shipping_weight/$boxMaxWeight);
				$shipping_weight = $shipping_weight/$shipping_num_boxes;
			}
		}
		
		public static function getCheapestMethod(){
			if (self::hasModules() === true) {
				$rates = array();
				foreach(self::getModules() as $moduleName => $moduleClass){
					if ($moduleName != 'reservation'){
						$quotes = $moduleClass->quote();
						for($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++){
							if (isset($quotes['methods'][$i]['cost']) && $quotes['methods'][$i]['cost'] != '') {
								$rates[] = array(
									'id'     => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],
									'module' => $quotes['id'],
									'method' => $quotes['methods'][$i]['id'],
									'title'  => $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')',
									'cost'   => $quotes['methods'][$i]['cost']
								);
							}
						}
					}
				}

				$cheapest = false;
				for ($i=0, $n=sizeof($rates); $i<$n; $i++) {
					if (is_array($cheapest)) {
						if ($rates[$i]['cost'] < $cheapest['cost']) {
							$cheapest = $rates[$i];
						}
					} else {
						$cheapest = $rates[$i];
					}
				}
				return $cheapest;
			}
		}
		
		public static function getCountryInfo($cID){
			$Qcountry = Doctrine_Query::create()
			->from('Countries c')
			->leftJoin('c.AddressFormat af')
			->where('countries_id = ?', $cID)
			->execute();

			return $Qcountry[0];
		}

		public static function &getUserAccount(){
			global $onePageCheckout, $membershipUpdate;
			if (isset($onePageCheckout) && is_object($onePageCheckout)){
				$userAccount = &$onePageCheckout->getUserAccount();
			}elseif (isset($membershipUpdate) && is_object($membershipUpdate)){
				$userAccount = &$membershipUpdate->getUserAccount();
			}elseif (Session::exists('pointOfSale') === true){
				$pointOfSale = &Session::getReference('pointOfSale');
				$userAccount = &$pointOfSale->getUserAccount();
			}
			return $userAccount;
		}

		public static function setDeliveryAddress($addressObj){
			self::$deliveryAddress = $addressObj;
		}

		public static function getDeliveryAddress(){
			return self::$deliveryAddress;
		}
	}
?>