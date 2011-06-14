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
		'text' => sysLanguage::get('BOX_HEADING_CONFIGURATION'),
		'link' => false,
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
			'text'     => sysLanguage::get('BOX_HEADING_ADMINISTRATOR'),
			'link'     => false,
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
			'link'     => false,
			'text'     => sysLanguage::get('BOX_HEADING_LOCALIZATION'),
			'children' => $subChildren
		);
	}

	if (sysPermissions::adminAccessAllowed('configuration') === true){
		$contents['children'][] = array(
			'link'     => false,
			'text'     => 'Rental',
			'children' => array(
				array(
					'link' => itw_app_link('gID=16', 'configuration', 'default', 'SSL'),
					'text' => 'General'
				),
				array(
					'link' => itw_app_link('gID=3333', 'configuration', 'default', 'SSL'),
					'text' => 'Recurring Billing'
				)
			)
		);

		$contents['children'][] = array(
			'link'     => false,
			'text'     => 'Technical',
			'children' => array(
				array(
					'link' => itw_app_link('gID=10', 'configuration', 'default', 'SSL'),
					'text' => 'Logging'
				),
				array(
					'link' => itw_app_link('gID=11', 'configuration', 'default', 'SSL'),
					'text' => 'Cache'
				),
				array(
					'link' => itw_app_link('gID=14', 'configuration', 'default', 'SSL'),
					'text' => 'Gzip Compression'
				),
				array(
					'link' => itw_app_link('gID=15', 'configuration', 'default', 'SSL'),
					'text' => 'Sessions'
				),
				array(
					'link' => itw_app_link('gID=12957', 'configuration', 'default', 'SSL'),
					'text' => 'Error Reporting'
				),
				array(
					'link' => itw_app_link('gID=7575', 'configuration', 'default', 'SSL'),
					'text' => 'One Page Checkout'
				),
				array(
					'link' => itw_app_link('gID=12', 'configuration', 'default', 'SSL'),
					'text' => 'E-Mail Options'
				),
				array(
					'link' => itw_app_link('gID=17', 'configuration', 'default', 'SSL'),
					'text' => 'SEO Urls'
				),
				array(
					'link' => itw_app_link('gID=4', 'configuration', 'default', 'SSL'),
					'text' => 'Images'
				)
			)
		);

		$contents['children'][] = array(
			'link'     => false,
			'text'     => 'Downloads/Streaming',
			'children' => array(
				array(
					'link' => itw_app_link('gID=13', 'configuration', 'default', 'SSL'),
					'text' => 'Downloads'
				),
				array(
					'link' => itw_app_link('gID=12955', 'configuration', 'default', 'SSL'),
					'text' => 'Streaming'
				)
			)
		);
	
		$subChildren = array(
			array(
				'link' => itw_app_link('gID=8', 'configuration', 'default', 'SSL'),
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
			'link'     => false,
			'text'     => 'Product Listing',
			'children' => $subChildren
		);
	}
	
	if (
		sysPermissions::adminAccessAllowed('countries') === true || 
		sysPermissions::adminAccessAllowed('taxes') === true
	){
		$menuSection = array(
			'link'     => false,
			'text'     => sysLanguage::get('BOX_HEADING_LOCATION_AND_TAXES'),
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
				'link' => false,
				'text' => 'Taxes',
				'children' => $subChildren
			);
		}
		$contents['children'][] = $menuSection;
	}
	
	EventManager::notify('BoxConfigurationAddLink', &$contents);

	if (sysPermissions::adminAccessAllowed('configuration', 'default') === true){
		$ignoreGroups = array(12956, 12957, 16, 3333, 10, 11, 14, 15, 13, 12955, 7575, 17, 4, 12, 8);
		$QconfigurationGroups = Doctrine_Query::create()
		->select('configuration_group_id, configuration_group_title')
		->from('ConfigurationGroup')
		->whereNotIn('configuration_group_id', $ignoreGroups)
		->andWhere('visible = ?', '1')
		->orderBy('sort_order')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($QconfigurationGroups){
			foreach($QconfigurationGroups as $group){
				$contents['children'][] = array(
					'link' => itw_app_link('gID=' . $group['configuration_group_id'], 'configuration', 'default', 'SSL'),
					'text' => $group['configuration_group_title']
				);
			}
			unset($group);
			unset($QconfigurationGroups);
		}
	}
?>