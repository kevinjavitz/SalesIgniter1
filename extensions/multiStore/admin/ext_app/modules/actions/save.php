<?php
	if (isset($_POST['store_configuration'])){
		$StoresConfiguration = Doctrine_Core::getTable('StoresConfiguration');
		foreach($_POST['store_configuration'] as $storeId => $config){
			foreach($config as $key => $value){
				if ($_POST['store_show_method'][$storeId] == 'use_global'){
					Doctrine_Query::create()
					->delete('StoresConfiguration')
					->where('configuration_key = ?', $key)
					->andWhere('stores_id = ?', $storeId)
					->execute();
				}else{
					if (is_array($value)){
						$value = implode(',', $value);
					}
		
					$configEntry = $StoresConfiguration->findOneByConfigurationKeyAndStoresId($key, $storeId);
					if (!$configEntry){
						$configEntry = new StoresConfiguration();
						$configEntry->configuration_key = $key;
						$configEntry->stores_id = $storeId;
					}
					$configEntry->configuration_value = $value;
					$configEntry->save();
				}
			}
		}
	}
?>