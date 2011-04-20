<?php
/*
	Sales Igniter E-Commerce System
	Version: 1
	
	I.T. Web Experts
	http://www.itwebexperts.com

	Copyright (c) 2010 I.T. Web Experts

	This script and it's source is not redistributable
*/

	require('includes/application_top.php');
//$Files = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models/yml');
//foreach($Files as $File){
//	rename($Files->getPathname(), str_replace('.php', '.yml', $Files->getPathname()));
//}
	//Doctrine_Core::generateYamlFromModels(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models/models.yml', sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models');
//Doctrine_Core::generateModelsFromYaml(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models/yml/models.yml', sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models/yml/');
//	$Yaml = sfYaml::load(sysConfig::getDirFsCatalog() . 'ext/Doctrine/Models/models.yml');
//$Yaml['AddressBook']['columns']['testing'] = array(
//	'auto_increment' => false,
//	'type' => 'integer(4)',
//	'not_null' => false
//);
//echo '<pre>';print_r($Yaml);
	$action = (isset($_GET['action']) ? $_GET['action'] : '');
	
	$pageFunctionFiles = $App->getFunctionFiles($App->getAppPage());
	if (!empty($pageFunctionFiles)){
		foreach($pageFunctionFiles as $filePath){
			require($filePath);
		}
	}

	require($App->getAppFile());
	
	if (!empty($action)){
		EventManager::notify('ApplicationActionsBeforeExecute', $action);
		
		$actionFiles = $App->getActionFiles($action);
		foreach($actionFiles as $file){
			require($file);
		}
		
		EventManager::notify('ApplicationActionsAfterExecute');
	}
	
	EventManager::notify('ApplicationTemplateBeforeInclude');

	require(sysConfig::getDirFsAdmin() . 'template/' . sysConfig::get('ADMIN_TEMPLATE_NAME') . '/main_page.tpl.php');
	
	EventManager::notify('ApplicationTemplateAfterInclude');

	require(sysConfig::get('DIR_WS_INCLUDES') . 'application_bottom.php');
?>