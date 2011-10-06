<?php
$selectWebsite = isset($WidgetSettings->website) ? $WidgetSettings->website : '';
$selectEmail = isset($WidgetSettings->email) ? $WidgetSettings->email : '';

$WebsiteCheckbox = '<input type="checkbox" name="website" value="1"'. ($selectWebsite === true ? ' checked=checked' : '').'>';
$EmailCheckbox = '<input type="checkbox" name="email" value="1"'. ($selectEmail === true ? ' checked=checked' : '').'>';

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b> Store Address Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Show Website Address:'),
		array('text' => $WebsiteCheckbox)
	)
));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Show Email:'),
			array('text' => $EmailCheckbox)
		)
));
