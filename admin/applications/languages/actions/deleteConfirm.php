<?php
	$loadedModels = Doctrine_Core::getLoadedModels();
 	foreach($loadedModels as $modelName){
 		$Model = Doctrine_Core::getTable($modelName);
 		$RecordInst = $Model->getRecordInstance();
 		if (method_exists($RecordInst, 'deleteLanguageProcess')){
			$RecordInst->deleteLanguageProcess((int) $_GET['lID']);
 		}
 	}

    $languageDir = sysLanguage::getDirectory((int) $_GET['lID']);

$ftpConn = ftp_connect(sysConfig::get('SYSTEM_FTP_SERVER'));
if ($ftpConn === false){
	echo 'Error ftp_connect';
}
else {
	$ftpCmd = ftp_login($ftpConn, sysConfig::get('SYSTEM_FTP_USERNAME') , sysConfig::get('SYSTEM_FTP_PASSWORD'));
	if (!$ftpCmd){
		echo 'Error ftp_login';
	}
}



$ftpCmd = ftp_chdir($ftpConn, sysConfig::get('SYSTEM_FTP_PATH'));
if (!$ftpCmd){
	echo 'Error ftp_chdir public_html';
}

	$path = sysConfig::getDirFsCatalog() . 'includes/languages/' . $languageDir;
    $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

	for ($dir->rewind(); $dir->valid(); $dir->next()) {
		if ($dir->isDir()) {
			$fullPath = $dir->getPathname();
			$cleaned = substr($fullPath, strlen(sysConfig::getDirFsCatalog()));
			rmdir($fullPath);
			ftp_rmdir($ftpConn, $cleaned);
		} else {
			$fullPath = $dir->getPathname();
			$cleaned = substr($fullPath, strlen(sysConfig::getDirFsCatalog()));
			unlink($fullPath);
			ftp_delete($ftpConn, $cleaned);
		}
	}
	rmdir($path);
	$cleaned = substr($path, strlen(sysConfig::getDirFsCatalog()));
	ftp_rmdir($ftpConn,$cleaned);

    Doctrine_Query::create()
    ->delete('Languages')
    ->where('languages_id = ?', (int) $_GET['lID'])
    ->execute();

	ftp_close($ftpConn);
    EventManager::attachActionResponse(array(
		'success'  => true
	), 'json');
?>