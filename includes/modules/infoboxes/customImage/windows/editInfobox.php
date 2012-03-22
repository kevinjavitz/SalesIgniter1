<?php
$htmlCode = (isset($WidgetSettings->image_source) ? $WidgetSettings->image_source : '');
if(sysConfig::getDirWsCatalog() == '/' || (strpos($htmlCode, sysConfig::getDirWsCatalog()) === 0)){
	$imgPath = $htmlCode;
}else{
	$imgPath = sysConfig::getDirWsCatalog() .$htmlCode;
}
$imgPath = str_replace('//','/', $imgPath);

$imageSource = htmlBase::newElement('input')
	->setName('image_source')
	->addClass('BrowseServerField')
	->setValue($imgPath);

$imageLink = htmlBase::newElement('input')
	->setName('image_link')
	->setValue((isset($WidgetSettings->image_link) ? $WidgetSettings->image_link : ''));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Custom Image Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Image Source: '),
		array('text' => $imageSource->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Image Link: '),
		array('text' => $imageLink->draw())
	)
));
