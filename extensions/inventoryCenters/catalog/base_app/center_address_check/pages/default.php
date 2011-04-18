<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE');
?></div>
<br />
<div class="main"><?php
	if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_ALLOW_MANUAL_ZONE') == 'True'){
		$setAddressButton = htmlBase::newElement('button')->setType('submit')->usePreset('continue')
		->setId('setLocation')->setName('setLocation')->setText(sysLanguage::get('TEXT_BUTTON_SET_CENTER'));

		$setAddressBox = htmlBase::newElement('contentbox')
		->setHeader(sysLanguage::get('TEXT_CHOOSE_SERVICE_ADDRESS'))
		->addButton($setAddressButton)
		->setButtonBarAlign('right');

		$serviceAreas = getServiceAreas();
		$col = 0;
		foreach($serviceAreas as $areaInfo){
			$setAddressBox->addContentBlock('<span class="contentBoxContentText" style="height:65px;width:33%;">' . 
				'<table cellspacing="0" cellpadding="2">' . 
					'<tr>' . 
						'<td valign="top"><input type="radio" name="serviceArea" value="' . $areaInfo['id'] . '"></td>' . 
						'<td>' . nl2br($areaInfo['address']) . '</td>' . 
					'</tr>' . 
				'</table>' . 
			'</span>');
		
				$col++;
			if ($col > 2){
				$setAddressBox->addContentBlock('<br />');
				$col = 0;
			}
		}
	}

	$checkAddressButton = htmlBase::newElement('button')->setType('submit')->usePreset('continue')
	->setId('checkAddress')->setName('checkAddress')->setText(sysLanguage::get('TEXT_BUTTON_CHECK_ADDRESS'));

	$checkAddressBox = htmlBase::newElement('contentbox')
	->setHeader(sysLanguage::get('TEXT_CHECK_ADDRESS'))
	->addButton($checkAddressButton)
	->setButtonBarAlign('right');

	$checkAddressBox->addContentBlock('<table border="0" cellspacing="2" cellpadding="2" id="addressEntry">' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('ENTRY_STREET_ADDRESS') . '</td>' . 
			'<td>' . tep_draw_input_field('street_address') . '</td>' . 
		'</tr>' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('ENTRY_CITY') . '</td>' . 
			'<td>' . tep_draw_input_field('city') . '</td>' . 
		'</tr>' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('ENTRY_STATE') . '</td>' . 
			'<td id="stateCol">' . tep_draw_input_field('state') . '</td>' . 
		'</tr>' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('ENTRY_POST_CODE') . '</td>' . 
			'<td>' . tep_draw_input_field('postcode') . '</td>' . 
		'</tr>' . 
		'<tr>' . 
			'<td>' . sysLanguage::get('ENTRY_COUNTRY') . '</td>' . 
			'<td>' . tep_get_country_list('country', STORE_COUNTRY, 'id="countryDrop"') . '</td>' . 
		'</tr>' . 
	'</table>');

	$bodyForm = htmlBase::newElement('form')
	->attr('name', 'addressCheck')
	->attr('action', itw_app_link('appExt=inventoryCenters&action=continueShopping', 'center_address_check', 'default'))
	->attr('method', 'post');

	if (isset($setAddressBox)){
		$bodyForm->append($setAddressBox);
	}
	$bodyForm->append($checkAddressBox);
		
	echo $bodyForm->draw();
?></div>