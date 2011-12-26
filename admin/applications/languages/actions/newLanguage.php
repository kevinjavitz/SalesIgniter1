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

$languages = sysLanguage::getGoogleLanguages();

$langCode = $_POST['toLangCode'];
$langName = $languages[$langCode];

$catalogAbsPath = sysConfig::getDirFsCatalog();
$newLangPath = $catalogAbsPath . 'includes/languages/' . strtolower($langName) . '/';
$globalLangPath = $catalogAbsPath . 'includes/languages/english/';

$exclude = array($catalogAbsPath . 'includes/languages');

/*
 * Search all folders for global.xml files
 *
 * @TODO: Update when application page specific language files are created
 */
$Directory = new RecursiveDirectoryIterator($catalogAbsPath);
$Iterator = new RecursiveIteratorIterator($Directory);
$Regex = new RegexIterator($Iterator, '/^.+global\.xml$/i', RegexIterator::GET_MATCH);

updateProgressBar($progressBarName, 'Copying All Language Files');

$files = array();
foreach($Regex as $arr){
	$skipFile = false;
	/*
	 * Exclude any files inside the folders specified in the exclude array
	 */
	foreach($exclude as $excludeDir){
		if (stristr($arr[0], $excludeDir)){
			$skipFile = true;
			break;
		}
	}

	if ($skipFile === false){
		$Ftp->updateFileFromString(
			$newLangPath . str_replace(array($catalogAbsPath, 'language_defines/'), '', $arr[0]),
			sysLanguage::translateFile($arr[0], $langCode, $langName, true)
		);
	}
}

/*
 * Copy the global file for the admin and set the permissions
 */
updateProgressBar($progressBarName, 'Translating File: ' . $newLangPath . 'admin/global.xml');
$Ftp->updateFileFromString(
	$newLangPath . 'admin/global.xml',
	sysLanguage::translateFile($globalLangPath . 'admin/global.xml', $langCode, $langName, true)
);

/*
 * Copy the global file for the catalog and set the permissions
 */
updateProgressBar($progressBarName, 'Translating File: ' . $newLangPath . 'catalog/global.xml');
$Ftp->updateFileFromString(
	$newLangPath . 'catalog/global.xml',
	sysLanguage::translateFile($globalLangPath . 'catalog/global.xml', $langCode, $langName, true)
);

$success = false;
if (is_dir($newLangPath)){
	$success = true;

	updateProgressBar($progressBarName, 'Copying settings file and applying changes');
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

	$Translated = sysLanguage::translateText($langName, $newLang->languages_id);
	$newLang->name_real = $Translated[0];
	$newLang->save();

	if (isset($_POST['translate_model'])){
		foreach($_POST['translate_model'] as $modelName){
			$Model = Doctrine_Core::getTable($modelName);
			$RecordInst = $Model->getRecordInstance();
			if (method_exists($RecordInst, 'newLanguageProcess')){
				updateProgressBar($progressBarName, 'Translating Description Table: ' . $modelName);
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