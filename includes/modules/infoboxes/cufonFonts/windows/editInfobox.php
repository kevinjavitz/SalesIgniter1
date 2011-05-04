<?php

   	$selectedElements = isset($WidgetSettings->applied_elements)?$WidgetSettings->applied_elements:'';
    $selectedFont = isset($WidgetSettings->applied_font)?$WidgetSettings->applied_font:'';

	$fontElements = htmlBase::newElement('input')
	->setName('applied_elements')
	->setLabel('Elements To apply font to elements:')
	->setValue($selectedElements)
	->setLabelPosition('before');

	$elementFont = htmlBase::newElement('input')
	->setName('applied_font')
	->setLabel('Font Name(the name of js file without extension):')
	->setValue($selectedFont)
	->setLabelPosition('before');

	$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Custom PHP Block Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'PHP or Html Code:'),
		array('text' => $elementFont->draw())
	)
));
$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'PHP or Html Code:'),
		array('text' => $fontElements->draw())
	)
));

?>