<?php
class ModuleInstaller {

	private $moduleDir;

	private $moduleType;

	private $moduleCls;

	private $moduleInfo;

	private $configData;

	public function __construct($moduleType, $moduleCode, $extName = null, $modulePath = null){
		$this->moduleType = $moduleType;
		$this->moduleDir = sysConfig::getDirFsCatalog();
		if (is_null($modulePath) === false){
			if (is_dir($modulePath)){
				$this->moduleDir = $modulePath;
			}
			else{
				$this->moduleDir .= $modulePath;
			}
		}
		elseif (is_null($extName) === false){
			$this->moduleDir .= 'extensions/' . $extName . '/' . $moduleType . 'Modules/' . $moduleCode . '/';
		}
		else{
			$this->moduleDir .= 'includes/modules/' . $moduleType . 'Modules/' . $moduleCode . '/';
		}
		$dataDir = $this->moduleDir . 'data/';

		$this->moduleInfo = simplexml_load_file(
			$dataDir . 'info.xml',
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);

		$this->configData = simplexml_load_file(
			$dataDir . 'config.xml',
			'SimpleXMLElement',
			LIBXML_NOCDATA
		);

		$className = ucfirst($moduleType) . 'Modules';
		$this->moduleCls = $className::getModule($moduleCode, true);
	}

	public function install(){
		if ($this->moduleCls->isInstalled() === false){
			if (is_dir($this->moduleDir . 'Doctrine')){
				Doctrine_Core::createTablesFromModels($this->moduleDir . 'Doctrine/');
			}

			$moduleConfig = new Modules();
			$moduleConfig->modules_code = $this->moduleCls->getCode();
			$moduleConfig->modules_status = '1';
			$moduleConfig->modules_type = $this->moduleType;

			$moduleConfiguration =& $moduleConfig->ModulesConfiguration;
			$k = 0;
			foreach($this->configData->configuration as $cInfo){
				$ConfigValue = (string) $cInfo->value;
				if ((string) $cInfo->key == (string) $this->moduleInfo->installed_key){
					$ConfigValue = 'True';
				}
				$moduleConfiguration[$k]->configuration_key = (string) $cInfo->key;
				$moduleConfiguration[$k]->configuration_value = $ConfigValue;
				$k++;
			}

			$moduleConfig->save();

			/*
							 * @TODO: Translate module language files for installed languages
							 */

			$this->moduleCls->onInstall(&$this, &$moduleConfig);
		}
	}

	public function enable(){
		if ($this->moduleCls->isEnabled() === false){
			$Module = Doctrine_Core::getTable('Modules')
				->findOneByModulesCodeAndModulesType($this->moduleCls->getCode(), $this->moduleType);
			Doctrine_Query::create()
				->update('ModulesConfiguration')
				->set('configuration_value', '?', 'True')
				->where('configuration_key = ?', (string) $this->moduleInfo->status_key)
				->andWhere('modules_id = ?', $Module->module_id)
				->execute();
		}
	}

	public function disable(){
		if ($this->moduleCls->isEnabled() === true){
			echo '->findOneByModulesCodeAndModulesType(' . $this->moduleCls->getCode() . ', ' . $this->moduleType . ')' . "\n";
			$Module = Doctrine_Core::getTable('Modules')
				->findOneByModulesCodeAndModulesType($this->moduleCls->getCode(), $this->moduleType);
			print_r($Module->toArray());
			Doctrine_Query::create()
				->update('Modules')
				->set('configuration_value', '?', 'False')
				->where('configuration_key = ?', (string) $this->moduleInfo->status_key)
				->andWhere('modules_id = ?', $Module->module_id)
				->execute();
		}
	}

	public function remove(){
		global $manager;
		if ($this->moduleCls->isInstalled() === true){
			$Module = Doctrine_Core::getTable('Modules')->findOneByModulesCode($this->moduleCls->getCode());
			if ($Module){
				$Module->delete();
				if (is_dir($this->moduleDir . 'Doctrine/')){
					Doctrine_Core::loadModels($this->moduleDir . 'Doctrine/', Doctrine_Core::MODEL_LOADING_AGGRESSIVE);

					$dbConn = $manager->getCurrentConnection();
					$Exporter = $dbConn->export;
					$Importer = $dbConn->import;

					$dir = new DirectoryIterator($this->moduleDir . 'Doctrine/');
					foreach($dir as $dInfo){
						if ($dInfo->isDot()) continue;

						$tableName = substr($dInfo->getBasename(), 0, -4);

						$tableObj = Doctrine_Core::getTable($tableName);
						if ($Importer->tableExists($tableObj->getTableName())){
							try {
								$Exporter->dropTable($tableObj->getTableName());
							}catch (Exception $e){
							}
						}
					}
				}
			}

			/*
							 * @TODO: Remove translated language files for the module
							 */
		}
	}
}
?>