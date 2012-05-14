<?php
/*
$imageSource1 = htmlBase::newElement('input')
	->setName('image_source1')
	->addClass('BrowseServerField')
	->setValue((isset($WidgetSettings->image_source1) ? $WidgetSettings->image_source1 : ''));

$imageLink1 = htmlBase::newElement('input')
	->setName('image_link1')
	->setValue((isset($WidgetSettings->image_link1) ? $WidgetSettings->image_link1 : ''));

$imageSource2 = htmlBase::newElement('input')
	->setName('image_source2')
	->addClass('BrowseServerField')
	->setValue((isset($WidgetSettings->image_source2) ? $WidgetSettings->image_source2 : ''));

$imageLink2 = htmlBase::newElement('input')
	->setName('image_link2')
	->setValue((isset($WidgetSettings->image_link2) ? $WidgetSettings->image_link2 : ''));
*/


$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Languages Widget Properties</b>')
	)
));

foreach(sysLanguage::getLanguages() as $lInfo) {

	//$boxContent .= ' <a href="' . itw_app_link(tep_get_all_get_params(array('language', 'currency')) . 'language=' . $lInfo['code']) . '">' . $lInfo['showName']('&nbsp;') . '</a><br>';
	$image_link = 'image_link_' . $lInfo['code'];
	$image_source = 'image_source_' . $lInfo['code'];

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Image Source For ' . $lInfo['name'] . ': '),
			array('text' => htmlBase::newElement('input')
				->setName('image_source_' . $lInfo['code'])
				->addClass('BrowseServerField')
				->setValue((isset($WidgetSettings->$image_source) ? $WidgetSettings->$image_source : ''))
				->draw())
		)
	));
}