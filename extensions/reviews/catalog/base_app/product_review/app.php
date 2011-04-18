<?php
/*
	Reviews Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();
 	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
    $App->addJavascriptFile('ext/jQuery/external/fancybox/jquery.fancybox.js');
	$App->addJavascriptFile('ext/jQuery/external/jqzoom/jquery.jqzoom.js');

	$App->addStylesheetFile('ext/jQuery/external/fancybox/jquery.fancybox.css');
	$App->addStylesheetFile('ext/jQuery/external/jqzoom/jquery.jqzoom.css');

    $pageTabsFolder = sysConfig::getDirFsCatalog() . 'extensions/reviews/catalog/base_app/product_review/pages_tabs/';
	switch($App->getPageName()){
		case 'default':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_DEFAULT'), itw_app_link(tep_get_all_get_params(), 'product_review', 'default'));
			break;
		case 'details':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_INFO_PRODUCT'), itw_app_link(tep_get_all_get_params(), 'product_review', 'details'));
			break;
		case 'write':
			$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE_WRITE_PRODUCT'), itw_app_link(tep_get_all_get_params(), 'product_review', 'write'));
			if($userAccount->isLoggedIn() === false){
				$navigation->set_snapshot();
				tep_redirect(itw_app_link(null, 'account', 'login', 'SSL'));
			}

			break;
	}
?>