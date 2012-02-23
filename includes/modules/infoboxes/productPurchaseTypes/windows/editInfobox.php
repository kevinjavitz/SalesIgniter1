<?php
$editTable = htmlBase::newElement('table')
	->setCellPadding(2)
	->setCellSpacing(0);

$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="widgetHeader" value="true"' . (isset($WidgetSettings->widgetHeader) && $WidgetSettings->widgetHeader == 'true' ? ' checked=checked' : '') . '> Use Widget Header'),
		)
	));
$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="useQty" value="true"' . (isset($WidgetSettings->useQty) && $WidgetSettings->useQty == 'true' ? ' checked=checked' : '') . '> Use Quantity'),
		)
	));
$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showPrice" value="true"' . (isset($WidgetSettings->showPrice) && $WidgetSettings->showPrice == 'true' ? ' checked=checked' : '') . '> Show Price'),
		)
	));
$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showButton" value="true"' . (isset($WidgetSettings->showButton) && $WidgetSettings->showButton == 'true' ? ' checked=checked' : '') . '> Show Buttons'),
		)
	));

$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showQtyDiscounts" value="true"' . (isset($WidgetSettings->showQtyDiscounts) && $WidgetSettings->showQtyDiscounts == 'true' ? ' checked=checked' : '') . '> Show Qty Discounts Box'),
		)
	));



$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showNew" value="true"' . (isset($WidgetSettings->showNew) && $WidgetSettings->showNew == 'true' ? ' checked=checked' : '') . '> Show New Purchase Type'),
		)
	));
$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showUsed" value="true"' . (isset($WidgetSettings->showUsed) && $WidgetSettings->showUsed == 'true' ? ' checked=checked' : '') . '> Show Used Purchase Type'),
		)
	));
$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showDownload" value="true"' . (isset($WidgetSettings->showDownload) && $WidgetSettings->showDownload == 'true' ? ' checked=checked' : '') . '> Show Download Purchase Type'),
		)
	));
$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showStream" value="true"' . (isset($WidgetSettings->showStream) && $WidgetSettings->showStream == 'true' ? ' checked=checked' : '') . '> Show Stream Purchase Type'),
		)
	));
$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showReservation" value="true"' . (isset($WidgetSettings->showReservation) && $WidgetSettings->showReservation == 'true' ? ' checked=checked' : '') . '> Show Reservation Purchase Type'),
		)
	));
$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<input type="checkbox" name="showRental" value="true"' . (isset($WidgetSettings->showRental) && $WidgetSettings->showRental == 'true' ? ' checked=checked' : '') . '> Show Rental Purchase Type'),
		)
	));



$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Purchase Type Options:'),
			array('text' => $editTable->draw())
		)
	));