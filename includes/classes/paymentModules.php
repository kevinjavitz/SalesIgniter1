<?php
/*
	Rental Store Version 2

	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	require(dirname(__FILE__) . '/paymentModules/Abstract/PaymentModuleBase.php');
	require(dirname(__FILE__) . '/paymentModules/Abstract/StandardModule.php');
	require(dirname(__FILE__) . '/paymentModules/Abstract/CreditCardModule.php');
	
	class PaymentModules {
		private static $installedPaymentModules = array();
		private static $requestedPaymentModules = array();
		
		public static function getModule($moduleName, $ignoreStatus = false){
			if (self::moduleIsLoaded($moduleName)){
				if ($ignoreStatus === true){
					return self::$requestedPaymentModules[$moduleName];
				}elseif (self::$requestedPaymentModules[$moduleName]->enabled === true){
					return self::$requestedPaymentModules[$moduleName];
				}
			}else{
				include(sysConfig::getDirFsCatalog() . 'includes/classes/paymentModules/LanguageDefines/' . $moduleName . '/' . Session::get('language') . '.xml');
				include(sysConfig::getDirFsCatalog() .'includes/classes/paymentModules/Modules/' . $moduleName . '.php');
				
				self::$requestedPaymentModules[$moduleName] = new $moduleName;
				if ($ignoreStatus === true){
					return self::$requestedPaymentModules[$moduleName];
				}else{
					if (self::$requestedPaymentModules[$moduleName]->enabled === true){
						return self::$requestedPaymentModules[$moduleName];
					}
				}
			}
			return null;
		}
		
		public static function getAllModules($includeDisabled = false){
			$modules = array();
			if ($includeDisabled === true){
				$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/classes/paymentModules/Modules');
				foreach($dir as $fileObj){
					if ($fileObj->isDir() || $fileObj->isDot()) continue;
					
					$moduleName = $fileObj->getBaseName('.php');
					$modules[$moduleName] = self::getModule($moduleName, true);
				}
			}else{
				if (empty(self::$installedPaymentModules)){
					if (sysConfig::exists('MODULE_PAYMENT_INSTALLED') && sysConfig::isNotEmpty('MODULE_PAYMENT_INSTALLED')){
						self::$installedPaymentModules = sysConfig::explode('MODULE_PAYMENT_INSTALLED', ';');
					}
				}
				
				foreach(self::$installedPaymentModules as $moduleFile){
					$moduleName = substr($moduleFile, 0, strrpos($moduleFile, '.'));
					$modules[$moduleName] = self::getModule($moduleName, true);
				}
			}
			return $modules;
		}
		
		public static function getTotalEnabled(){
			$modules = self::getAllModules();
			$enabledTotal = 0;
			foreach($modules as $moduleName => $moduleClass){
				if ($moduleClass->enabled === true){
					$enabledTotal++;
				}
			}
			return $enabledTotal;
		}
		
		public static function modulesAreInstalled(){
			$modules = self::getAllModules();
			return (sizeof($modules) > 0 ? true : false);
		}
		
		public static function moduleIsLoaded($moduleName){
			if (array_key_exists($moduleName, self::$requestedPaymentModules)){
				return true;
			}
			return false;
		}
		
		public static function moduleIsEnabled($moduleName){
			$module = self::getModule($moduleName);
			if (is_null($module) === false){
				return true;
			}
			return false;
		}
		
		public function getDropMenuArray($includeDisabled = false){
			$modules = self::getAllModules($includeDisabled);
			
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
			}elseif (Session::exists('pointOfSale') === true){
				$pointOfSale = &Session::getReference('pointOfSale');
				$userAccount = &$pointOfSale->getUserAccount();
			}
			return $userAccount;
		}
		
		public static function &getPaymentInfo(){
			global $onePageCheckout;
			if (isset($onePageCheckout) && is_object($onePageCheckout)){
				$paymentInfo = $onePageCheckout->onePage['info']['payment'];
			}elseif (Session::exists('pointOfSale') === true){
				$pointOfSale = &Session::getReference('pointOfSale');
				if (isset($pointOfSale->order['info']['payment'])){
					$paymentInfo = $pointOfSale->order['info']['payment'];
				}else{
					$paymentInfo = array();
				}
			}
			return $paymentInfo;
		}
		
		public static function installModule($moduleName){
			if (Session::exists('login_id') === false) return;
			if (!file_exists(sysConfig::getDirFsCatalog() . 'includes/classes/paymentModules/Installers/' . $moduleName . '.php')) return;
			
			require(sysConfig::getDirFsCatalog() . 'includes/classes/paymentModules/Installers/' . $moduleName . '.php');
			$className = $moduleName . 'Installer';
			$dataArray = call_user_func(array($className, 'install'), '');
			if (is_array($dataArray)){
				foreach($dataArray as $key => $cInfo){
					$Qinsert = new Configuration();
					$Qinsert->configuration_group_id = 6;
					if (!is_array($cInfo)){
						switch($key){
							case 'sortOrderKey':
								$Qinsert->configuration_title = 'Sort order of display';
								$Qinsert->configuration_value = '0';
								$Qinsert->configuration_description = 'Sort order of display. Lowest is displayed first.';
								break;
							case 'paymentZoneKey':
								$Qinsert->configuration_title = 'Payment Zone';
								$Qinsert->configuration_value = '0';
								$Qinsert->configuration_description = 'If a zone is selected, only enable this payment method for that zone.';
								$Qinsert->use_function = 'tep_get_zone_class_title';
								$Qinsert->set_function = 'tep_cfg_pull_down_zone_classes(';
								break;
							case 'orderStatusKey':
								$Qinsert->configuration_title = 'Set Order Status';
								$Qinsert->configuration_value = '0';
								$Qinsert->configuration_description = 'Set the status of orders made with this payment module to this value';
								$Qinsert->use_function = 'tep_get_order_status_name';
								$Qinsert->set_function = 'tep_cfg_pull_down_order_statuses(';
								break;
							case 'checkoutMethodKey':
								$Qinsert->configuration_title = 'Accept For';
								$Qinsert->configuration_value = '0';
								$Qinsert->configuration_description = 'Allow this payment module to be used for (Rental membership signup, Normal checkout or Both )';
								$Qinsert->set_function = 'tep_cfg_select_option(array(\'Membership\',\'Normal\',\'Both\'),';
								break;
							default:
								die('Unknown Key: ' . $key . '::' . $cInfo);
								break;
						}
						$Qinsert->configuration_key = $cInfo;
					}else{
						foreach($cInfo as $k => $v){
							$Qinsert->$k = $v;
						}
					}
					$Qinsert->save();
				}
			}
		}
	
		public static function uninstallModule($moduleName){
			global $appExtension;
			
			$module = self::getModule($moduleName);
			$moduleKeys = $module->keys();
		
			Doctrine_Query::create()
			->delete('Configuration')
			->whereIn('configuration_key', $moduleKeys)
			->execute();
		}
	
		public static function addMissingConfig($moduleName){
			require(sysConfig::getDirFsCatalog() . 'includes/classes/paymentModules/Installers/' . $moduleName . '.php');
			$className = $moduleName . 'Installer';
			$dataArray = call_user_func(array($className, 'install'), '');
			foreach($dataArray as $configKey => $configSettings){
				$Qcheck = Doctrine_Query::create()
				->select('configuration_id')
				->from('Configuration');
				if (is_array($configSettings)){
					$Qcheck->where('configuration_key = ?', $configSettings['configuration_key']);
				}else{
					$Qcheck->where('configuration_key = ?', $configSettings);
				}
				$Qcheck->execute();
				if ($Qcheck->count() <= 0){
					if (is_array($configSettings)){
						$newConfig = new Configuration();
						$newConfig->configuration_key = (string) $configSettings['configuration_key'];
						$newConfig->configuration_title = (string) $configSettings['configuration_title'];
						$newConfig->configuration_value = (string) $configSettings['configuration_value'];
						$newConfig->configuration_description = (string) $configSettings['configuration_description'];

						if (isset($configSettings['use_function'])){
							$newConfig->use_function = (string) $configSettings['use_function'];
						}

						if (isset($configSettings['set_function'])){
							$newConfig->set_function = (string) $configSettings['set_function'];
						}
					
						$newConfig->configuration_group_id = (int) 6;
						$newConfig->sort_order = (int) 0;
						$newConfig->save();
					}else{
						die('Preset Keys Are Not Supported At This Time.');
					}
				}
				$Qcheck->free();
			}
		}
	}
?>