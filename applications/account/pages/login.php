<?php
$tabsArr = array(
	'tabReturning' => array(
		'heading' => sysLanguage::get('HEADING_RETURNING_CUSTOMER'),
		'contentFile' => sysConfig::getDirFsCatalog() . 'applications/account/pages_tabs/login/returning.php'
	)
);

if (sysConfig::get('SHOW_STANDARD_CREATE_ACCOUNT') == 'true'){
	$tabsArr['tabNewAccount'] = array(
		'heading' => sysLanguage::get('HEADING_NEW_CUSTOMER'),
		'contentFile' => sysConfig::getDirFsCatalog() . 'applications/account/pages_tabs/login/customer.php'
	);
}

if (sysConfig::get('ALLOW_RENTALS') == 'true'){
	$tabsArr['tabNewRentAccount'] = array(
		'heading' => sysLanguage::get('HEADING_NEW_RENTAL_CUSTOMER'),
		'contentFile' => sysConfig::getDirFsCatalog() . 'applications/account/pages_tabs/login/rental.php'
	);
}

EventManager::notify('AccountLoginAddTabs', $tabsArr);

$TabsObj = htmlBase::newElement('tabs')
	->setId('tabs');
foreach($tabsArr as $tabId => $tInfo){
	$TabsObj->addTabHeader($tabId, array(
			'text' => $tInfo['heading']
		));

	ob_start();
	include($tInfo['contentFile']);
	$TabsObj->addTabPage($tabId, array(
			'text' => ob_get_contents()
		));
	ob_end_clean();
}

//build the pageContent
$theContent = '';
$beforeTabs = EventManager::notifyWithReturn('LoginBeforeTabs');
if (!empty($beforeTabs)){
	foreach($beforeTabs as $content){
		$theContent .= $content;
	}
}
$theContent .= $TabsObj->draw();

$pageTitle = sysLanguage::get('HEADING_TITLE_LOGIN');
	
$pageContent->set('pageForm', array(
		'name' => 'login',
		'action' => itw_app_link('action=processLogin', 'account', 'login', 'SSL'),
		'method' => 'post'
	));
	
$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $theContent);
