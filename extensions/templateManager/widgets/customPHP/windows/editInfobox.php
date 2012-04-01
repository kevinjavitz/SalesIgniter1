<?php
$selectedText = isset($WidgetSettings->php_text) ? $WidgetSettings->php_text : '';

$PhpText = htmlBase::newElement('textarea')
	->setName('php_text')
	->val($selectedText)
	->attr('rows', '5')
	->attr('cols', '15');

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Custom PHP Block Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'PHP or Html Code:'),
		array('text' => $PhpText->draw())
	)
));
