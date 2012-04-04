<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->find((int)$_GET['tID']);
if ($Template){
	$TemplateDir = $Template->Configuration['DIRECTORY']->configuration_value;
	foreach($Template->Layouts as $Layout){
		$TemplatePages = Doctrine_Core::getTable('TemplatePages')->findAll();
		foreach($TemplatePages as $rInfo){
			$pageLayouts = explode(',', $rInfo->layout_id);
			if (in_array($Layout->layout_id, $pageLayouts)){
				foreach($pageLayouts as $idx => $id){
					if ($id == $Layout->layout_id){
						unset($pageLayouts[$idx]);
					}

					if ($id = ''){
						unset($pageLayouts[$idx]);
					}
				}
				$rInfo->layout_id = implode(',', $pageLayouts);
			}
		}
		$TemplatePages->save();
	}
	$Template->delete();

		$Ftp = new SystemFTP();
		$Ftp->connect();

		$Dir = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(sysConfig::getDirFsCatalog() . 'templates/' . $TemplateDir . '/'),
			RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach($Dir as $d){
			if ($d->getBasename() == '.' || $d->getBasename() == '..') {
				continue;
			}

			if ($d->isDir() === true){
				$Ftp->deleteDir(str_replace(sysConfig::getDirFsCatalog(), '', $d->getPathname()));
			}
			else {
				$Ftp->deleteFile(str_replace(sysConfig::getDirFsCatalog(), '', $d->getPathname()));
			}
		}
		$Ftp->deleteDir('templates/' . $TemplateDir);
		$Ftp->disconnect();
}

EventManager::attachActionResponse(array(
	'success' => true
), 'json');
?>