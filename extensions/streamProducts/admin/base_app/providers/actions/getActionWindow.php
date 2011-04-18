<?php
	if (isset($_GET['module'])){
		$moduleCode = $_GET['module'];
	
		if (file_exists(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $moduleCode . '/actionsWindows/' . $_GET['window'] . '.php')){
			require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $moduleCode . '/actionsWindows/' . $_GET['window'] . '.php');
		}else{
			require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/admin/base_app/providers/actionsWindows/' . $_GET['window'] . '.php');
		}
	}else{
		require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/admin/base_app/providers/actionsWindows/' . $_GET['window'] . '.php');
	}
?>