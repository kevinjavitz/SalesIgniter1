<?php
	$langData = simplexml_load_file(
		$_GET['langDir'] . '/settings.xml',
		'SimpleXMLExtended'
	);
	
	$DateFormatInput = htmlBase::newElement('input')->setName('date_format')->val((string) $langData->date_format);
	$DateFormatShortInput = htmlBase::newElement('input')->setName('date_format_short')->val((string) $langData->date_format_short);
	$DateFormatLongInput = htmlBase::newElement('input')->setName('date_format_long')->val((string) $langData->date_format_long);
	$DateTimeFormatInput = htmlBase::newElement('input')->setName('date_time_format')->val((string) $langData->date_time_format);
	$DefaultCurrencyInput = htmlBase::newElement('input')->setName('default_currency')->val((string) $langData->default_currency);
	$HtmlParamsInput = htmlBase::newElement('input')->setName('html_params')->val((string) $langData->html_params);
	$HtmlCharsetInput = htmlBase::newElement('input')->setName('html_charset')->val((string) $langData->html_charset);
	
	$hiddenInput = htmlBase::newElement('input')
	->setType('hidden')
	->setName('filePath')
	->val($_GET['langDir']);
	
	$Table = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0);
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'Code:' . $hiddenInput->draw()),
			array('text' => (string) $langData->code)
		)
	));
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'Name:'),
			array('text' => (string) $langData->name)
		)
	));
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'Date Format:'),
			array('text' => $DateFormatInput)
		)
	));
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'Date Format Short:'),
			array('text' => $DateFormatShortInput)
		)
	));
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'Date Format Long:'),
			array('text' => $DateFormatLongInput)
		)
	));
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'Datetime Format:'),
			array('text' => $DateTimeFormatInput)
		)
	));
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'Default Currency:'),
			array('text' => $DefaultCurrencyInput)
		)
	));
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'HTML Params:'),
			array('text' => $HtmlParamsInput)
		)
	));
	
	$Table->addBodyRow(array(
		'columns' => array(
			array('text' => 'HTML Charset:'),
			array('text' => $HtmlCharsetInput)
		)
	));
	
	EventManager::attachActionResponse($Table->draw(), 'html');
?>