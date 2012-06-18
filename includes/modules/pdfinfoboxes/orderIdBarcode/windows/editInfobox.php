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

