<?php
$TemplateName = $_POST['templateName'];
$TemplateDirectory = $_POST['templateDirectory'];

if ($TemplateDirectory == 'newDir'){
	$TemplateDirectory = $_POST['templateNewDirectory'];
	$Ftp = new SystemFTP();
	$Ftp->connect();
	$Ftp->checkPath('templates/' . $TemplateDirectory);

	$templateDirRel = 'templates/' . $TemplateDirectory . '/';
	$copyTemplateDir = sysConfig::getDirFsCatalog() . 'extensions/templateManager/admin/template/original/';

	$Dir = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($copyTemplateDir),
		RecursiveIteratorIterator::SELF_FIRST
	);
	foreach($Dir as $dInfo){
		if (!$dInfo->isFile()){
			continue;
		}

		$path = str_replace($copyTemplateDir, '', $dInfo->getPathname());
		$Ftp->copyFile(
			$dInfo->getPathname(),
			$templateDirRel . $path
		);
	}
}

$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->create();
$Template->Configuration['NAME']->configuration_value = $TemplateName;
$Template->Configuration['DIRECTORY']->configuration_value = $TemplateDirectory;
$Template->save();

$json = array(
	'success' => true
);

EventManager::attachActionResponse($json, 'json');
?>