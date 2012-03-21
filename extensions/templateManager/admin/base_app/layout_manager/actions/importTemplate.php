<?php
function installInfobox($boxPath, $className, $extName = null){
	$moduleDir = sysConfig::getDirFsCatalog() . $boxPath;
	if (is_dir($moduleDir . 'Doctrine/base/')){
		Doctrine_Core::createTablesFromModels($moduleDir . 'Doctrine/base/');
	}

	$className = 'InfoBox' . ucfirst($className);
	if(file_exists($moduleDir . 'infobox.php')){
		if (!class_exists($className)){
			require($moduleDir . 'infobox.php');
		}
		$class = new $className;

		$Infobox = new TemplatesInfoboxes();
		$Infobox->box_code = $class->getBoxCode();
		$Infobox->box_path = $boxPath;
		if (!is_null($extName)){
			$Infobox->ext_name = $extName;
		}
		$Infobox->save();
	}
}

function addLayoutToPage($app, $appPage, $extName, $layoutId){
	global $TemplatePages;

	if (!is_null($extName)){
		$Page = $TemplatePages->findOneByApplicationAndPageAndExtension($app, $appPage, $extName);
	}else{
		$Page = $TemplatePages->findOneByApplicationAndPage($app, $appPage);
	}

	if (!$Page){
		$Page = $TemplatePages->create();
		$Page->layout_id = $layoutId;
		$Page->application = $app;
		$Page->page = $appPage;
		if (!is_null($extName)){
			$Page->extension = $extName;
		}
	}elseif ($Page->count() > 0){
		$Page->layout_id .= ',' . $layoutId;
	}
	$Page->save();
}

$TemplateLayouts = Doctrine_Core::getTable('TemplateLayouts');
$TemplatePages = Doctrine_Core::getTable('TemplatePages');
$TemplatesInfoboxes = Doctrine_Core::getTable('TemplatesInfoboxes');
$TemplatesInfoboxesToTemplates = Doctrine_Core::getTable('TemplatesInfoboxesToTemplates');

$templates = $_POST['template'];

$Ftp = new SystemFtp();
$Ftp->connect();
foreach($templates as $tplName){
	$Download = new CurlDownload(
		'https://' . sysConfig::get('SYSTEM_UPGRADE_SERVER') . '/sesUpgrades/getTemplate.php',
		sysConfig::get('SYSTEM_UPGRADE_USERNAME'),
		sysConfig::get('SYSTEM_UPGRADE_PASSWORD')
	);
	$Download->setRequestData(array(
		'action' => 'process',
		'template' => $tplName,
		'domain' => sysConfig::get('HTTP_HOST'),
		'version' => 1
	));
	$Download->setAuthMethod('post');
	$Download->setLocalFolder(sysConfig::getDirFsCatalog() . 'templates/' . $tplName . '/');
	$Download->setLocalFileName('import.zip');
	$Download->download();

	$zipFile = sysConfig::getDirFsCatalog() . 'templates/' . $tplName . '/import.zip';

	$ZipArchive = new ZipArchive();
	$ZipStatus = $ZipArchive->open($zipFile);
	if ($ZipStatus === true){
		for($i = 0; $i < $ZipArchive->numFiles; $i++){
			$filePath = $ZipArchive->getNameIndex($i);

			$Ftp->copyFile(
				'zip://' . $zipFile . '#' . $filePath,
				'templates/' . $tplName . '/' . $filePath
			);
		}

		require(sysConfig::getDirFsCatalog() . 'templates/' . $tplName . '/installData.php');
	}
}

EventManager::attachActionResponse(array(
	'success' => true
), 'json');
