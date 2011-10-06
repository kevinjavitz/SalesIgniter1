<?php

	if (!isset($process)) $process = false;

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
		
		if ((isset($gender) && $gender == 'f') || (isset($addressEntry) && $addressEntry['entry_gender'] == 'f')){
			$femaleInput->setChecked(true);
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
	->setName('firstname');
	if (isset($addressEntry)){
		$firstNameInput->setValue($addressEntry['entry_firstname']);
	}

	$lastNameInput = htmlBase::newElement('input')
	->setName('lastname');
	if (isset($addressEntry)){
		$lastNameInput->setValue($addressEntry['entry_lastname']);
	}
	
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
	
	if (sysConfig::get('ACCOUNT_COMPANY') == 'true'){
		$companyInput = htmlBase::newElement('input')
		->setName('company');
		if (isset($addressEntry)){
			$companyInput->setValue($addressEntry['entry_company']);
		}

		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_COMPANY')),
				array('addCls' => 'main', 'text' => $companyInput)
			)
		));
	}

	$streetAddressInput = htmlBase::newElement('input')
	->setName('street_address');
	if (isset($addressEntry)){
		$streetAddressInput->setValue($addressEntry['entry_street_address']);
	}
		
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_STREET_ADDRESS')),
			array('addCls' => 'main', 'text' => $streetAddressInput)
		)
	));
	
	if (sysConfig::get('ACCOUNT_SUBURB') == 'true'){
		$suburbInput = htmlBase::newElement('input')
		->setName('suburb');
		if (isset($addressEntry)){
			$suburbInput->setValue($addressEntry['entry_suburb']);
		}

		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_SUBURB')),
				array('addCls' => 'main', 'text' => $suburbInput)
			)
		));
	}

	if (sysConfig::get('ACCOUNT_FISCAL_CODE') == 'true'){
		$fiscalCodeInput = htmlBase::newElement('input')
		->setName('fiscal_code');
		if (isset($addressEntry)){
			$fiscalCodeInput->setValue($addressEntry['entry_cif']);
		}

		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_FISCAL_CODE')),
				array('addCls' => 'main', 'text' => $fiscalCodeInput)
			)
		));
	}

	if (sysConfig::get('ACCOUNT_VAT_NUMBER') == 'true'){
		$vatInput = htmlBase::newElement('input')
		->setName('vat_number');
		if (isset($addressEntry)){
			$vatInput->setValue($addressEntry['entry_vat']);
		}

		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_VAT_NUMBER')),
				array('addCls' => 'main', 'text' => $vatInput)
			)
		));
	}

	if (sysConfig::get('ACCOUNT_CITY_BIRTH') == 'true'){
		$citybirthInput = htmlBase::newElement('input')
		->setName('city_birth');
		if (isset($addressEntry)){
			$citybirthInput->setValue($addressEntry['entry_city_birth']);
		}

		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_CITY_BIRTH')),
				array('addCls' => 'main', 'text' => $citybirthInput)
			)
		));
	}

	$postCodeInput = htmlBase::newElement('input')
	->setName('postcode');
	if (isset($addressEntry)){
		$postCodeInput->setValue($addressEntry['entry_postcode']);
	}

	$cityInput = htmlBase::newElement('input')
	->setName('city');
	if (isset($addressEntry)){
		$cityInput->setValue($addressEntry['entry_city']);
	}
		
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_POST_CODE')),
			array('addCls' => 'main', 'text' => $postCodeInput)
		)
	));
		
	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_CITY')),
			array('addCls' => 'main', 'text' => $cityInput)
		)
	));
	
	if (sysConfig::get('ACCOUNT_STATE') == 'true'){

		$country = (isset($addressEntry)?$addressEntry['entry_country_id']:sysConfig::get('ONEPAGE_DEFAULT_COUNTRY'));
		$stateInput = htmlBase::newElement('selectbox');
		$Qzones = Doctrine_Query::create()
			->select('zone_name')
			->from('Zones')
			->where('zone_country_id = ?', (int) $country)
			->orderBy('zone_name')
			->execute(array(), Doctrine::HYDRATE_ARRAY);
		foreach ($Qzones as $zInfo) {
			$stateInput->addOption($zInfo['zone_name'], $zInfo['zone_name']);
		}
		if (isset($addressEntry)) {
			$stateInput->selectOptionByValue(tep_get_zone_name($addressEntry['entry_country_id'], $addressEntry['entry_zone_id'], $addressEntry['entry_state']));
		}
		if (count($Qzones) <= 0) {
			$stateInput = htmlBase::newElement('input');
		}
		$editVal = htmlBase::newElement('input')
		->setType('hidden')
		->setValue((isset($_GET['edit'])?$_GET['edit']:-1))
		->attr('id', 'editVal');


		$stateInput->setName('state')
		->attr('id','state');
		
		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_STATE')),
				array('addCls' => 'main', 'text' => $stateInput->draw(). $editVal->draw())
			)
		));
	}

	$formTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_COUNTRY')),
			array('addCls' => 'main', 'text' => tep_get_country_list('country', (isset($addressEntry)?$addressEntry['entry_country_id']:sysConfig::get('ONEPAGE_DEFAULT_COUNTRY'))) . '&nbsp;' . (tep_not_null(sysLanguage::get('ENTRY_COUNTRY_TEXT')) ? '<span class="inputRequirement">' . sysLanguage::get('ENTRY_COUNTRY_TEXT') . '</span>': ''))
		)
	));
	
	if ((isset($_GET['edit']) && ($addressBook->getDefaultAddressId() != $_GET['edit'])) || (isset($_GET['edit']) == false) ){
		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => tep_draw_checkbox_field('primary', 'on', false, 'id="primary"') . ' ' . sysLanguage::get('SET_AS_PRIMARY'), 'attr' => array('colspan' => 2)),
			)
		));
	}

	if ((isset($_GET['edit']) && ($addressBook->getDeliveryDefaultAddressId() != $_GET['edit'])) || (isset($_GET['edit']) == false) ){
		$formTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => tep_draw_checkbox_field('primary_shipping', 'on', false, 'id="primaryShipping"') . ' ' . sysLanguage::get('SET_AS_PRIMARY_SHIPPING'), 'attr' => array('colspan' => 2)),
			)
		));
	}



	
	$pageContents = '<span class="inputRequirement" style="float:right;">' . sysLanguage::get('FORM_REQUIRED_INFORMATION') . '</span>' . $formTable->draw();
	
	echo $pageContents;