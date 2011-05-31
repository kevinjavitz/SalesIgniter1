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
		'text' => sysLanguage::get('BOX_HEADING_MODULES'),
		'link' => false,
		'children' => array()
	);

	if (sysPermissions::adminAccessAllowed('extensions', 'default') === true){
		$extensionPages = array();
		$dir = new DirectoryIterator(DIR_FS_CATALOG . 'extensions/');
		$sorted = array();
		foreach($dir as $fileObj){
			if ($fileObj->isDot() || $fileObj->isFile()) continue;
		
			$sorted[] = $fileObj->getBasename();
		}
		sort($sorted);
	
		$k = 0;
		foreach($sorted as $extensionName){
			$k++;
			$className = 'Extension_' . $extensionName;
			if (!class_exists($className)){
				require(sysConfig::getDirFsCatalog() . 'extensions/' . $extensionName . '/ext.php');
			}
			$classObj = new $className;
		
			$pages = array(
				array(
					'link' => itw_app_link('action=edit&ext=' . $classObj->getExtensionKey(), 'extensions', 'default', 'SSL'),
					'text' => 'Configure'
				)
			);
		
			if (is_dir($classObj->getExtensionDir() . 'admin/base_app/')){
				$extDir = new DirectoryIterator($classObj->getExtensionDir() . 'admin/base_app/');
				foreach($extDir as $extFileObj){
					if ($extFileObj->isDot() === true || $extFileObj->isDir() === false) continue;
					if (file_exists($extFileObj->getPath() . '/' . $extFileObj->getBaseName() . '/.menu_ignore')) continue;

					if (file_exists($extFileObj->getPath() . '/' . $extFileObj->getBaseName() . '/pages/default.php')){
						if (sysPermissions::adminAccessAllowed($extFileObj->getBaseName(), 'default', $classObj->getExtensionKey()) === true){
							$pages[] = array(
								'link' => itw_app_link('appExt=' . $classObj->getExtensionKey(), $extFileObj->getBaseName(), 'default', 'SSL'),
								'text' => ucwords(str_replace('_', ' ', $extFileObj->getBaseName()))
							);
						}
					}
				}
			}
		
			$extensionPages[] = array(
				'link' => itw_app_link('ext=' . $classObj->getExtensionKey(), 'extensions', 'default', 'SSL'),
				'text' => $classObj->getExtensionName(),
				'children' => $pages
			);
			if($k % 7 == 0){
				$contents['children'][] = array(
					'link' => itw_app_link(null, 'extensions', 'default', 'SSL'),
					'text' => 'Extensions'.($k/7),
					'children' => $extensionPages
				);
				unset($pages);
				unset($extensionPages);
			}
		}
		if (isset($extensionPages)){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'extensions', 'default', 'SSL'),
				'text' => 'Extensions'.((int)($k/7)+1),
				'children' => $extensionPages
			);
		}
	}
	
	if (sysPermissions::adminAccessAllowed('modules') === true){
		if (sysPermissions::adminAccessAllowed('modules', 'orderPayment') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'modules', 'orderPayment', 'SSL'),
				'text' => sysLanguage::get('BOX_MODULES_PAYMENT')
			);
		}
		
		if (sysPermissions::adminAccessAllowed('modules', 'orderShipping') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'modules', 'orderShipping', 'SSL'),
				'text' => sysLanguage::get('BOX_MODULES_SHIPPING')
			);
		}
		
		if (sysPermissions::adminAccessAllowed('modules', 'orderTotal') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'modules', 'orderTotal', 'SSL'),
				'text' => sysLanguage::get('BOX_MODULES_ORDER_TOTAL')
			);
		}

		if (sysPermissions::adminAccessAllowed('modules', 'infoboxes') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'modules', 'infoboxes', 'SSL'),
				'text' => sysLanguage::get('BOX_MODULES_INFOBOXES')
			);
		}

		if (sysPermissions::adminAccessAllowed('modules', 'purchaseTypes') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'modules', 'purchaseTypes', 'SSL'),
				'text' => sysLanguage::get('BOX_MODULES_PURCHASETYPES')
			);
		}
	}
	
 	if (sysPermissions::adminAccessAllowed('coupons') === true){
 		$subChildren = array();
		if (sysPermissions::adminAccessAllowed('coupons', 'default') === true){
			$subChildren[] = array(
				'link' => itw_app_link(null, 'coupons', 'default', 'SSL'),
				'text' => sysLanguage::get('BOX_COUPON_ADMIN')
			);
		}
		
		$contents['children'][] = array(
			'link' => false,
			'text' => sysLanguage::get('BOX_HEADING_GV_ADMIN'),
			'children' => $subChildren
		);
 	}

	EventManager::notify('BoxModulesAddLink', &$contents);
?>