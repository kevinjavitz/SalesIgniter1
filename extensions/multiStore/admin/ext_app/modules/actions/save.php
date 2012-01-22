<?php
	if (isset($_POST['store_configuration'])){
		$StoresModulesConfiguration = Doctrine_Core::getTable('StoresModulesConfiguration');
		foreach($_POST['store_configuration'] as $storeId => $config){
			foreach($config as $key => $value){
				if ($_POST['store_show_method'][$storeId] == 'use_global'){
					Doctrine_Query::create()
						->delete('StoresModulesConfiguration')
						->where('configuration_key = ?', $key)
						->andWhere('module_code = ?', $_GET['module'])
						->andWhere('module_type = ?', $_GET['moduleType'])
						->andWhere('store_id = ?', $storeId)
						->execute();
				}else{
					if (is_array($value)){
						$value = implode(',', $value);
					}
		
					$configEntry = $StoresModulesConfiguration->findOneByModuleCodeAndModuleTypeAndConfigurationKeyAndStoreId($_GET['module'], $_GET['moduleType'], $key, $storeId);
					if (!$configEntry){
						$configEntry = new StoresModulesConfiguration();
						$configEntry->module_type = $_GET['moduleType'];
						$configEntry->module_code = $_GET['module'];
						$configEntry->configuration_key = $key;
						$configEntry->store_id = $storeId;
					}
					$configEntry->configuration_value = $value;
					$configEntry->save();
				}
			}
		}
	}
?>