<?php
/*
	Inventory Centers Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	if (Session::exists('post_array') && isset($_GET['is_check_address'])){
		$_POST = array_merge($_POST, Session::get('post_array'));		
		Session::remove('post_array');
	}

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

	Session::set('post_array', $_POST);
	$hiddenField = htmlBase::newElement('input')
	->setType('hidden')
	->setName('is_change_address')
	->setValue('1');

	$pageTitle = sysLanguage::get('HEADING_TITLE');
	$pageContents = $checkAddressBox->draw() . $hiddenField->draw();
	
	$pageButtons = htmlBase::newElement('button')
	->setType('submit')
	->setText(sysLanguage::get('TEXT_BUTTON_RESERVE'))
	->setId('inCart')
	->setName('reserve_now')
	->draw() . 
	htmlBase::newElement('button')
	->setType('submit')
	->usePreset('continue')
	->setId('checkAddress')
	->setName('checkAddress')
	->setText(sysLanguage::get('TEXT_BUTTON_CHECK_ADDRESS'))
	->draw();
	
	$pageContent->set('pageForm', array(
		'name' => 'addressCheck',
		'action' => itw_app_link('appExt=payPerRentals&products_id=' . $_GET['products_id'], 'build_reservation', 'default'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
