<?php
	class PurchaseTypeInstaller {
		
		public function __construct($moduleCode, $extName = null){
			$this->moduleDir = sysConfig::getDirFsCatalog();
			if (is_null($extName) === false){
				$this->moduleDir .= 'extensions/' . $extName . '/purchaseTypes/' . $moduleCode . '/';
			}else{
				$this->moduleDir .= 'includes/modules/purchaseTypes/' . $moduleCode . '/';
			}
			$dataDir = $this->moduleDir . 'data/';
			
			$this->configData = simplexml_load_file(
				$dataDir . 'config.xml',
				'SimpleXMLElement',
				LIBXML_NOCDATA
			);

			require($this->moduleDir . 'module.php');
			$className = 'PurchaseType_' . ucfirst($moduleCode);
			$this->moduleCls = new $className;
		}
		
		public function install(){
			if ($this->moduleCls->isInstalled() === false){
				$moduleConfig = new Modules();
				$moduleConfig->modules_code = $this->moduleCls->getCode();
				$moduleConfig->modules_status = '1';
				$moduleConfig->modules_type = 'purchase_type';
			
				$moduleConfiguration =& $moduleConfig->ModulesConfiguration;
				$k = 0;
				foreach($this->configData->configuration as $cInfo){
					$moduleConfiguration[$k]->configuration_title = (string) $cInfo->title;
					$moduleConfiguration[$k]->configuration_key = (string) $cInfo->key;
					$moduleConfiguration[$k]->configuration_value = (string) $cInfo->value;
					$moduleConfiguration[$k]->configuration_description = (string) $cInfo->description;
					//$moduleConfiguration[$k]->sort_order = (string) $cInfo->sort_order;
				
					if (isset($cInfo->use_function)){
						$moduleConfiguration[$k]->use_function = (string) $cInfo->use_function;
					}
				
					if (isset($cInfo->set_function)){
						$moduleConfiguration[$k]->set_function = (string) $cInfo->set_function;
					}
					$k++;
				}

				$Extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions');
				foreach($Extensions as $Ext){
					if ($Ext->isDot() || $Ext->isFile()) continue;

					if (is_dir($Ext->getPathname() . '/purchaseTypes/' . $moduleConfig->modules_code)){
						if (file_exists($Ext->getPathname() . '/purchaseTypes/' . $moduleConfig->modules_code . '/config.xml')){
							$cfgData = simplexml_load_file(
								$Ext->getPathname() . '/purchaseTypes/' . $moduleConfig->modules_code . '/config.xml',
								'SimpleXMLElement',
								LIBXML_NOCDATA
							);
							foreach($cfgData->configuration as $cInfo){
								$moduleConfiguration[$k]->configuration_title = (string) $cInfo->title;
								$moduleConfiguration[$k]->configuration_key = (string) $cInfo->key;
								$moduleConfiguration[$k]->configuration_value = (string) $cInfo->value;
								$moduleConfiguration[$k]->configuration_description = (string) $cInfo->description;
								//$moduleConfiguration[$k]->sort_order = (string) $cInfo->sort_order;

								if (isset($cInfo->use_function)){
									$moduleConfiguration[$k]->use_function = (string) $cInfo->use_function;
								}

								if (isset($cInfo->set_function)){
									$moduleConfiguration[$k]->set_function = (string) $cInfo->set_function;
								}
								$k++;
							}
						}
					}
				}
			
				$moduleConfig->save();
			
				/*
				 * @TODO: Translate module language files for installed languages
				 */
				
				$this->moduleCls->onInstall(&$this, &$moduleConfig);
			}
		}
		
		public function remove(){
			if ($this->moduleCls->isInstalled() === true){
				$Module = Doctrine_Core::getTable('Modules')->findOneByModulesCode($this->moduleCls->getCode());
				if ($Module){
					$Module->delete();
				}
			
				/*
				 * @TODO: Remove translated language files for the module
				 */
			}
		}
	}
?>