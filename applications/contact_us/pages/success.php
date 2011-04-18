<?php
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
	$pageContent->set('pageContent', sysLanguage::get('TEXT_SUCCESS'));
	$pageContent->set('pageButtons', htmlBase::newElement('button')->usePreset('continue')->setHref(itw_app_link(null, 'index', 'default'))->draw());
