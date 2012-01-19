<?php

   	$selectedElements = isset($WidgetSettings->applied_elements)?$WidgetSettings->applied_elements:'';
    $selectedFont = isset($WidgetSettings->applied_font)?$WidgetSettings->applied_font:'';

	$fontElements = htmlBase::newElement('textarea')
	->setName('applied_elements')
	->html($selectedElements)
	->attr('rows','10')
	->attr('cold','5');

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

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'cufon font text shadow (e.g. 1px 1px #ccc)'),
			array('text' => '<input type="text" name="cufon_text_shadow" value="'. (isset($WidgetSettings->cufon_text_shadow)  ? $WidgetSettings->cufon_text_shadow : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'cufon font text shadow on hover (e.g. 1px 1px #ccc)'),
			array('text' => '<input type="text" name="cufon_text_shadow_hover" value="'. (isset($WidgetSettings->cufon_text_shadow_hover)  ? $WidgetSettings->cufon_text_shadow_hover : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'cufon font hover color'),
			array('text' => '<input type="text" name="cufon_hover_color" value="'. (isset($WidgetSettings->cufon_hover_color)  ? $WidgetSettings->cufon_hover_color : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>' cufon font hover font size'),
			array('text' => '<input type="text" name="cufon_hover_font_size" value="'. (isset($WidgetSettings->cufon_hover_font_size)  ? $WidgetSettings->cufon_hover_font_size : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font hover font weight'),
			array('text' => '<input type="text" name="cufon_hover_font_weight" value="'. (isset($WidgetSettings->cufon_hover_font_weight)  ? $WidgetSettings->cufon_hover_font_weight : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font hover font family'),
			array('text' => '<input type="text" name="cufon_hover_font_family" value="'. (isset($WidgetSettings->cufon_hover_font_family)  ? $WidgetSettings->cufon_hover_font_family : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font hover font style'),
			array('text' => '<input type="text" name="cufon_hover_font_style" value="'. (isset($WidgetSettings->cufon_hover_font_style)  ? $WidgetSettings->cufon_hover_font_style : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font color'),
			array('text' => '<input type="text" name="cufon_color" value="'. (isset($WidgetSettings->cufon_color)  ? $WidgetSettings->cufon_color : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font font family'),
			array('text' => '<input type="text" name="cufon_font_family" value="'. (isset($WidgetSettings->cufon_font_family)  ? $WidgetSettings->cufon_font_family : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font size'),
			array('text' => '<input type="text" name="cufon_font_size" value="'. (isset($WidgetSettings->cufon_font_size)  ? $WidgetSettings->cufon_font_size : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font stretch'),
			array('text' => '<input type="text" name="cufon_font_stretch" value="'. (isset($WidgetSettings->cufon_font_stretch)  ? $WidgetSettings->cufon_font_stretech : '') . '">')
		)
));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'   cufon font style'),
			array('text' => '<input type="text" name="cufon_font_style" value="'. (isset($WidgetSettings->cufon_font_style)  ? $WidgetSettings->cufon_font_style : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'   cufon font weight'),
			array('text' => '<input type="text" name="cufon_font_weight" value="'. (isset($WidgetSettings->cufon_font_weight)  ? $WidgetSettings->cufon_font_weight : '') . '">')
		)
	));


?>