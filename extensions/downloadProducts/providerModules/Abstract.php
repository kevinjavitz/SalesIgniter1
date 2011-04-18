<?php
	abstract class DownloadProviderModule {
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
			
			$moduleDir = sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/' . $code . '/';
			$this->xmlData = simplexml_load_file(
				$moduleDir . 'data/info.xml',
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);
				
			$info = $this->xmlData;
			
			$Qmodules = Doctrine_Query::create()
			->from('Modules m')
			->leftJoin('m.ModulesConfiguration mc')
			->where('m.modules_type = ?', 'download_provider')
			->andWhere('m.modules_code = ?', $this->code)
			->orderBy('mc.sort_order')
			->execute();
			if ($Qmodules->count() > 0){
				$this->moduleData = $Qmodules->toArray(true);
				$this->configData = $this->moduleData[0]['ModulesConfiguration'];
				$this->installed = true;
				
				sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/' . $code . '/language_defines/global.xml');
				if (file_exists(sysConfig::getDirFsCatalog() . 'includes/languages/' . Session::get('language') . '/extensions/downloadProducts/providerModules/' . $code . '/global.xml')){
					sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() .'includes/languages/' . Session::get('language') . '/extensions/downloadProducts/providerModules/' . $code . '/global.xml');
				}
			
				$this->title = sysLanguage::get((string) $info->title_key);
				$this->description = sysLanguage::get((string) $info->description_key);
				$this->enabled = (bool) ($this->configData[(string) $info->status_key]['configuration_value'] == 'True' ? true : false);
			}
		}
		
		public function getHeaderContentType($ext){
			$type = 'application/force-download';
			switch($ext){
				case 'gif': $type = 'image/gif'; break;
				case 'png': $type = 'image/png'; break;
				case 'jpg': $type = 'image/jpg'; break;
				case 'mpg': $type = 'video/mpg'; break;
				case 'mp4': $type = 'video/mp4'; break;
				case 'm4v': $type = 'video/m4v'; break;
				case 'flv': $type = 'video/flv'; break;
			}
			return $type;
		}
		
		public function getDownloadTypes(){
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