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

$contents = array(
	'text'     => sysLanguage::get('BOX_HEADING_CONFIGURATION'),
	'link'     => false,
	'children' => array()
);

if (
	sysPermissions::adminAccessAllowed('admin_members') === true
){
	$subChildren = array();

	if (sysPermissions::adminAccessAllowed('admin_members', 'default') === true){
		$subChildren[] = array(
			'link' => itw_app_link(null, 'admin_members', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_ADMINISTRATOR_MEMBERS')
		);
	}

	$contents['children'][] = array(
		'text'	 => sysLanguage::get('BOX_HEADING_ADMINISTRATOR'),
		'link'	 => false,
		'children' => $subChildren
	);
}

if (
	sysPermissions::adminAccessAllowed('currencies') === true ||
	sysPermissions::adminAccessAllowed('languages') === true ||
	sysPermissions::adminAccessAllowed('orders_status') === true
){
	$subChildren = array();

	if (sysPermissions::adminAccessAllowed('currencies', 'default') === true){
		$subChildren[] = array(
			'link' => itw_app_link(null, 'currencies', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_LOCALIZATION_CURRENCIES')
		);
	}

	if (sysPermissions::adminAccessAllowed('languages', 'default') === true){
		$subChildren[] = array(
			'link' => itw_app_link(null, 'languages', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_LOCALIZATION_LANGUAGES')
		);
	}

	if (sysPermissions::adminAccessAllowed('address_format', 'default') === true){
		$subChildren[] = array(
			'link' => itw_app_link(null, 'address_format', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_LOCALIZATION_ADDRESS_FORMAT')
		);
	}

	if (sysPermissions::adminAccessAllowed('orders_status', 'default') === true){
		$subChildren[] = array(
			'link' => itw_app_link(null, 'orders_status', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_LOCALIZATION_ORDERS_STATUS')
		);
	}

	$contents['children'][] = array(
		'link'	 => false,
		'text'	 => sysLanguage::get('BOX_HEADING_LOCALIZATION'),
		'children' => $subChildren
	);
}

if (sysPermissions::adminAccessAllowed('configuration') === true){
	$contents['children'][] = array(
		'link'	 => false,
		'text'	 => 'Rental',
		'children' => array(
			array(
				'link' => itw_app_link('key=rentals', 'configuration', 'default', 'SSL'),
				'text' => 'General'
			),
			array(
				'link' => itw_app_link('key=recurringBilling', 'configuration', 'default', 'SSL'),
				'text' => 'Recurring Billing'
			)
		)
	);

	$contents['children'][] = array(
		'link'	 => false,
		'text'	 => 'Technical',
		'children' => array(
			array(
				'link' => itw_app_link('key=coreLogging', 'configuration', 'default', 'SSL'),
				'text' => 'Logging'
			),
			array(
				'link' => itw_app_link('key=coreCache', 'configuration', 'default', 'SSL'),
				'text' => 'Cache'
			),
			array(
				'link' => itw_app_link('key=coreGzip', 'configuration', 'default', 'SSL'),
				'text' => 'Gzip Compression'
			),
			array(
				'link' => itw_app_link('key=coreSessions', 'configuration', 'default', 'SSL'),
				'text' => 'Sessions'
			),
			array(
				'link' => itw_app_link('key=coreErrorReporting', 'configuration', 'default', 'SSL'),
				'text' => 'Error Reporting'
			),
			array(
				'link' => itw_app_link('key=coreGoogleApis', 'configuration', 'default', 'SSL'),
				'text' => 'Google Api\'s'
			),
			array(
				'link' => itw_app_link('key=onePageCheckout', 'configuration', 'default', 'SSL'),
				'text' => 'One Page Checkout'
			),
			array(
				'link' => itw_app_link('key=coreEmail', 'configuration', 'default', 'SSL'),
				'text' => 'E-Mail Options'
			),
			/*array(
				   'link' => itw_app_link('key=coreSeoUrls', 'configuration', 'default', 'SSL'),
				   'text' => 'SEO Urls'
			   ),*/
			array(
				'link' => itw_app_link('key=coreImages', 'configuration', 'default', 'SSL'),
				'text' => 'Images'
			)
		)
	);

	$contents['children'][] = array(
		'link'	 => false,
		'text'	 => 'Downloads/Streaming',
		'children' => array(
			array(
				'link' => itw_app_link('key=coreDownload', 'configuration', 'default', 'SSL'),
				'text' => 'Downloads'
			)
		)
	);

	$subChildren = array(
		array(
			'link' => itw_app_link('key=coreProductListing', 'configuration', 'default', 'SSL'),
			'text' => 'Config Settings'
		)
	);

	if (sysPermissions::adminAccessAllowed('configuration', 'product_listing') === true){
		$subChildren[] = array(
			'link' => itw_app_link(null, 'configuration', 'product_listing', 'SSL'),
			'text' => 'Manage Columns'
		);
	}

	if (sysPermissions::adminAccessAllowed('configuration', 'product_sort_listing') === true){
		$subChildren[] = array(
			'link' => itw_app_link(null, 'configuration', 'product_sort_listing', 'SSL'),
			'text' => 'Manage Sort Columns'
		);
	}

	$contents['children'][] = array(
		'link'	 => false,
		'text'	 => 'Product Listing',
		'children' => $subChildren
	);
}

if (
	sysPermissions::adminAccessAllowed('countries') === true ||
	sysPermissions::adminAccessAllowed('taxes') === true
){
	$menuSection = array(
		'link'	 => false,
		'text'	 => sysLanguage::get('BOX_HEADING_LOCATION_AND_TAXES'),
		'children' => array()
	);

	if (sysPermissions::adminAccessAllowed('countries', 'default') === true){
		$menuSection['children'][] = array(
			'link' => itw_app_link(null, 'countries', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_TAXES_COUNTRIES') . ' / ' . sysLanguage::get('BOX_TAXES_ZONES')
		);
	}

	if (sysPermissions::adminAccessAllowed('taxes') === true){
		$subChildren = array();
		if (sysPermissions::adminAccessAllowed('taxes', 'zones') === true){
			$subChildren[] = array(
				'link' => itw_app_link(null, 'taxes', 'zones', 'SSL'),
				'text' => sysLanguage::get('BOX_TAXES_GEO_ZONES')
			);
		}

		if (sysPermissions::adminAccessAllowed('taxes', 'classes') === true){
			$subChildren[] = array(
				'link' => itw_app_link(null, 'taxes', 'classes', 'SSL'),
				'text' => sysLanguage::get('BOX_TAXES_TAX_CLASSES')
			);
		}

		if (sysPermissions::adminAccessAllowed('taxes', 'rates') === true){
			$subChildren[] = array(
				'link' => itw_app_link(null, 'taxes', 'rates', 'SSL'),
				'text' => sysLanguage::get('BOX_TAXES_TAX_RATES')
			);
		}

		$menuSection['children'][] = array(
			'link'     => false,
			'text'     => 'Taxes',
			'children' => $subChildren
		);
	}
	$contents['children'][] = $menuSection;
}

EventManager::notify('BoxConfigurationAddLink', &$contents);

if (sysPermissions::adminAccessAllowed('configuration', 'default') === true){
	$ignoreGroups = array(
		'rentals', 'recurringBilling', 'coreLogging', 'coreCache', 'coreGzip', 'coreSessions', 'coreErrorReporting',
		'coreGoogleApis', 'onePageCheckout', 'coreEmail', 'coreImages', 'coreDownload', 'coreProductListing'
	);

	$Dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'includes/configs');
	foreach($Dir as $cfgFilePath){
		if ($cfgFilePath->isDot() || $cfgFilePath->isDir()) {
			continue;
		}

		$Configuration = new ConfigurationReader();
		$Configuration->loadConfiguration($cfgFilePath->getPathname(), false);

		if (in_array($Configuration->getKey(), $ignoreGroups)) {
			continue;
		}

		$contents['children'][] = array(
			'link' => itw_app_link('key=' . $Configuration->getKey(), 'configuration', 'default', 'SSL'),
			'text' => $Configuration->getTitle()
		);
	}
}
if (count($contents['children']) == 0){
	$contents = array();
}
?>