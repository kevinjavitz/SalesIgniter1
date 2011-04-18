<?php
	abstract class StreamProviderModule {
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
			
			$moduleDir = sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $code . '/';
			$this->xmlData = simplexml_load_file(
				$moduleDir . 'data/info.xml',
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);
				
			$info = $this->xmlData;
			
			$Qmodules = Doctrine_Query::create()
			->from('Modules m')
			->leftJoin('m.ModulesConfiguration mc')
			->where('m.modules_type = ?', 'stream_provider')
			->andWhere('m.modules_code = ?', $this->code)
			->orderBy('mc.sort_order')
			->execute();
			if ($Qmodules->count() > 0){
				$this->moduleData = $Qmodules->toArray(true);
				$this->configData = $this->moduleData[0]['ModulesConfiguration'];
				$this->installed = true;
				
				sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $code . '/language_defines/global.xml');
				if (file_exists(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/extensions/streamProducts/providerModules/' . $code . '/global.xml')){
					sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() .'includes/languages/' . Session::get('language') . '/extensions/streamProducts/providerModules/' . $code . '/global.xml');
				}
			
				$this->title = sysLanguage::get((string) $info->title_key);
				$this->description = sysLanguage::get((string) $info->description_key);
				$this->enabled = (bool) ($this->configData[(string) $info->status_key]['configuration_value'] == 'True' ? true : false);
			}
		}
		
		public function userHasPermission($streamId){
			global $userAccount;
			$hasPermission = false;
			
			$Qstream = Doctrine_Query::create()
			->from('ProductsStreams')
			->where('stream_id = ?', (int) $streamId)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qstream){
				if ($Qstream[0]['is_preview'] == 1){
					$hasPermission = true;
				}elseif ($userAccount->isLoggedIn() === true){
					if (true == false){
						$hasPermission = true;
					}
				}
			}
			return $hasPermission;
		}
		
		public function getStreamTypes(){
			return array(
				'http'
			);
		}
		
		public function setProviderConfig($config){
			foreach($this->configData as $key => $cInfo){
				if (isset($config[$key])){
					$this->configData[$key]['configuration_value'] = $config[$key];
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
		
		public function getStatus(){
			return $this->enabled;
		}
		
		public function getOutput(){
			return $this->output;
		}
		
		public function onInstall(&$module, &$moduleConfig){
		}
	}
?>