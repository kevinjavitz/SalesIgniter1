<?php
	class OrderShippingInstaller {
		
		public function __construct($moduleCode, $extName = null){
			$this->moduleDir = sysConfig::getDirFsCatalog();
			if (is_null($extName) === false){
				$this->moduleDir .= 'extensions/' . $extName . '/orderShippingModules/' . $moduleCode . '/';
			}else{
				$this->moduleDir .= 'includes/modules/orderShippingModules/' . $moduleCode . '/';
			}
			$dataDir = $this->moduleDir . 'data/';
			
			$this->configData = simplexml_load_file(
				$dataDir . 'config.xml',
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);

			require($this->moduleDir . 'module.php');
			$className = 'OrderShipping' . ucfirst($moduleCode);
			$this->moduleCls = new $className;
		}
		
		public function install(){
			if ($this->moduleCls->isInstalled() === false){
				if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $this->moduleCls->getCode() . '/Doctrine/')){
					Doctrine_Core::createTablesFromModels(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $this->moduleCls->getCode() . '/Doctrine/');
				}
				
				$moduleConfig = new Modules();
				$moduleConfig->modules_code = $this->moduleCls->getCode();
				$moduleConfig->modules_status = '1';
				$moduleConfig->modules_type = 'order_shipping';
			
				$moduleConfiguration =& $moduleConfig->ModulesConfiguration;
				$k = 0;
				foreach($this->configData->configuration as $cInfo){
					$moduleConfiguration[$k]->configuration_title = (string) $cInfo->title;
					$moduleConfiguration[$k]->configuration_key = (string) $cInfo->key;
					$moduleConfiguration[$k]->configuration_value = (string) $cInfo->value;
					$moduleConfiguration[$k]->configuration_description = (string) $cInfo->description;
					$moduleConfiguration[$k]->sort_order = (string) $cInfo->sort_order;
				
					if (isset($cInfo->use_function)){
						$moduleConfiguration[$k]->use_function = (string) $cInfo->use_function;
					}
				
					if (isset($cInfo->set_function)){
						$moduleConfiguration[$k]->set_function = (string) $cInfo->set_function;
					}
					$k++;
				}
			
				$moduleConfig->save();
				
				/*
				 * @TODO: Translate module language files for installed languages
				 */

				$this->moduleCls->onInstall(&$this, &$moduleConfig);
			}
		}
		
		public function remove(){
			global $manager;
			if ($this->moduleCls->isInstalled() === true){
				$Module = Doctrine_Core::getTable('Modules')->findOneByModulesCode($this->moduleCls->getCode());
				if ($Module){
					$Module->delete();
					if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $this->moduleCls->getCode() . '/Doctrine/')){
						Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $this->moduleCls->getCode() . '/Doctrine/', Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
						
						$dbConn = $manager->getCurrentConnection();
						$Exporter = $dbConn->export;
						$Importer = $dbConn->import;
						
						$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $this->moduleCls->getCode() . '/Doctrine/');
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