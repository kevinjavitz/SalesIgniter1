<?php
$selectType = isset($WidgetSettings->type) ? $WidgetSettings->type : '';
$selectText = isset($WidgetSettings->text) ? $WidgetSettings->text : '';
$selectWidth = isset($WidgetSettings->width) ? $WidgetSettings->width : '';

$TypeSelect = '<select name="type">
<option value="top" '.(($selectType == 'top')?'selected="selected"':'').'>Top</option>
<option value="bottom" '.(($selectType == 'bottom')?'selected="selected"':'').'>Bottom</option>
<option value="left" '.(($selectType == 'left')?'selected="selected"':'').'>Left</option>
<option value="right" '.(($selectType == 'right')?'selected="selected"':'').'>Right</option>
</select> ';

$textWidth = htmlBase::newElement('input')
->setName('width')
->val($selectWidth);


$textArea = htmlBase::newElement('textarea')
	->setName('text')
	->val($selectText)
	->attr('rows', '5')
	->attr('cols', '15');


$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b> Custom Line Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Line Type:'),
		array('text' => $TypeSelect)
	)
));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Line Width:'),
			array('text' => $textWidth->draw())
		)
	));


$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Text:'),
			array('text' => $textArea->draw())
		)
));
