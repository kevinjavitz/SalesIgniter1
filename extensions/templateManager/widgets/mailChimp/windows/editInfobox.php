<?php

 	$selectedApi = isset($WidgetSettings->api_key)?$WidgetSettings->api_key:'';
   	$selectedList = isset($WidgetSettings->list_id)?$WidgetSettings->list_id:'';

	$linkApi = htmlBase::newElement('input')
	->setName('api_key')
	->setValue($selectedApi);

	$linkList = htmlBase::newElement('input')
	->setName('list_id')
	->setValue($selectedList);

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('colspan' => 2, 'text' => sysLanguage::get('INFOBOX_MAILCHIMP_TITLE'))
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TEXT_API_KEY')),
			array('text' => $linkApi->draw())
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TEXT_LIST_ID')),
			array('text' => $linkList->draw())
		)
	));
?>