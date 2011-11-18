<?php
	$formTable = htmlBase::newElement('table')->setCellSpacing(0)->setCellPadding(3);
	
	if (sysConfig::get('ACCOUNT_GENDER') == 'true'){
		$maleInput = htmlBase::newElement('radio')
		->setName('gender')
		->setValue('m')
		->setLabel(sysLanguage::get('MALE'));
		
		$femaleInput = htmlBase::newElement('radio')
		->setName('gender')
		->setValue('f')
		->setLabel(sysLanguage::get('FEMALE'));
		
		if ((isset($gender) && $gender == 'f') || ($userAccount->getGender() == 'f')){			$femaleInput->setChecked(true);
		}else{
			$maleInput->setChecked(true);
		}
		
		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_GENDER')),
				array('addCls' => 'main', 'text' => $maleInput->draw() . $femaleInput->draw())
			)
		));
	}

	$firstNameInput = htmlBase::newElement('input')
	->setName('firstname')
	->setValue($userAccount->getFirstName());

	$lastNameInput = htmlBase::newElement('input')
	->setName('lastname')
	->setValue($userAccount->getLastName());
	
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_FIRST_NAME')),
			array('addCls' => 'main', 'text' => $firstNameInput)
		)
	));
		
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_LAST_NAME')),
			array('addCls' => 'main', 'text' => $lastNameInput)
		)
	));
	
	if (sysConfig::get('ACCOUNT_DOB') == 'true') {
		$dobInput = htmlBase::newElement('input')
		->setName('dob')
		->setValue(tep_date_short($userAccount->getDateOfBirth()));
		
		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sprintf(sysLanguage::get('ENTRY_DATE_OF_BIRTH'),'('.str_replace('%Y','yy',str_replace('%m','mm',str_replace('%d','dd',sysLanguage::getDateFormat('short')))).')')),
				array('addCls' => 'main', 'text' => $dobInput->draw())
			)
		));
	}

if (sysConfig::get('ACCOUNT_CITY_BIRTH') == 'true') {
	$cityBirthInput = htmlBase::newElement('input')
		->setName('city_birth')
		->setValue($userAccount->getCityBirth());

	$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_CITY_BIRTH')),
				array('addCls' => 'main', 'text' => $cityBirthInput->draw())
			)
		));
}

	$emailInput = htmlBase::newElement('input')
	->setName('email_address')
	->setValue($userAccount->getEmailAddress());

	$telephoneInput = htmlBase::newElement('input')
	->setName('telephone')
	->setValue($userAccount->getTelephoneNumber());

	$faxInput = htmlBase::newElement('input')
	->setName('fax')
	->setValue($userAccount->getFaxNumber());

	$languageInput = htmlBase::newElement('selectbox')
	->setName('language_id');
	$languages = SysLanguage::getLanguages();
	foreach($languages as $lInfo){
		$languageInput->addOption($lInfo['id'], $lInfo['name']);
	}
	$languageInput->selectOptionByValue($userAccount->getLanguageId());

	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_EMAIL_ADDRESS')),
			array('addCls' => 'main', 'text' => $emailInput)
		)
	));
		
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_TELEPHONE_NUMBER')),
			array('addCls' => 'main', 'text' => $telephoneInput)
		)
	));

	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_FAX_NUMBER')),
			array('addCls' => 'main', 'text' => $faxInput)
		)
	));

	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_LANGUAGE_ID')),
			array('addCls' => 'main', 'text' => $languageInput)
		)
	));
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_EDIT');
	
	$pageContents = '<div class="main">' . 
		'<b>' . 
			sysLanguage::get('MY_ACCOUNT_TITLE') . 
		'</b>' . 
		'<span class="inputRequirement" style="float:right;">' . 
			sysLanguage::get('FORM_REQUIRED_INFORMATION') .
		'</span>' .
	'</div>';
	
	$pageContents .= $formTable->draw();
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->css(array('float' => 'right'))
	->setType('submit')
	->draw() . 
	htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_CREATE');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setType('submit')
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'account_edit',
		'action' => itw_app_link('action=saveAccount', 'account', 'edit', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
