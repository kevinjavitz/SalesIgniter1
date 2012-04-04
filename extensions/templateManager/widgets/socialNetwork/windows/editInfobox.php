<?php


 	$selectedFacebook = (isset($WidgetSettings->facebook)?$WidgetSettings->facebook:'');
	$selectedGooglePlus = (isset($WidgetSettings->googlePlus)?$WidgetSettings->googlePlus:'');
   	$selectedTwitter = (isset($WidgetSettings->twitter)?$WidgetSettings->twitter:'');
	$selectedLinked = (isset($WidgetSettings->linked)?$WidgetSettings->linked:'');
	$selectedBeforeText = (isset($WidgetSettings->beforeText)?$WidgetSettings->beforeText:'');
	$selectedEmail = (isset($WidgetSettings->email)?$WidgetSettings->email:'');

	$linkFacebook = htmlBase::newElement('input')
	->setName('facebook')
	->setValue($selectedFacebook);

	$linkGooglePlus = htmlBase::newElement('input')
	->setName('googlePlus')
	->setValue($selectedGooglePlus);

	$linkLinked = htmlBase::newElement('input')
	->setName('linked')
	->setValue($selectedLinked);

	$beforeText = htmlBase::newElement('input')
	->setName('beforeText')
	->setValue($selectedBeforeText);

	$linkTwitter = htmlBase::newElement('input')
	->setName('twitter')
	->setValue($selectedTwitter);

	$linkEmail = htmlBase::newElement('input')
	->setName('email')
	->setValue($selectedEmail);

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('colspan' => 2, 'text' => '<b>Social Networks Properties</b>')
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Before Text:'),
			array('text' => $beforeText->draw())
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Twitter Link:'),
			array('text' => $linkTwitter->draw())
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Facebook Link:'),
			array('text' => $linkFacebook->draw())
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Google Plus Link:'),
			array('text' => $linkGooglePlus->draw())
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Linkedin Link:'),
			array('text' => $linkLinked->draw())
		)
	));

	$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Email Link:'),
			array('text' => $linkEmail->draw())
		)
	));
?>