<?php

	/* remoteUpdate needs to do its job before show this app */
	EventManager::notify('AccountChangePasswordBeforeBegin');

	$formTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);

	$curPassField = htmlBase::newElement('input')
	->setType('password')
	->setName('password_current')
	->setRequired(true);

	$newPassField = htmlBase::newElement('input')
	->setType('password')
	->setName('password_new')
	->setRequired(true);

	$newPassConfirmField = htmlBase::newElement('input')
	->setType('password')
	->setName('password_confirmation')
	->setRequired(true);

	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_PASSWORD_CURRENT')),
			array('addCls' => 'main', 'text' => $curPassField)
		)
	));

	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_PASSWORD_NEW')),
			array('addCls' => 'main', 'text' => $newPassField)
		)
	));

	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_PASSWORD_CONFIRMATION')),
			array('addCls' => 'main', 'text' => $newPassConfirmField)
		)
	));

	ob_start();
?>
<div class="main" style="margin-top:1em;"><b><?php echo sysLanguage::get('MY_PASSWORD_TITLE'); ?></b><span class="inputRequirement" style="float:right;"><?php echo sysLanguage::get('FORM_REQUIRED_INFORMATION'); ?></span></div>
<div class="ui-widget ui-widget-content ui-corner-all" style="padding:1em;"><?php echo $formTable->draw();?></div>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_PASSWORD');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw() . 
	htmlBase::newElement('button')
	->usePreset('continue')
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'account_password',
		'action' => itw_app_link('action=savePassword', 'account', 'password', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
