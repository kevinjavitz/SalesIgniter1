<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	require(dirname(__FILE__) . '/PaymentModuleBase.php');
	require(dirname(__FILE__) . '/StandardModule.php');
	require(dirname(__FILE__) . '/CreditCardModule.php');
	
	class OrderPaymentModules extends SystemModulesLoader {
		public static $dir = 'orderPaymentModules';
		public static $classPrefix = 'OrderPayment';
		private static $selected = null;

		public static function setSelected($moduleName){
			self::$selected = $moduleName;
		}

		public static function getSelected(){
			return self::getModule(self::$selected);
		}
		
		public static function getDropMenuArray($includeDisabled = false){
			$modules = self::getModules($includeDisabled);
			
			$dropMenuArray = array(array(
				'id' => '',
				'text' => 'Please Select A Payment Method'
			));
			foreach($modules as $moduleName => $moduleClass){
				$dropMenuArray[] = array(
					'id'   => $moduleClass->code,
					'text' => $moduleClass->title
				);
			}
			return $dropMenuArray;
		}
		
		public static function &getUserAccount(){
			global $onePageCheckout, $membershipUpdate;
			if (isset($onePageCheckout) && is_object($onePageCheckout)){
				$userAccount = &$onePageCheckout->getUserAccount();
			}elseif (isset($membershipUpdate) && is_object($membershipUpdate)){
				$userAccount = &$membershipUpdate->getUserAccount();
			}
			return $userAccount;
		}
		
		public static function &getPaymentInfo(){
			global $onePageCheckout;
			if (isset($onePageCheckout) && is_object($onePageCheckout)){
				$paymentInfo = $onePageCheckout->onePage['info']['payment'];
			}
			return $paymentInfo;
		}
	}
?>