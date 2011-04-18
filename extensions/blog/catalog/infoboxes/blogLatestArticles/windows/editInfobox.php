<?php
$newText = isset($WidgetSettings->nr_art) ? $WidgetSettings->nr_art : '';
//can add category from where to pull articles

$nrArt = htmlBase::newElement('input')
	->setName('nr_art')
	->setValue($newText);

$checkedImage = '';
$checkedDate = '';
$checkedReadMore = '';
$descLength = '200';
if (isset($WidgetSettings->showImage) && $WidgetSettings->showImage == 'showImage'){
	$checkedImage = 'checked="checked"';
}
if (isset($WidgetSettings->showReadMore) && $WidgetSettings->showReadMore == 'showReadMore'){
	$checkedReadMore = 'checked="checked"';
}
if (isset($WidgetSettings->showDate) && $WidgetSettings->showDate == 'showDate'){
	$checkedDate = 'checked="checked"';
}
if (isset($WidgetSettings->descLength) && !empty($WidgetSettings->descLength)){
	$descLength = $WidgetSettings->descLength;
}
ob_start();
?>
<fieldset>
	<legend>Blog Latest Articles Configuration</legend>

	<table cellpadding="0" cellspacing="0" border="0" class="scrollerConfig">

		<tr>
			<td>Show Image:</td>
			<td><input type="checkbox" name="showImage" value="showImage" <?php echo $checkedImage;?>></td>
		</tr>
		<tr>
			<td>Show Date:</td>
			<td><input type="checkbox" name="showDate" value="showDate" <?php echo $checkedDate;?>></td>
		</tr>
		<tr>
			<td>Show Read More:</td>
			<td><input type="checkbox" name="showReadMore" value="showReadMore" <?php echo $checkedReadMore;?>></td>
		</tr>
		<tr>
			<td>Description Length:</td>
			<td><input type="text" name="descLength" value="<?php echo $descLength;?>"></td>
		</tr>
	</table>
</fieldset>
<?php
$Fieldset = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => '<b>Blog Latest Articles</b>')
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => 'Number of Articles:'),
		array('text' => $nrArt->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => $Fieldset)
	)
));
