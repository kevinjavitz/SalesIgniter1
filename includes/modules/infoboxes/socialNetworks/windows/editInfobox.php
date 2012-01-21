<?php


 	$selectedFacebook = (isset($WidgetSettings->facebook)?$WidgetSettings->facebook:'');
   	$selectedTwitter = (isset($WidgetSettings->twitter)?$WidgetSettings->twitter:'');
	$selectedEmail = (isset($WidgetSettings->email)?$WidgetSettings->email:'');

	$linkFacebook = htmlBase::newElement('input')
	->setName('facebook')
	->setValue($selectedFacebook);

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
			array('text' => 'Email Link:'),
			array('text' => $linkEmail->draw())
		)
	));
?>