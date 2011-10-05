<?php

$selectFirstName = isset($WidgetSettings->firstname) ? $WidgetSettings->firstname : '';
$selectLastName = isset($WidgetSettings->lastname) ? $WidgetSettings->lastname : '';
$selectName = isset($WidgetSettings->name) ? $WidgetSettings->name : '';
$selectFullAddress = isset($WidgetSettings->fulladdress) ? $WidgetSettings->fulladdress : '';
$selectStreetAddress = isset($WidgetSettings->streetaddress) ? $WidgetSettings->streetaddress : '';
$selectPostcode = isset($WidgetSettings->postcode) ? $WidgetSettings->postcode : '';
$selectCity = isset($WidgetSettings->city) ? $WidgetSettings->city : '';
$selectSuburb = isset($WidgetSettings->suburb) ? $WidgetSettings->suburb : '';
$selectState = isset($WidgetSettings->state) ? $WidgetSettings->state : '';
$selectCountry = isset($WidgetSettings->country) ? $WidgetSettings->country : '';
$selectEmail = isset($WidgetSettings->email) ? $WidgetSettings->email : '';
$selectTelephone = isset($WidgetSettings->telephone) ? $WidgetSettings->telephone : '';
$selectGender = isset($WidgetSettings->gender) ? $WidgetSettings->gender : '';
$selectDOB = isset($WidgetSettings->dob) ? $WidgetSettings->dob : '';
$selectCIF = isset($WidgetSettings->cif) ? $WidgetSettings->cif : '';
$selectVAT = isset($WidgetSettings->vat) ? $WidgetSettings->vat : '';
$selectCompany = isset($WidgetSettings->company) ? $WidgetSettings->company : '';

$FirstnameCheckbox = '<input type="checkbox" name="firstname" value="1"'. ($selectFirstName === true ? ' checked=checked' : '').'>';
$LastnameCheckbox = '<input type="checkbox" name="lastname" value="1"'. ($selectLastName === true ? ' checked=checked' : '').'>';
$NameCheckbox = '<input type="checkbox" name="name" value="1"'. ($selectName === true ? ' checked=checked' : '').'>';
$FullAddressCheckbox = '<input type="checkbox" name="fulladdress" value="1"'. ($selectFullAddress === true ? ' checked=checked' : '').'>';
$StreetAddressCheckbox = '<input type="checkbox" name="streetaddress" value="1"'. ($selectStreetAddress === true ? ' checked=checked' : '').'>';
$PostcodeCheckbox = '<input type="checkbox" name="postcode" value="1"'. ($selectPostcode === true ? ' checked=checked' : '').'>';
$CityCheckbox = '<input type="checkbox" name="city" value="1"'. ($selectCity === true ? ' checked=checked' : '').'>';
$SuburbCheckbox = '<input type="checkbox" name="suburb" value="1"'. ($selectSuburb === true ? ' checked=checked' : '').'>';
$StateCheckbox = '<input type="checkbox" name="state" value="1"'. ($selectState === true ? ' checked=checked' : '').'>';
$CountryCheckbox = '<input type="checkbox" name="country" value="1"'. ($selectCountry === true ? ' checked=checked' : '').'>';
$TelephoneCheckbox = '<input type="checkbox" name="telephone" value="1"'. ($selectTelephone === true ? ' checked=checked' : '').'>';
$GenderCheckbox = '<input type="checkbox" name="gender" value="1"'. ($selectGender === true ? ' checked=checked' : '').'>';
$DOBCheckbox = '<input type="checkbox" name="dob" value="1"'. ($selectDOB === true ? ' checked=checked' : '').'>';
$CIFCheckbox = '<input type="checkbox" name="cif" value="1"'. ($selectCIF === true ? ' checked=checked' : '').'>';
$VATCheckbox = '<input type="checkbox" name="vat" value="1"'. ($selectVAT === true ? ' checked=checked' : '').'>';
$CompanyCheckbox = '<input type="checkbox" name="company" value="1"'. ($selectCompany === true ? ' checked=checked' : '').'>';
$EmailCheckbox = '<input type="checkbox" name="email" value="1"'. ($selectEmail === true ? ' checked=checked' : '').'>';

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('colspan' => 2, 'text' => '<b> Customer Information Widget Properties</b>')
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Firstname:'),
			array('text' => $FirstnameCheckbox)
		)
));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Lastname:'),
			array('text' => $LastnameCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Name:'),
			array('text' => $NameCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Full Address:'),
			array('text' => $FullAddressCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Street Address:'),
			array('text' => $StreetAddressCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Postcode:'),
			array('text' => $PostcodeCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show City:'),
			array('text' => $CityCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Suburb:'),
			array('text' => $SuburbCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show State:'),
			array('text' => $StateCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Country:'),
			array('text' => $CountryCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Telephone:'),
			array('text' => $TelephoneCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Gender:'),
			array('text' => $GenderCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Date of birth:'),
			array('text' => $DOBCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show CIF:'),
			array('text' => $CIFCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show VAT:'),
			array('text' => $VATCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Company:'),
			array('text' => $CompanyCheckbox)
		)
	));


$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Email:'),
			array('text' => $EmailCheckbox)
		)
	));
