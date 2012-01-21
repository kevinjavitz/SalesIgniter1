<?php
	require(DIR_FS_CATALOG . 'includes/classes/extensionInstaller.php');
	
	$extension = basename($_GET['ext']);
	$installer = new extensionInstaller($extension);
	$response = $installer->runUpgrades();
	
	if ($response === true){
		$messageStack->addSession('pageStack', 'Upgrade Performed Successfully.', 'success');
		Session::remove('AllowedUpgrades');
	}else{
		$messageStack->addSession('pageStack', 'There was an error during upgrade, all changes have been reverted. Please contact I.T. Web Experts about this error.', 'error');
	}
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>