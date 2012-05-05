<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

function updateProgressBar($name, $message) {
	$Check = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select name from progress_bar where name="' . $name . '"');
	if (sizeof($Check) > 0){
		$query = 'update progress_bar set message = "' . $message . '" where name = "' . $name . '"';
	}
	else {
		$query = 'insert into progress_bar (message, name) values ("' . $message . '", "' . $name . '")';
	}
	Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->exec($query);
}

$progressBarName = 'newLanguage';

$Ftp = new SystemFTP();
$Ftp->connect();

if (sysConfig::exists('GOOGLE_API_SERVER_KEY') && sysConfig::get('GOOGLE_API_SERVER_KEY') != ''){
	$languages = sysLanguage::getGoogleLanguages();
}
$langCode = (isset($languages) ? $_POST['toLanguage'] : $_POST['toLangCode']);
$langName = (isset($languages) ? $languages[$langCode] : $_POST['toLanguage']);

$catalogAbsPath = sysConfig::getDirFsCatalog();
$newLangPath = $catalogAbsPath . 'includes/languages/' . strtolower($langName) . '/';
$globalLangPath = $catalogAbsPath . 'includes/languages/english/';

$exclude = array($catalogAbsPath . 'includes/languages');

$Directory = new RecursiveDirectoryIterator($catalogAbsPath);
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+global\.xml$/i', RegexIterator::GET_MATCH);


$files = array();
foreach($Regex as $arr){
	$skipFile = false;
	foreach($exclude as $excludeDir){
		if (stristr($arr[0], $excludeDir)){
			$skipFile = true;
			break;
		}
	}


	if ($skipFile === false){
		$newLangNewPath = $newLangPath . str_replace(array($catalogAbsPath, 'language_defines/'), '', $arr[0]);
		if(strpos($arr[0],sysConfig::getDirFsAdmin()) === false){
			$newLangNewPath = $newLangPath .'catalog/'.str_replace(array($catalogAbsPath, 'language_defines/'), '', $arr[0]);

		}
		if(file_exists($newLangNewPath)){
			$Ftp->updateFileFromString(
				$newLangNewPath,
				sysLanguage::translateFileUpdate($arr[0],$newLangNewPath, $langCode, $langName, true)
			);
		}else{
			$Ftp->updateFileFromString(
				$newLangNewPath,
				sysLanguage::translateFile($arr[0], $langCode, $langName, true)
			);
		}
	}
}
if(file_exists($newLangPath . 'admin/global.xml')){
	$Ftp->updateFileFromString(
		$newLangPath . 'admin/global.xml',
		sysLanguage::translateFileUpdate($globalLangPath . 'admin/global.xml', $newLangPath . 'admin/global.xml', $langCode, $langName, true)
	);
}else{
	$Ftp->updateFileFromString(
		$newLangPath . 'admin/global.xml',
		sysLanguage::translateFile($globalLangPath . 'admin/global.xml', $langCode, $langName, true)
	);

}

$isUpdate = false;
if(file_exists($newLangPath . 'catalog/global.xml')){
	$Ftp->updateFileFromString(
		$newLangPath . 'catalog/global.xml',
		sysLanguage::translateFileUpdate($globalLangPath . 'catalog/global.xml',$newLangPath . 'catalog/global.xml', $langCode, $langName, true)
	);
	$isUpdate = true;
}else{
	$Ftp->updateFileFromString(
		$newLangPath . 'catalog/global.xml',
		sysLanguage::translateFile($globalLangPath . 'catalog/global.xml', $langCode, $langName, true)
	);
}

$success = false;
if (is_dir($newLangPath)){
	$success = true;

	$langData = simplexml_load_file(
		$globalLangPath . 'settings.xml',
		'SimpleXMLExtended'
	);

	$langData->name->setCData($langName);
	$langData->code->setCData($langCode);
	$langData->html_params->setCData('dir=ltr lang=' . $langCode);

	$Ftp->updateFileFromString($newLangPath . 'settings.xml', $langData->asXML());

	$newLang = new Languages();
	$newLang->code = $langCode;
	$newLang->name = $langName;
	$newLang->directory = strtolower($langName);

	$newLang->save();

	if (sysConfig::exists('GOOGLE_API_SERVER_KEY') && sysConfig::get('GOOGLE_API_SERVER_KEY') != ''){
		$Translated = sysLanguage::translateText($langName, Session::get('languages_id'), $newLang->languages_id);
		$newLang->name_real = $Translated[0];
	}else{
		$newLang->name_real = $langName;
	}
	$newLang->save();

	if (isset($_POST['translate_model']) && $isUpdate == false){
		foreach($_POST['translate_model'] as $modelName){
			$Model = Doctrine_Core::getTable($modelName);
			$RecordInst = $Model->getRecordInstance();
			if (method_exists($RecordInst, 'newLanguageProcess')){
				//updateProgressBar($progressBarName, 'Translating Description Table: ' . $modelName);
				$RecordInst->newLanguageProcess(Session::get('languages_id'), $newLang->languages_id);
			}
		}
	}
}

EventManager::attachActionResponse(array(
	'success'  => $success,
	'langCode' => $langCode,
	'langDir'  => $newLangPath
), 'json');
?>