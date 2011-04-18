<?php
	if ($set == 'payment'){
		$moduleName = $_GET['module'];
		if (!class_exists($moduleName)){
			include(sysConfig::get('DIR_FS_CATALOG') . sysConfig::get('DIR_WS_MODULES') . 'payment/' . $moduleName . '.php');
		}

		$module = new $moduleName();
		$moduleKeys = $module->keys();
	
		Doctrine_Query::create()
		->delete('StoresConfiguration')
		->whereIn('configuration_key', $moduleKeys)
		->execute();
	}
?>