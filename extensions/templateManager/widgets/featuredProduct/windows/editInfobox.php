<?php

$thumbnail = htmlBase::newElement('checkbox')
    ->setName('thumbnailImage')
    ->setLabelPosition('before')
    ->setChecked((($WidgetSettings->thumbnailImage == 1)?true:false));


$WidgetSettingsTable->addBodyRow(array(
    'columns' => array(
        array('text' => 'Use Thumbnail Image:'),
        array('text' => $thumbnail->draw())
    )
));

$qtyImages = htmlBase::newElement('input')
    ->setName('produts_qty')
    ->setLabelPosition('before')
    ->val($WidgetSettings->qty);

$WidgetSettingsTable->addBodyRow(array(
    'columns' => array(
        array('text' => 'Featured Products Quantity:'),
        array('text' => $qtyImages->draw())
    )
));

$marginLeft = htmlBase::newElement('input')
    ->setName('marginLeft')
    ->setLabelPosition('before')
    ->val($WidgetSettings->marginLeft);

$WidgetSettingsTable->addBodyRow(array(
    'columns' => array(
        array('text' => 'No thumbnails margin left:'),
        array('text' => $marginLeft->draw())
    )
));

$marginTop = htmlBase::newElement('input')
    ->setName('marginTop')
    ->setLabelPosition('before')
    ->val($WidgetSettings->marginTop);

$WidgetSettingsTable->addBodyRow(array(
    'columns' => array(
        array('text' => 'No thumbnails margin top:'),
        array('text' => $marginTop->draw())
    )
));