<?php
$selectType = isset($WidgetSettings->type) ? $WidgetSettings->type : '';
$selectText = isset($WidgetSettings->text) ? $WidgetSettings->text : '';
$selectShort = isset($WidgetSettings->short) ? $WidgetSettings->short : '';

$TypeSelect = '<select name="type">
<option value="top" '.(($selectType == 'top')?'selected="selected"':'').'>Top</option>
<option value="bottom" '.(($selectType == 'bottom')?'selected="selected"':'').'>Bottom</option>
<option value="left" '.(($selectType == 'left')?'selected="selected"':'').'>Left</option>
<option value="right" '.(($selectType == 'right')?'selected="selected"':'').'>Right</option>
</select> ';

//$ShortCheckbox = '<input type="checkbox" name="short" value="1"'. ($selectShort === true ? ' checked=checked' : '').'>';


$textArea = htmlBase::newElement('textarea')
	->setName('text')
	->val($selectText)
	->attr('rows', '5')
	->attr('cols', '15');


$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b> Invoice Date Widget Properties</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Line Type:'),
		array('text' => $TypeSelect)
	)
));

/*$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Is Short Date:'),
			array('text' => $ShortCheckbox)
		)
	));*/


$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' => 'Text:'),
			array('text' => $textArea->draw())
		)
));
