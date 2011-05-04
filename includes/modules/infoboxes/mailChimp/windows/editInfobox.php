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
			array('colspan' => 2, 'text' => '<b>Custom MailChimp Properties</b>')
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'API KEY: '),
			array('text' => $linkApi->draw())
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'List ID:'),
			array('text' => $linkList->draw())
		)
	));
?>