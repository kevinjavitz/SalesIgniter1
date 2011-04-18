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
		'text' => 'Reports',
		'link' => false,
		'children' => array()
	);

	if (sysPermissions::adminAccessAllowed('membership', 'billing_report') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'membership', 'billing_report', 'SSL'),
			'text' => sysLanguage::get('BOX_CUSTOMERS_MEMBERSHIP_BILLING_REPORT')
		);
	}
	
	if (sysPermissions::adminAccessAllowed('statistics') === true){
		if (sysPermissions::adminAccessAllowed('statistics', 'customers') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'statistics', 'customers', 'SSL'),
				'text' => 'Customers Orders'
			);
		}
		
		if (sysPermissions::adminAccessAllowed('statistics', 'keywords') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'statistics', 'keywords', 'SSL'),
				'text' => 'Search Keywords'
			);
		}
		
		if (sysPermissions::adminAccessAllowed('statistics', 'monthlySales') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'statistics', 'monthlySales', 'SSL'),
				'text' => 'Monthly Sales'
			);
		}
		
		if (sysPermissions::adminAccessAllowed('statistics', 'purchasedProducts') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'statistics', 'purchasedProducts', 'SSL'),
				'text' => 'Purchased Products'
			);
		}
		
		if (sysPermissions::adminAccessAllowed('statistics', 'recoverCartSales') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'statistics', 'recoverCartSales', 'SSL'),
				'text' => 'Recovered Cart Sales'
			);
		}
		
		if (sysPermissions::adminAccessAllowed('statistics', 'viewedProducts') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'statistics', 'viewedProducts', 'SSL'),
				'text' => 'Products Views'
			);
		}
	}

	EventManager::notify('BoxMarketingAddLink', &$contents);
?>