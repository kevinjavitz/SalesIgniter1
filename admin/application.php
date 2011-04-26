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