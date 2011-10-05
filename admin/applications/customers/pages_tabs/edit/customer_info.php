<?php
	$firstNameInput = htmlBase::newElement('input')
	->setName('customers_firstname')
	->setRequired(true)
	->val($Customer->customers_firstname);
	
	$lastNameInput = htmlBase::newElement('input')
	->setName('customers_lastname')
	->setRequired(true)
	->val($Customer->customers_lastname);
	
	$emailAddressInput = htmlBase::newElement('input')
	->setName('customers_email_address')
	->setRequired(true)
	->val($Customer->customers_email_address);
	
	$streetAddressInput = htmlBase::newElement('input')
	->setName('entry_street_address')
	->setRequired(true)
	->val($Customer->AddressBook[0]->entry_street_address);
	
	$postcodeInput = htmlBase::newElement('input')
	->setName('entry_postcode')
	->setRequired(true)
	->val($Customer->AddressBook[0]->entry_postcode);
	
	$cityInput = htmlBase::newElement('input')
	->setName('entry_city')
	->setRequired(true)
	->val($Customer->AddressBook[0]->entry_city);
	
	$telephoneInput = htmlBase::newElement('input')
	->setName('customers_telephone')
	->val($Customer->customers_telephone);
	
	$faxInput = htmlBase::newElement('input')
	->setName('customers_fax')
	->val($Customer->customers_fax);
	
	$countryInput = htmlBase::newElement('selectbox')->setName('country')
	->setRequired(true)
	->selectOptionByValue($Customer->AddressBook[0]->entry_country_id);
	$countryInput->addOption('', sysLanguage::get('PULL_DOWN_DEFAULT'));
	$countries = tep_get_countries();
	for($i = 0, $n = sizeof($countries); $i < $n; $i++){
		$countryInput->addOption($countries[$i]['id'], $countries[$i]['text']);
	}
	
	$newsletterSet = htmlBase::newElement('radio')->addGroup(array(
		'name' => 'customers_newsletter',
		'checked' => $Customer->customers_newsletter,
		'data' => array(
			array('label' => sysLanguage::get('ENTRY_NEWSLETTER_NO'), 'value' => '0'),
			array('label' => sysLanguage::get('ENTRY_NEWSLETTER_YES'), 'value' => '1')
		)
	));
	
	if (sysConfig::get('ACCOUNT_GENDER') == 'true'){
		$genderSet = htmlBase::newElement('radio')->addGroup(array(
			'name' => 'customers_gender',
			'checked' => $Customer->AddressBook[0]->entry_gender,
			'data' => array(
				array('label' => sysLanguage::get('FEMALE'), 'value' => 'f'),
				array('label' => sysLanguage::get('MALE'), 'value' => 'm')
			)
		));
	}

	if (sysConfig::get('ACCOUNT_STATE') == 'true'){
		$stateInput = htmlBase::newElement('input')
		->setName('entry_state')
		->val($Customer->AddressBook[0]->entry_state);
	}
	
	if (sysConfig::get('ACCOUNT_DOB') == 'true'){
		$dobInput = htmlBase::newElement('input')
		->setName('customers_dob')
		->setId('customers_dob')
		->val(strftime(sysLanguage::getDateFormat('short'),strtotime($Customer->customers_dob)));
	}
	
	if (sysConfig::get('ACCOUNT_COMPANY') == 'true'){
		$companyInput = htmlBase::newElement('input')
		->setName('entry_company')
		->val($Customer->AddressBook[0]->entry_company);
	}

if (sysConfig::get('ACCOUNT_VAT_NUMBER') == 'true'){
	$vatInput = htmlBase::newElement('input')
		->setName('entry_vat')
		->val($Customer->AddressBook[0]->entry_vat);
}

if (sysConfig::get('ACCOUNT_FISCAL_CODE') == 'true'){
	$cifInput = htmlBase::newElement('input')
		->setName('entry_cif')
		->val($Customer->AddressBook[0]->entry_cif);
}

if (sysConfig::get('ACCOUNT_CITY_BIRTH') == 'true'){
	$cityBirthInput = htmlBase::newElement('input')
		->setName('entry_city_birth')
		->val($Customer->AddressBook[0]->entry_city_birth);
}
	
	if (sysConfig::get('ACCOUNT_SUBURB') == 'true'){
		$suburbInput = htmlBase::newElement('input')
		->setName('entry_suburb')
		->val($Customer->AddressBook[0]->entry_suburb);
	}
	
	/*
	 * Build the personal table -- BEGIN
	 */
	$personalTableRows = array(
		1 => array(sysLanguage::get('ENTRY_FIRST_NAME') => $firstNameInput),
		2 => array(sysLanguage::get('ENTRY_LAST_NAME') => $lastNameInput),
		4 => array(sysLanguage::get('ENTRY_EMAIL_ADDRESS') => $emailAddressInput)
	);
	if (isset($genderSet)){
		$personalTableRows[0] = array(sysLanguage::get('ENTRY_GENDER') => $genderSet);
	}
	
	if (isset($dobInput)){
		$personalTableRows[3] = array(sysLanguage::get('ENTRY_DATE_OF_BIRTH') => $dobInput);
	}

	$personalTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	foreach($personalTableRows as $key => $rInfo){
		$cols = each($rInfo);
		
		$personalTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $cols['key']),
				array('addCls' => 'main', 'text' => $cols['value'])
			)
		));
	}
	
	$personalTableContainer = htmlBase::newElement('div')->addClass('ui-widget ui-widget-content ui-corner-all')
	->css(array(
		'padding' => '.5em'
	))->append($personalTable);
	/*
	 * Build the personal table -- END
	 */
	
	/*
	 * Build the company table -- BEGIN
	 */
	if (isset($companyInput)){
		$companyTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	
		$companyTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_COMPANY')),
				array('addCls' => 'main', 'text' => $companyInput)
			)
		));
		$companyTableContainer = htmlBase::newElement('div')->addClass('ui-widget ui-widget-content ui-corner-all')
		->css(array(
			'padding' => '.5em'
		))->append($companyTable);
	}
	/*
	 * Build the company table -- END
	 */

	/*
	 * Build the address table -- BEGIN
	 */
	$addressTableRows = array(
		0 => array(sysLanguage::get('ENTRY_STREET_ADDRESS') => $streetAddressInput),
		2 => array(sysLanguage::get('ENTRY_POST_CODE') => $postcodeInput),
		3 => array(sysLanguage::get('ENTRY_CITY') => $cityInput),
		5 => array(sysLanguage::get('ENTRY_COUNTRY') => $countryInput),
	);
	
	if (isset($suburbInput)){
		$addressTableRows[1] = array(sysLanguage::get('ENTRY_SUBURB') => $suburbInput);
	}
	
	if (isset($stateInput)){
		$addressTableRows[4] = array(sysLanguage::get('ENTRY_STATE') => $stateInput);
	}

if (isset($cifInput)){
	$addressTableRows[6] = array(sysLanguage::get('ENTRY_CIF') => $cifInput);
}
if (isset($vatInput)){
	$addressTableRows[7] = array(sysLanguage::get('ENTRY_VAT') => $vatInput);
}
if (isset($cityBirthInput)){
	$addressTableRows[8] = array(sysLanguage::get('ENTRY_CITY_BIRTH') => $cityBirthInput);
}
	
	$addressTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	foreach($addressTableRows as $key => $rInfo){
		$cols = each($rInfo);
		
		$addressTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $cols['key']),
				array('addCls' => 'main', 'text' => $cols['value'])
			)
		));
	}
	
	$addressTableContainer = htmlBase::newElement('div')->addClass('ui-widget ui-widget-content ui-corner-all')
	->css(array(
		'padding' => '.5em'
	))->append($addressTable);
	/*
	 * Build the address table -- END
	 */
	
	/*
	 * Build the contact table -- BEGIN
	 */
	$contactTableRows = array(
		0 => array(sysLanguage::get('ENTRY_TELEPHONE_NUMBER') => $telephoneInput),
		1 => array(sysLanguage::get('ENTRY_FAX_NUMBER') => $faxInput)
	);
	
	$contactTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	foreach($contactTableRows as $key => $rInfo){
		$cols = each($rInfo);
		
		$contactTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => $cols['key']),
				array('addCls' => 'main', 'text' => $cols['value'])
			)
		));
	}
	
	$contactTableContainer = htmlBase::newElement('div')->addClass('ui-widget ui-widget-content ui-corner-all')
	->css(array(
		'padding' => '.5em'
	))->append($contactTable);
	/*
	 * Build the contact table -- END
	 */
	
	/*
	 * Build the options table -- BEGIN
	 */
	$optionsTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
	
	$optionsTable->addBodyRow(array(
		'columns' => array(
			array('addCls' => 'main', 'text' => sysLanguage::get('ENTRY_NEWSLETTER')),
			array('addCls' => 'main', 'text' => $newsletterSet)
		)
	));
	
	$optionsTableContainer = htmlBase::newElement('div')->addClass('ui-widget ui-widget-content ui-corner-all')
	->css(array(
		'padding' => '.5em'
	))->append($optionsTable);
	/*
	 * Build the options table -- END
	 */
?>
<div class="main" style="font-weight:bold;"><?php echo sysLanguage::get('CATEGORY_PERSONAL');?></div>
<?php echo $personalTableContainer->draw();
/*
    <tr>
     <td class="main">Inventory Center:</td>
     <td class="main"><?php 
      $QinventoryName = tep_db_query('select inventory_center_name from ' . TABLE_PRODUCTS_INVENTORY_CENTERS . ' where inventory_center_id = "' . $cInfo->inventory_center_id . '"');
      $inventoryName = tep_db_fetch_array($QinventoryName);
      echo $inventoryName['inventory_center_name'];
     ?></td>
    </tr>
*/
?>

<?php if (isset($companyInput)){ ?>
<div class="main" style="margin-top:.5em;font-weight:bold;"><?php echo sysLanguage::get('CATEGORY_COMPANY');?></div>
<?php echo $companyTableContainer->draw();?>
<?php } ?>

<div class="main" style="margin-top:.5em;font-weight:bold;"><?php echo sysLanguage::get('CATEGORY_ADDRESS');?></div>
<?php echo $addressTableContainer->draw();?>

<div class="main" style="margin-top:.5em;font-weight:bold;"><?php echo sysLanguage::get('CATEGORY_CONTACT');?></div>
<?php echo $contactTableContainer->draw();?>

<div class="main" style="margin-top:.5em;font-weight:bold;"><?php echo sysLanguage::get('CATEGORY_OPTIONS');?></div>
<?php echo $optionsTableContainer->draw();?>

<?php
	$contents = EventManager::notifyWithReturn('CustomerInfoAddTableContainer', $Customer);
	if (!empty($contents)){
		foreach($contents as $content){
			echo $content;
		}
	}
?>