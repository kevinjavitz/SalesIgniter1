<?php
/*
	Sales Igniter E-Commerce System
	Version: 1.0
	
	I.T. Web Experts
	http://www.itwebexperts.com
	
	Copyright (c) 2010 I.T. Web Experts
	
	This script and its source are not distributable without the written conscent of I.T. Web Experts
*/

	$contents = array(
		'text' => sysLanguage::get('BOX_HEADING_TOOLS'),
		'link' => false,
		'children' => array()
	);

	if (sysPermissions::adminAccessAllowed('mail', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'mail', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_TOOLS_MAIL')
		);
	}
	
	if (sysPermissions::adminAccessAllowed('newsletters', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'newsletters', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_TOOLS_NEWSLETTER_MANAGER')
		);
	}
	
	if (sysPermissions::adminAccessAllowed('server_info', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'server_info', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_TOOLS_SERVER_INFO')
		);
	}
	
	if (sysPermissions::adminAccessAllowed('whos_online', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'whos_online', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_TOOLS_WHOS_ONLINE')
		);
	}
	
	if (sysPermissions::adminAccessAllowed('zones', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'zones', 'default', 'SSL'),
			'text' => 'Google Zones'
		);
	}
	
	if (sysPermissions::adminAccessAllowed('ses_update', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'ses_update', 'default', 'SSL'),
			'text' => sysLanguage::get('TEXT_ADMIN_MENU_SES_UPDATES')
		);
	}
	if (sysPermissions::adminAccessAllowed('database_manager', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'database_manager', 'default'),
			'text' => 'Database Management'
		);
	}

	if (sysPermissions::adminAccessAllowed('index', 'manageFavorites') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'index', 'manageFavorites'),
			'text' => 'Manage Favorites'
		);
	}

	if (sysPermissions::adminAccessAllowed('tools', 'cleardb') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'tools', 'cleardb'),
			'text' => 'Clear Database'
		);
	}
	
	if (sysPermissions::adminAccessAllowed('tools', 'fill_model') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'tools', 'fill_model'),
			'text' => 'Fill Empty Models'
		);
	}
	if (sysPermissions::adminAccessAllowed('tools', 'setTaxable') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'tools', 'setTaxable'),
			'text' => 'Make Products Taxable'
		);
	}

	EventManager::notify('BoxToolsAddLink', &$contents);
if(count($contents['children']) == 0){
	$contents = array();
}
?>