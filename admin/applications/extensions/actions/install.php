<?php
	require(sysConfig::getDirFsCatalog() . 'includes/classes/extensionInstaller.php');
	
	$extension = basename($_GET['ext']);
	$extensionDir = sysConfig::getDirFsCatalog() . 'extensions/' . $extension . '/';
	if (file_exists($extensionDir . 'install/install.php')) {
		$className = $extension . 'Install';
		if (!class_exists($className)){
			include($extensionDir . 'install/install.php');
		}
		$ext = new $className($extension);
		$ext->install();
	}else{
		$installer = new extensionInstaller($extension);
		$installer->install();
	}
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>