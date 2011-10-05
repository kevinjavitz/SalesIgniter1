<?php
$selectType = isset($WidgetSettings->type) ? $WidgetSettings->type : '';
$selectText = isset($WidgetSettings->text) ? $WidgetSettings->text : '';
$selectOther = isset($WidgetSettings->other) ? $WidgetSettings->other : '';

$selectSameLine = isset($WidgetSettings->sameline) ? $WidgetSettings->sameline : '';

$SamelineCheckbox = '<input type="checkbox" name="sameline" value="1"'. ($selectSameLine === true ? ' checked=checked' : '').'>';


$TypeSelect = '<select name="type">
<option value="left" '.(($selectType == 'left')?'selected="selected"':'').'>Left</option>
<option value="right" '.(($selectType == 'right')?'selected="selected"':'').'>Right</option>
</select> ';

$textOther = htmlBase::newElement('input')
->setName('other')
->val($selectOther);


$textArea = htmlBase::newElement('textarea')
	->setName('text')
	->val($selectText)
	->attr('rows', '5')
	->attr('cols', '15');


$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b> Custom Checkboxes Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Checkbox Type:'),
		array('text' => $TypeSelect)
	)
));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'All on same line:'),
			array('text' => $SamelineCheckbox)
		)
	));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Has Other:'),
			array('text' => $textOther->draw())
		)
	));


$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Options(one per line):'),
			array('text' => $textArea->draw())
		)
));
