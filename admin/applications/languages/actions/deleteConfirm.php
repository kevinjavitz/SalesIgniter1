<?php
	$loadedModels = Doctrine_Core::getLoadedModelFiles();
 	foreach($loadedModels as $modelName => $modelPath){
 		$Model = Doctrine_Core::getTable($modelName);
 		$RecordInst = $Model->getRecordInstance();
 		if (method_exists($RecordInst, 'deleteLanguageProcess')){
			$RecordInst->deleteLanguageProcess((int) $_GET['lID']);
 		}
 	}

    $languageDir = sysLanguage::getDirectory((int) $_GET['lID']);
    Doctrine_Lib::removeDirectories(sysConfig::getDirFsCatalog() . 'includes/languages/' . $languageDir);
	//echo 'aa'. $LanguageDir;
	/*$path = sysConfig::getDirFsCatalog() . 'includes/languages/' . $languageDir;
    $dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

	for ($dir->rewind(); $dir->valid(); $dir->next()) {
		if ($dir->isDir()) {
			rmdir($dir->getPathname());
		} else {
			unlink($dir->getPathname());
		}
	}
	rmdir($path);*/
    Doctrine_Query::create()
    ->delete('Languages')
    ->where('languages_id = ?', (int) $_GET['lID'])
    ->execute();

    EventManager::attachActionResponse(array(
		'success'  => true
	), 'json');
?>