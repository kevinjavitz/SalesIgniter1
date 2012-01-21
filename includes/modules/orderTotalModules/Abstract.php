<?php
	abstract class OrderTotalModule {
		private $code = null;
		private $title = 'No Title Loaded';
		private $description = 'No Description Loaded';
		private $sort_order = 0;
		private $enabled = false;
		private $installed = false;
		private $output = array();
		private $xmlData = null;
		private $check = null;
		private $configData = array();
		
		public function init($code){
			$this->code = $code;
			
			$moduleDir = sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/' . $code . '/';
			$this->xmlData = simplexml_load_file(
				$moduleDir . 'data/info.xml',
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);
				
			$info = $this->xmlData;
			
			$Qmodules = Doctrine_Query::create()
			->from('Modules m')
			->leftJoin('m.ModulesConfiguration mc')
			->where('m.modules_type = ?', 'order_total')
			->andWhere('m.modules_code = ?', $this->code)
			->orderBy('mc.sort_order')
			->execute();
			if ($Qmodules->count() > 0){
				$this->moduleData = $Qmodules->toArray(true);
				$this->configData = $this->moduleData[0]['ModulesConfiguration'];
				$this->installed = true;
				
				sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/modules/orderTotalModules/' . $code . '/language_defines/global.xml');
				if (file_exists(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/orderTotalModules/' . $code . '/global.xml')){
					sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/includes/modules/orderTotalModules/' . $code . '/global.xml');
				}
			
				$this->title = sysLanguage::get((string) $info->title_key);
				$this->description = sysLanguage::get((string) $info->description_key);
				$this->enabled = (bool) ($this->configData[(string) $info->status_key]['configuration_value'] == 'True' ? true : false);
				$this->sort_order = (int) $this->configData[(string) $info->sort_key]['configuration_value'];
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
		
		public function getConfigData($key){
			if (array_key_exists($key, $this->configData)){
				return $this->configData[$key]['configuration_value'];
			}
			return null;
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
			return $this->sort_order;
		}
		
		public function getStatus(){
			return $this->enabled;
		}
		
		public function getOutput(){
			return $this->output;
		}
		
		public function process(){
			die('Process function not overwritten.');
		}

		public function pre_confirmation_check($orderTotal){
		}
		
		public function selection_test(){
		}
		
		public function formatAmount($amount){
			global $order, $currencies;
			return $currencies->format($amount, true, $order->info['currency'], $order->info['currency_value']);
		}
		
		public function onInstall(&$module, &$moduleConfig){
		}
	}
?>