<?php
	$pageTitle = sysLanguage::get('HEADING_TITLE_LOGOFF');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'create_account',
		'action' => itw_app_link('action=createAccount', 'account', 'create', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', sysLanguage::get('TEXT_MAIN_LOGOFF'));
	$pageContent->set('pageButtons', $pageButtons);
