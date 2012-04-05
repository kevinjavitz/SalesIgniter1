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

$TemplatePages = Doctrine_Core::getTable('TemplatePages');
$TemplatesInfoboxes = Doctrine_Core::getTable('TemplatesInfoboxes');
$TemplatesInfoboxesToTemplates = Doctrine_Core::getTable('TemplatesInfoboxesToTemplates');

error_reporting(0);
$error = '';
$Ftp = new SystemFtp();
$Ftp->connect();
if(isset($_POST['template'])){
	$templates = $_POST['template'];
	foreach($templates as $tplName){
		if(!file_exists(sysConfig::getDirFsCatalog() . 'templates/' . $tplName . '/')){
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
				$file = sysConfig::getDirFsCatalog() . 'templates/' . $tplName . '/installData.php';
				$string = file_get_contents($file);

				preg_match_all("/Configuration\['DIRECTORY'\]->configuration_value = '(.*?)'/ime", $string, $matches);
				if(isset($matches[1])){
					$templateNameOld = $matches[1][0];
					$string = str_replace("->configuration_value = '".$templateNameOld."'", "->configuration_value = '".$tplName."'", $string);
					$string = str_replace("str_replace('".$templateNameOld."'", "str_replace('".$tplName."'", $string);
					$string = str_replace("->configuration_value = '".ucfirst($templateNameOld)."'", "->configuration_value = '".ucfirst($tplName)."'", $string);
					$string = str_replace("/".$templateNameOld."/images", "/".$tplName."/images", $string);
				}

				$Ftp->makeWritable( 'templates/' . $tplName . '/installData.php');
				file_put_contents($file, $string);
				require($file);
				$QProductListing = Doctrine_Query::create()
					->from('ProductsListing')
					->execute();

				foreach($QProductListing as $listingCol){
					$listingCol->products_listing_template = $listingCol->products_listing_template.','.$tplName;
					$listingCol->save();
				}
			}
		}else{
			$error .= 'Directory '.$tplName.' already exists<br/>';

		}
	}
}


if(isset($_POST['templateZip']) && !empty($_POST['templateName'])){
	$templateName = $_POST['templateName'];
	if(!file_exists(sysConfig::getDirFsCatalog(). 'templates/' . $templateName)){
			$Ftp->createDir('templates/' . $templateName);
			$Ftp->makeWritable('images/templates/'.$_POST['templateZip']);
			$Ftp->makeWritable('templates/' . $templateName.'/');
			$Ftp->makeWritable('images/templates/');
			rename(sysConfig::getDirFsCatalog(). 'images/templates/'.$_POST['templateZip'],sysConfig::getDirFsCatalog(). 'templates/' . $templateName . '/' . $_POST['templateZip']);

			$zipFile = sysConfig::getDirFsCatalog() . 'templates/' . $templateName . '/'.$_POST['templateZip'];

			$ZipArchive = new ZipArchive();
			$ZipStatus = $ZipArchive->open($zipFile);
			if ($ZipStatus === true){
				for($i = 0; $i < $ZipArchive->numFiles; $i++){
					$filePath = $ZipArchive->getNameIndex($i);

					$Ftp->copyFile(
						'zip://' . $zipFile . '#' . $filePath,
						'templates/' . $templateName . '/' . $filePath
					);
				}

				$file = sysConfig::getDirFsCatalog() . 'templates/' . $templateName . '/installData.php';
				$string = file_get_contents($file);

				preg_match_all("/Configuration\['DIRECTORY'\]->configuration_value = '(.*?)'/ime", $string, $matches);
				if(isset($matches[1])){
					$templateNameOld = $matches[1][0];
					$string = str_replace("->configuration_value = '".$templateNameOld."'", "->configuration_value = '".$templateName."'", $string);
					$string = str_replace("str_replace('".$templateNameOld."'", "str_replace('".$templateName."'", $string);
					$string = str_replace("->configuration_value = '".ucfirst($templateNameOld)."'", "->configuration_value = '".ucfirst($templateName)."'", $string);
					$string = str_replace("/".$templateNameOld."/images", "/".$templateName."/images", $string);
				}

				$Ftp->makeWritable( 'templates/' . $templateName . '/installData.php');
				file_put_contents($file, $string);

				require($file);
				$QProductListing = Doctrine_Query::create()
					->from('ProductsListing')
					->execute();

				foreach($QProductListing as $listingCol){
					$listingCol->products_listing_template = $listingCol->products_listing_template.','.$templateName;
					$listingCol->save();
				}
			}
	} else{
		$error = 'Directory already exists';
	}

}

EventManager::attachActionResponse(array(
	'success' => true,
	'error' => $error
), 'json');
