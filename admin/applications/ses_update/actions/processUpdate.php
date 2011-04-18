<?php
	if (!isset($_POST['action'])){
		$updateNumber = $_GET['update_num'];
		$updateFile = sysConfig::getDirFsAdmin() . 'applications/ses_update/updates/' . $_GET['update_num'] . '.zip';

		$Download = new CurlDownload(
			'https://' . sysConfig::get('SYSTEM_UPGRADE_SERVER') . '/sesUpgrades/getUpdatesDiff.php',
			sysConfig::get('SYSTEM_UPGRADE_USERNAME'),
			sysConfig::get('SYSTEM_UPGRADE_PASSWORD')
		);
		$Download->setRequestData(array(
			'action' => 'process',
			'update_num' => $updateNumber,
			'domain' => $_SERVER['HTTP_HOST'],
			'version' => 1,
			'last_update' => sysConfig::get('SYSTEM_LAST_UPDATE')
		));
		$Download->setAuthMethod('post');
		$Download->setLocalFolder(sysConfig::getDirFsAdmin() . 'applications/ses_update/updates/');
		$Download->setLocalFileName($updateNumber . '.zip');
		$Download->download();
	}else{
		$updateNumber = str_replace('.zip', '', basename($_POST['curPackage']));
		$updateFile = $_POST['curPackage'];
	}
	
	$DiffFile = new DiffFile($updateFile);
	$DiffFile->processUpdate();

	$json = array('success' => true);
	if ($DiffFile->hasError()){
		$json = array(
			'success' => false,
			'errorInfo' => $DiffFile->getError()
		);
	}
	
	if ($DiffFile->hasFatalError() === false){
		$Update = Doctrine_Core::getTable('SesUpdates')->findOneByUpdateNumber($updateNumber);
		$Update->update_date = date('Y-m-d');
		$Update->update_status = 1;
		$Update->save();
	}
	unset($DiffFile);
	
	EventManager::attachActionResponse($json, 'json');
?>
