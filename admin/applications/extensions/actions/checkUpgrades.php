<?php
	require(DIR_FS_CATALOG . 'includes/classes/extensionInstaller.php');
	
	$extension = basename($_GET['ext']);
	$installer = new extensionInstaller($extension);
	$response = $installer->checkUpgrades();
	
	if ($response === true){
		if (Session::exists('AllowedUpgrades')){
			Session::append('AllowedUpgrades', ',' . $extension);
		}else{
			Session::set('AllowedUpgrades', $extension);
		}
	}
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>