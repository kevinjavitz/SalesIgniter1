<?php
	require(DIR_FS_CATALOG . 'includes/classes/extensionInstaller.php');
	
	$extension = basename($_GET['ext']);
	$removeSettings = (isset($_POST['remove']) ? true : false);
	
	$extensionDir = DIR_FS_CATALOG . 'extensions/' . $extension . '/';
	if (file_exists($extensionDir . 'install/install.php')) {
		$className = $extension . 'Install';
		if (!class_exists($className)){
			include($extensionDir . 'install/install.php');
		}
		$ext = new $className;
		$ext->uninstall($removeSettings);
	}else{
		$installer = new extensionInstaller($extension);
		$installer->uninstall($removeSettings);
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>