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
		'text' => sysLanguage::get('BOX_HEADING_CUSTOMERS'),
		'link' => false,
		'children' => array()
	);
	
	if (sysPermissions::adminAccessAllowed('customers', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'customers', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_CUSTOMERS_CUSTOMERS')
		);
	}
	
	if (sysPermissions::adminAccessAllowed('orders', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'orders', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_CUSTOMERS_ORDERS')
		);
	}
	
	if (sysPermissions::adminAccessAllowed('label_maker', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'label_maker', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_RENTAL_GENERAL_BATCH_PRINT')
		);
	}

	EventManager::notify('BoxCustomersAddLink', &$contents);
?>