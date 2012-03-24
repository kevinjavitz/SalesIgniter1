<?php
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


$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Languages Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Image Source: '),
		array('text' => $imageSource1->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Image Link: '),
		array('text' => $imageLink1->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Image Source: '),
		array('text' => $imageSource2->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Image Link: '),
		array('text' => $imageLink2->draw())
	)
));
