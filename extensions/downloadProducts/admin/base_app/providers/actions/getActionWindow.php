<?php
	if (isset($_GET['module'])){
		$moduleCode = $_GET['module'];
	
		if (file_exists(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/' . $moduleCode . '/actionsWindows/' . $_GET['window'] . '.php')){
			require(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/' . $moduleCode . '/actionsWindows/' . $_GET['window'] . '.php');
		}else{
			require(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/admin/base_app/providers/actionsWindows/' . $_GET['window'] . '.php');
		}
	}else{
		require(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/admin/base_app/providers/actionsWindows/' . $_GET['window'] . '.php');
	}
?>