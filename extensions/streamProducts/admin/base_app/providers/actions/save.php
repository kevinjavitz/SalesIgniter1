<?php
	while (list($key, $value) = each($_POST['configuration'])){
		if (is_array($value)){
			$value = implode(',', $value);

			if (substr($value, -1) == ','){
				$value = substr($value, 0, -1);
			}
		}

		Doctrine_Query::create()->update('ModulesConfiguration')
		->set('configuration_value', '?', $value)
		->where('configuration_key = ?', $key)
		->execute();
	}
		
	$moduleCode = $_GET['module'];
	
	if (file_exists(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $moduleCode . '/actions/save.php')){
			require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $moduleCode . '/actions/save.php');
		}
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>