<?php
	$shInfo = ReservationUtilities::getShippingDetails((isset($_GET['sh_id']) ? $_GET['sh_id'] : null));

	$contentHtml = "Shipping Description: ".$shInfo['details'];

	$pageTitle = stripslashes($shInfo['title']);
	$pageContents = stripslashes($contentHtml);
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();
	
	$Template->setPopupMode(true);
	$pageContent->set('pageForm', array(
		'name' => 'create_account',
		'action' => itw_app_link('action=createAccount', 'account', 'create', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
