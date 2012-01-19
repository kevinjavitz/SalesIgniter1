<?php


 	$selectedFacebook = (isset($WidgetSettings->facebook)?$WidgetSettings->facebook:'');
   	$selectedTwitter = (isset($WidgetSettings->twitter)?$WidgetSettings->twitter:'');
	$selectedYoutube = (isset($WidgetSettings->youtube)?$WidgetSettings->youtube:'');
	$selectedEmail = (isset($WidgetSettings->email)?$WidgetSettings->email:'');

	$linkFacebook = htmlBase::newElement('input')
	->setName('facebook')
	->setValue($selectedFacebook);

	$linkTwitter = htmlBase::newElement('input')
	->setName('twitter')
	->setValue($selectedTwitter);

	$linkYoutube = htmlBase::newElement('input')
	->setName('youtube')
	->setValue($selectedYoutube);

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
			array('text' => 'Twitter Link:'),
			array('text' => $linkTwitter->draw())
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Youtube Link:'),
			array('text' => $linkYoutube->draw())
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
			array('text' => 'Email Link:'),
			array('text' => $linkEmail->draw())
		)
	));
?>