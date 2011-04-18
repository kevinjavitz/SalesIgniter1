<?php
	$pageTitle = sysLanguage::get('HEADING_TITLE_CREATE_SUCCESS');
	
	$pageContents = sysLanguage::get('TEXT_ACCOUNT_CREATED');
	
	$pageButtons = htmlBase::newElement('button')->usePreset('continue')->setHref($origin_href)->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
