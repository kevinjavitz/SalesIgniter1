<?php


    $selectedFont = isset($WidgetSettings->applied_font)?$WidgetSettings->applied_font:'';

	$elementFont = htmlBase::newElement('input')
	->setName('applied_font')
	->setValue($selectedFont)
	->setLabelPosition('before');

	$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => sysLanguage::get('TEXT_INFOBOX_GOOGLE_FONTS_HEADING_NAME'))
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_INFOBOX_GOOGLE_FONTS_FONT_FILE')),
		array('text' => $elementFont->draw())
	)
));

?>