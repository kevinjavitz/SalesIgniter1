<?php
	abstract class OrderShippingModule {
		private $code = null;
		private $title = 'No Title Loaded';
		private $description = 'No Description Loaded';
		private $sortOrder = 0;
		private $enabled = false;
		private $installed = false;
		private $output = array();
		private $xmlData = null;
		private $check = null;
		private $configData = array();
		private $taxClass = 0;
		private $shippingZone = 0;
		
		public function init($code){
			$this->code = $code;
			
			$moduleDir = sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $code . '/';
			$this->xmlData = simplexml_load_file(
				$moduleDir . 'data/info.xml',
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);
				
			$info = $this->xmlData;
			
			$Qmodules = Doctrine_Query::create()
			->from('Modules m')
			->leftJoin('m.ModulesConfiguration mc')
			->where('m.modules_type = ?', 'order_shipping')
			->andWhere('m.modules_code = ?', $this->code)
			->orderBy('mc.sort_order')
			->execute();
			if ($Qmodules->count() > 0){
				$this->moduleData = $Qmodules->toArray(true);
				$this->configData = $this->moduleData[0]['ModulesConfiguration'];
				$this->installed = true;
				
				sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $code . '/language_defines/global.xml');
				if (file_exists(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/orderShippingModules/' . $code . '/global.xml')){
					sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/orderShippingModules/' . $code . '/global.xml');
				}
				
				if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $code . '/Doctrine/')){
					Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $code . '/Doctrine/', Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
				}
			
				$this->title = sysLanguage::get((string) $info->title_key);
				$this->description = sysLanguage::get((string) $info->description_key);
				
				if (array_key_exists((string) $info->status_key, $this->configData)){
					$this->enabled = (bool) ($this->configData[(string) $info->status_key]['configuration_value'] == 'True' ? true : false);
				}
				
				if (array_key_exists((string) $info->zone_key, $this->configData)){
					$this->shippingZone = (int) $this->configData[(string) $info->zone_key]['configuration_value'];
				}
				
				if (array_key_exists((string) $info->tax_class_key, $this->configData)){
					$this->taxClass = (int) $this->configData[(string) $info->tax_class_key]['configuration_value'];
				}
				
				if (array_key_exists((string) $info->sort_key, $this->configData)){
					$this->sortOrder = (int) $this->configData[(string) $info->sort_key]['configuration_value'];
				}
				
				$this->updateStatus();
			}
		}
		
		public function &getUserAccount(){
			global $onePageCheckout;
			if (isset($onePageCheckout) && is_object($onePageCheckout)){
				$userAccount = &$onePageCheckout->getUserAccount();
			}elseif (Session::exists('pointOfSale') === true){
				$pointOfSale = &Session::getReference('pointOfSale');
				$userAccount = &$pointOfSale->getUserAccount();
			}
			return $userAccount;
		}
		
		public function getDeliveryAddress(){
			global $Editor, $userAccount;
			if (isset($Editor) && is_object($Editor)){
				$Address = $Editor->AddressManager->getAddress('delivery');
				return $Address->toArray();
			}

			if (isset($userAccount)){
				$addressBook = $userAccount->plugins['addressBook'];
			}
			if (isset($addressBook) && is_object($addressBook)){
				if ($addressBook->entryExists('delivery') === true){
					$deliveryAddress = $addressBook->getAddress('delivery');
				}else{
					$deliveryAddress = $addressBook->getAddress('billing');
				}
				return $deliveryAddress;
			}
			return false;
		}
		
		public function updateStatus(){
			global $order, $onePageCheckout;

			if (is_object($order) && $this->enabled === true && $this->shippingZone > 0){
				$deliveryAddress = $this->getDeliveryAddress();
				$check_flag = false;
				$Qcheck = Doctrine_Query::create()
				->select('zt.zone_id, z.geo_zone_id')
				->from('GeoZones z')
				->leftJoin('z.ZonesToGeoZones zt')
				->where('z.geo_zone_id = ?', $this->shippingZone)
				->andWhere('zt.zone_country_id = ?', $deliveryAddress['entry_country_id'])
				->orderBy('zt.zone_id')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if ($Qcheck){
					foreach($Qcheck as $zInfo){
						foreach($zInfo['ZonesToGeoZones'] as $iInfo){
							if ($iInfo['zone_id'] < 1){
								$check_flag = true;
								break;
							} elseif ($iInfo['zone_id'] == $deliveryAddress['entry_zone_id']){
								$check_flag = true;
								break;
							}
						}
					}
				}

				if ($check_flag == false){
					$this->enabled = false;
				}
			}
		}
		
		public function check(){
			return ($this->isInstalled() === true);
		}
		
		public function addOutput($data){
			$this->output[] = $data;
		}
		
		public function isEnabled(){
			return $this->enabled;
		}

		public function setEnabled($value){
			$this->enabled = $value;
		}
		
		public function isInstalled(){
			return ($this->installed === true);
		}
		
		public function isFromExtension(){
			return false;
		}
		
		public function getExtensionName(){
			return false;
		}
		
		public function getConfig(){
			return $this->configData;
		}
		
		public function getConfigData($key = null){
			if (is_null($key) === true){
				return $this->configData;
			}else{
				if (array_key_exists($key, $this->configData)){
					return $this->configData[$key]['configuration_value'];
				}
				return null;
			}
		}
		
		public function getCode(){
			return $this->code;
		}
		
		public function setTitle($val){
			$this->title = $val;
		}
		
		public function getTitle(){
			return $this->title;
		}
		
		public function setDescription($val){
			$this->description = $val;
		}
		
		public function getDescription(){
			return $this->description;
		}
		
		public function getSortOrder(){
			return $this->sortOrder;
		}
		
		public function getStatus(){
			return $this->enabled;
		}
		
		public function getTaxClass(){
			return $this->taxClass;
		}
		
		public function getOutput(){
			return $this->output;
		}
		
		public function getMethods(){
			return $this->methods;
		}
		
		public function quote($method = ''){
			die('Quote function not overwritten.');
		}
		
		public function formatAmount($amount){
			global $order, $currencies;
			return $currencies->format($amount, true, $order->info['currency'], $order->info['currency_value']);
		}
		
		public function onInstall(&$module, &$moduleConfig){
		}
	}
?>