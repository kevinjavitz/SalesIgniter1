<?php
class ModuleConfig {

	public function __construct($dataArray){
		$this->cfg = $dataArray;
	}

	public function getTab(){
		return $this->cfg['tab'];
	}

	public function getKey(){
		return $this->cfg['key'];
	}

	public function getValue(){
		return $this->cfg['value'];
	}

	public function getTitle(){
		return $this->cfg['title'];
	}

	public function getDescription(){
		return $this->cfg['description'];
	}

	public function hasUseFunction(){
		return ($this->cfg['use_function'] !== null);
	}

	public function getUseFunction(){
		return $this->cfg['use_function'];
	}

	public function hasSetFunction(){
		return ($this->cfg['set_function'] !== null);
	}

	public function getSetFunction(){
		return $this->cfg['set_function'];
	}
}

class ModuleConfigReader {

	private $configData = array();

	public function __construct($module, $moduleType, $moduleDir = false){
		$this->module = $module;
		$this->moduleType = $moduleType;

		if ($moduleDir === false){
			$moduleDir = sysConfig::getDirFsCatalog() . 'includes/modules/' . $this->moduleType . 'Modules/' . $this->module . '/';
		}
		$coreFile = simplexml_load_file(
			$moduleDir . 'data/config.xml',
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);

		$this->parseXmlConfig($coreFile);

		$Extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions');
		foreach($Extensions as $Ext){
			if ($Ext->isDot() || $Ext->isFile()) continue;

			if (is_dir($Ext->getPathname() . '/data/base')){
				if (file_exists($Ext->getPathname() . '/data/base/purchaseTypes.xml')){
					$extFile = simplexml_load_file(
						$Ext->getPathname() . '/data/base/purchaseTypes.xml',
						'SimpleXMLElement',
						LIBXML_NOCDATA
					);
					$this->parseXmlConfig($extFile);
				}
			}
		}
	}

	private function parseXmlConfig($xmlObj){
		$Qmodule = Doctrine_Query::create()
			->from('Modules m')
			->leftJoin('m.ModulesConfiguration mc')
			->where('m.modules_type = ?', $this->moduleType)
			->andWhere('m.modules_code = ?', $this->module)
			->execute()
			->toArray();
		foreach($xmlObj->configuration as $cInfo){
			$cfgKey = (string) $cInfo->key;
			if (isset($Qmodule[0]['ModulesConfiguration'][$cfgKey])){
				$configVal = $Qmodule[0]['ModulesConfiguration'][$cfgKey]['configuration_value'];
			}else{
				$configVal = (string) $cInfo->value;
			}
			$this->configData[$cfgKey] = new ModuleConfig(array(
				'key' => $cfgKey,
				'value' => $configVal,
				'tab' => (string) $cInfo->tab,
				'title' => (string) $cInfo->title,
				'description' => (string) $cInfo->description,
				'use_function' => (isset($cInfo->use_function) ? (string) $cInfo->use_function : null),
				'set_function' => (isset($cInfo->set_function) ? (string) $cInfo->set_function : null)
			));
		}
	}

	public function getConfig($key = false){
		if ($key === false){
			return $this->configData;
		}
		return $this->configData[$key];
	}
}