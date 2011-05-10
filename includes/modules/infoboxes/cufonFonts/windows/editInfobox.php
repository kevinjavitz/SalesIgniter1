<?php

   	$selectedElements = isset($WidgetSettings->applied_elements)?$WidgetSettings->applied_elements:'';
    $selectedFont = isset($WidgetSettings->applied_font)?$WidgetSettings->applied_font:'';

	$fontElements = htmlBase::newElement('input')
	->setName('applied_elements')
	->setValue($selectedElements)
	->setLabelPosition('before');

	$elementFont = htmlBase::newElement('input')
	->setName('applied_font')
	->setValue($selectedFont)
	->setLabelPosition('before');

	$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => sysLanguage::get('TEXT_INFOBOX_CUFON_HEADING_NAME'))
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_INFOBOX_CUFON_FONT_FILE')),
		array('text' => $elementFont->draw())
	)
));
$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_INFOBOX_CUFON_ELEMENTS')),
		array('text' => $fontElements->draw())
	)
));

?>