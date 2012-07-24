<?php

	$boxID = htmlBase::newElement('input')
	->setName('boxID')
	->setValue((isset($WidgetSettings->boxID) ? $WidgetSettings->boxID : ''));

	$hasHeader = '<input type="checkbox" name="hasHeader" '.(isset($WidgetSettings->hasHeader) && $WidgetSettings->hasHeader == true ? 'checked="checked"': '').'';

	$hasButton = '<input type="checkbox" name="hasButton" '.(isset($WidgetSettings->hasButton) && $WidgetSettings->hasButton == true ? 'checked="checked"': '').'"';

	$showSubmit = '<input type="checkbox" name="showSubmit" '.(isset($WidgetSettings->showSubmit) && $WidgetSettings->showSubmit == true ? 'checked="checked"': '').'"';

	$showShipping = '<input type="checkbox" name="showShipping" '.(isset($WidgetSettings->showShipping) && $WidgetSettings->showShipping == true ? 'checked="checked"': '').'"';

	$hasGeographic = '<input type="checkbox" name="hasGeographic" '.(isset($WidgetSettings->hasGeographic) && $WidgetSettings->hasGeographic == true ? 'checked="checked"': '').'"';

	$hasLaunchPoints = '<input type="checkbox" name="hasLP" '.(isset($WidgetSettings->hasLP) && $WidgetSettings->hasLP == true ? 'checked="checked"': '').'"';
	$hasTimesHeader = '<input type="checkbox" name="hasTimesHeader" '.(isset($WidgetSettings->hasTimesHeader) && $WidgetSettings->hasTimesHeader == true ? 'checked="checked"': '').'"';
	$showQty = '<input type="checkbox" name="showQty" '.(isset($WidgetSettings->showQty) && $WidgetSettings->showQty == true ? 'checked="checked"': '').'"';

	$showTimes = '<input type="checkbox" name="showTimes" '.(isset($WidgetSettings->showTimes) && $WidgetSettings->showTimes == true ? 'checked="checked"': '').'"';

	$showCategories = '<input type="checkbox" name="showCategories" '.(isset($WidgetSettings->showCategories) && $WidgetSettings->showCategories == true ? 'checked="checked"': '').'"';

	$showPickup = '<input type="checkbox" name="showPickup" '.(isset($WidgetSettings->showPickup) && $WidgetSettings->showPickup == true ? 'checked="checked"': '').'"';

	$showDropoff = '<input type="checkbox" name="showDropoff" '.(isset($WidgetSettings->showDropoff) && $WidgetSettings->showDropoff == true ? 'checked="checked"': '').'"';


	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('colspan' => 2, 'text' => sysLanguage::get('INFOBOX_HEADING_PAYPERRENTAL'))
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_BOXID')),
			array('text' => $boxID->draw())
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_HASHEADER')),
			array('text' => $hasHeader)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_SHOWSUBMIT')),
			array('text' => $showSubmit)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_HASBUTTON')),
			array('text' => $hasButton)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_SHOWSHIPPING')),
			array('text' => $showShipping)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_HASGEOGRAPHIC')),
			array('text' => $hasGeographic)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_HASLP')),
			array('text' => $hasLaunchPoints)
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_TIMESHEADER')),
			array('text' => $hasTimesHeader)
		)
	));
	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_SHOWQTY')),
			array('text' => $showQty)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_SHOWTIMES')),
			array('text' => $showTimes)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_SHOWCATEGORIES')),
			array('text' => $showCategories)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_SHOWPICKUP')),
			array('text' => $showPickup)
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('INFOBOX_PAYPERRENTAL_SHOWDROPOFF')),
			array('text' => $showDropoff)
		)
	));


