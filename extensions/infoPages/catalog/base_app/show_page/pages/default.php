<?php
/*
	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$infoPages = $appExtension->getExtension('infoPages');
	$langId = Session::get('languages_id');

	$pageInfo = $infoPages->getInfoPage((isset($infoPages->pageId) ? (int)$infoPages->pageId : $infoPages->pageKey));
	Session::set('current_app_page', $infoPages->pageKey);
	if ($pageInfo['page_type'] == 'field'){
		$contentHeading = $infoPages->getFieldPageTitle($pageInfo);
		$contentHtml = $infoPages->getFieldPageContent($pageInfo);
	}else{
		$contentHeading = $pageInfo['PagesDescription'][$langId]['pages_title'];
		$contentHtml = $pageInfo['PagesDescription'][$langId]['pages_html_text'];
	}

	if ($infoPages->checkMultiStore === true){
		$storePage = $pageInfo['StoresPages'][Session::get('current_store_id')];
		if ($storePage['show_method'] == 'use_custom'){
			$contentHeading = $storePage['StoresPagesDescription'][$langId]['pages_title'];
			$contentHtml = $storePage['StoresPagesDescription'][$langId]['pages_html_text'];
		}
	}

	$contentHeading = stripslashes($contentHeading);
	$contentHtml = stripslashes($contentHtml);

	/*
	 * @TODO: Fix this, to allow the pageKey to be correct for the url
	 */
	if (isset($_GET['pages_id'])){
		$breadcrumb->add($contentHeading, itw_app_link('pages_id=' . (int)$_GET['pages_id']));
	}else{
		$breadcrumb->add($contentHeading, itw_app_link('appExt=infoPages', 'show_page', $infoPages->pageKey));
	}

	$pageTitle = $contentHeading;
	$pageContents = $contentHtml;
	
	//$pageButtons = htmlBase::newElement('button')
	//->usePreset('continue')
	//->setHref(itw_app_link(null, 'index', 'default'))
	//->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	//$pageContent->set('pageButtons', $pageButtons);

	if (isset($_GET['dialog'])){
		$Template->setPopupMode(true);
	}
