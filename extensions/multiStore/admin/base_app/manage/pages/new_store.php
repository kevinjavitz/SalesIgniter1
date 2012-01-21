<?php
/*
	Multi Stores Extension Version 1.1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	if (isset($_GET['sID'])){
		$Qstore = Doctrine_Core::getTable('Stores')->findOneByStoresId((int)$_GET['sID']);
	}

	/* Build all store info inputs that are needed --BEGIN-- */
	$storeName = htmlBase::newElement('input')->setName('stores_name');
	$storeDomain = htmlBase::newElement('input')->setName('stores_domain');
	$storeSslDomain = htmlBase::newElement('input')->setName('stores_ssl_domain');
	$storeEmail = htmlBase::newElement('input')->setName('stores_email');
	$storeZip = htmlBase::newElement('input')->setName('stores_zip');
	$storeLocation = htmlBase::newElement('input')->setName('stores_location');

/* Auto Upgrade ( Version 1.0 to 1.1 ) --BEGIN-- */
				$storeOwner = htmlBase::newElement('input')->setName('stores_owner');
/* Auto Upgrade ( Version 1.0 to 1.1 ) --END-- */
			
	if (isset($Qstore)){
		$storeName->setValue($Qstore['stores_name']);
		$storeDomain->setValue($Qstore['stores_domain']);
		$storeSslDomain->setValue($Qstore['stores_ssl_domain']);
		$storeEmail->setValue($Qstore['stores_email']);
		$storeZip->setValue($Qstore['stores_zip']);
		$storeLocation->setValue($Qstore['stores_location']);

/* Auto Upgrade ( Version 1.0 to 1.1 ) --BEGIN-- */
				$storeOwner->setValue($Qstore['stores_owner']);
/* Auto Upgrade ( Version 1.0 to 1.1 ) --END-- */
	}

	$templatesSet = htmlBase::newElement('selectbox')->setName('stores_template');
	$dir = new DirectoryIterator(DIR_FS_CATALOG . 'templates/');
	$ignoreTemplates = array('email', 'help', 'help-text');
	$templatesArray = array();
	foreach($dir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isDir() === false) continue;
		if (in_array(strtolower($fileObj->getBasename()), $ignoreTemplates)) continue;

		$templatesSet->addOption($fileObj->getBasename(), ucfirst($fileObj->getBasename()));
	}
	
	if (isset($Qstore)){
		$templatesSet->selectOptionByValue($Qstore['stores_template']);
	}
	/* Build all store info inputs that are needed --END-- */

	/* Build all categories inputs that are needed --BEGIN-- */
	$checkedCats = array();
	if (isset($Qstore)){
		$Qcategories = Doctrine_Query::create()
		->select('categories_id')
		->from('CategoriesToStores')
		->where('stores_id = ?', $Qstore['stores_id'])
		->execute();
		if ($Qcategories){
			foreach($Qcategories->toArray() as $cInfo){
				$checkedCats[] = $cInfo['categories_id'];
			}
		}
	}
	$categoriesList = tep_get_category_tree_list('0', $checkedCats);
	/* Build all categories inputs that are needed --END-- */
	
	/* Build the store info table --BEGIN-- */
	$storeInfoTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_NAME')),
			array('addCls' => 'main','text' => $storeName->draw())
		)
	));

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_DOMAIN')),
			array('addCls' => 'main','text' => $storeDomain->draw())
		)
	));

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_SSL_DOMAIN')),
			array('addCls' => 'main','text' => $storeSslDomain->draw())
		)
	));

/* Auto Upgrade ( Version 1.0 to 1.1 ) --BEGIN-- */
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_OWNER')),
			array('addCls' => 'main','text' => $storeOwner->draw())
		)
	));
/* Auto Upgrade ( Version 1.0 to 1.1 ) --END-- */

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_EMAIL')),
			array('addCls' => 'main','text' => $storeEmail->draw())
		)
	));

	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_TEMPLATE')),
			array('addCls' => 'main','text' => $templatesSet->draw())
		)
	));
	
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_ZIP')),
			array('addCls' => 'main','text' => $storeZip->draw())
		)
	));
	
	$storeInfoTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main','text' => sysLanguage::get('TEXT_STORES_LOCATION')),
			array('addCls' => 'main','text' => $storeLocation->draw())
		)
	));	
	/* Build the store info table --END-- */

	/* Build the tabbed interface --BEGIN-- */
	$tabsObj = htmlBase::newElement('tabs')
	->setId('storeTabs')
	->addTabHeader('tab_store_info', array('text' => 'Store Info'))
	->addTabPage('tab_store_info', array('text' => $storeInfoTable->draw()))
	->addTabHeader('tab_categories', array('text' => 'Categories'))
	->addTabPage('tab_categories', array('text' => /*'<div style="color:red;">Note: All products inside the categories will be added to this store also.</div><br />' . */$categoriesList));
	/* Build the tabbed interface --END-- */
	
	EventManager::notify('NewStoreAddTab', &$tabsObj);
   
	$saveButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'));

	$buttonContainer = new htmlElement('div');
	$buttonContainer->append($saveButton)->append($cancelButton)->css(array(
		'float' => 'right',
		'width' => 'auto'
	))->addClass('ui-widget');
	
	$pageForm = htmlBase::newElement('form')
	->attr('name', 'new_store')
	->attr('action', itw_app_link(tep_get_all_get_params(array('action')) . 'action=save'))
	->attr('enctype', 'multipart/form-data')
	->attr('method', 'post')
	->html($tabsObj->draw() . '<br />' . $buttonContainer->draw());
	
	$headingTitle = htmlBase::newElement('div')
	->addClass('pageHeading')
	->html(sysLanguage::get('HEADING_TITLE'));
	
	echo $headingTitle->draw() . '<br />' . $pageForm->draw();
?>