<?php
ob_start();
$fontOptions = '';
$fontOptionsArr = array();
function getDirectoryList ($directory)
{

	// create an array to hold directory list
	$results = array();

	// create a handler for the directory
	$handler = opendir($directory);

	// open directory and walk through the filenames
	while ($file = readdir($handler)) {

		// if file isn't this directory or its parent, add it to the results
		if ($file != "." && $file != "..") {
			$results[] = $file;
		}

	}

	// tidy up: close the handler
	closedir($handler);

	// done!
	return $results;

}

foreach (getDirectoryList(sysConfig::getDirFsCatalog().'fonts/js/') as $filename){
	$fontOptions .='<option>'.$filename.'</option>';
	$fontOptionsArr[] = $filename;
}
?>
<style>
	#fontSortable { list-style-type: none; margin: 0; padding: 0; }
	#fontSortable li { display:inline-block;vertical-align: top;margin: 3px 3px 3px 0; padding: 1px; width: 350px; font-size: 4em; text-align: left; }
	#fontSortable li div { cursor: move;margin: 0;padding: 3px;border: 1px solid black;background:#ffffff; }
	#fontSortable li .ui-icon-closethick {
		float  : right;
		margin : .5em;
	}

	#fontSortable li select {
		margin-left  : .5em;
		margin-right : .5em;
	}
</style>
<script src="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/ui/jquery.ui.selectmenu.js"></script>
<link rel="stylesheet" href="<?php echo sysConfig::getDirWsCatalog();?>ext/jQuery/themes/smoothness/ui.selectmenu.css" type="text/css" media="screen,projection" />
<script type="text/javascript">
	$(document).ready(function () {
		$('#fontSortable').sortable({
			tolerance: 'pointer',
			placeholder: 'ui-state-highlight',
			forcePlaceholderSize: true
		});

		$('#fontsTable').find('.addMainBlock').click(function () {
			var inputKey = 0;
			while($('#fontSortable > li[data-input_key=' + inputKey + ']').size() > 0){
				inputKey++;
			}

			$('#fontSortable')
				.append('<li id="font_' + inputKey + '" data-input_key="' + inputKey + '">' +
				'<div><table cellpadding="2" cellspacing="0" border="0" width="100%">' +
				'<tr>' +
				'<td valign="top"><table cellpadding="2" cellspacing="0" border="0">' +
				'<tr>' +
				'<td><?php echo 'Font';?></td>' +
				'<td><select name="fontSelect[' + inputKey + ']"><?php echo $fontOptions;?></select></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Font Family';?></td>' +
				'<td><input name="fontFamily[' + inputKey + ']"></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Font Applied Elements';?></td>' +
				'<td><textarea cols="5" rows="2" name="fontElements[' + inputKey + ']"></textarea></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Font Size';?></td>' +
				'<td><input name="fontSize[' + inputKey + ']"></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Font Color';?></td>' +
				'<td><input name="fontColor[' + inputKey + ']"></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Text Shadow(#333 1px 1px, #fff -1px -1px)';?></td>' +
				'<td><input name="fontShadow[' + inputKey + ']"></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Hover Font Family';?></td>' +
				'<td><input name="fontFamilyHover[' + inputKey + ']"></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Hover Font Size';?></td>' +
				'<td><input name="fontSizeHover[' + inputKey + ']"></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Hover Font Color';?></td>' +
				'<td><input name="fontColorHover[' + inputKey + ']"></td>' +
				'</tr>' +
				'<tr>' +
				'<td><?php echo 'Hover Text Shadow(#333 1px 1px, #fff -1px -1px)';?></td>' +
				'<td><input name="fontShadowHover[' + inputKey + ']"></td>' +
				'</tr>' +
				'</table></td>' +
				'<td valign="top">' +
				'<span class="ui-icon ui-icon-closethick fontDelete" tooltip="Delete Font"></span>' +
				'</td>' +
				'</tr>' +
				'</table></div>' +
				'</li>');
			$('#fontSortable').sortable('refresh');
		});



		$('.fontDelete').live('click', function () {
			$(this).parentsUntil('ol').last().remove();
		});

		$('.saveButton').click(function () {
			$('input[name=fontSortable]').val($('#fontSortable').sortable('serialize'));
		});
	});
</script>
<?php
$editTable = htmlBase::newElement('table')
	->setId('fontsTable')
	->setCellPadding(2)
	->setCellSpacing(0);

$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<b>Fonts</b>')
		)
	));

$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<span class="ui-icon ui-icon-plusthick addMainBlock"></span><span class="ui-icon ui-icon-closethick"></span>')
		)
	));


function parseFont($item, &$i) {
	global $fontOptionsArr;

	$textInputs = '<table cellpadding="2" cellspacing="0" border="0">';

	$textInput = htmlBase::newElement('selectbox')
			->setName('fontSelect[' . $i . ']')
			->selectOptionByValue((isset($item->font) ? $item->font : ''));
	foreach($fontOptionsArr as $iFont){
		$textInput->addOption($iFont, $iFont);
	}

	$textInputs .= '<tr>' .
			'<td>' . 'Font'. '</td>' .
			'<td>' . $textInput->draw() . '</td>' .
			'</tr>';

	$textInput2 = htmlBase::newElement('input')
	->setName('fontFamily[' . $i . ']')
	->setValue((isset($item->fontFamily) ? $item->fontFamily : ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Font Family'. '</td>' .
		'<td>' . $textInput2->draw() . '</td>' .
		'</tr>';

	$textInput21 = htmlBase::newElement('textarea')
	->setName('fontElements[' . $i . ']')
	->attr('rows','2')
	->attr('cols', 5)
	->html((isset($item->fontElements) ? $item->fontElements: ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Font Applied Elements'. '</td>' .
		'<td>' . $textInput21->draw() . '</td>' .
		'</tr>';

	$textInput3 = htmlBase::newElement('input')
		->setName('fontSize[' . $i . ']')
		->setValue((isset($item->fontSize) ? $item->fontSize : ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Font Size'. '</td>' .
		'<td>' . $textInput3->draw() . '</td>' .
		'</tr>';

	$textInput4 = htmlBase::newElement('input')
		->setName('fontColor[' . $i . ']')
		->setValue((isset($item->fontColor) ? $item->fontColor : ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Font Color'. '</td>' .
		'<td>' . $textInput4->draw() . '</td>' .
		'</tr>';

	$textInput5 = htmlBase::newElement('input')
		->setName('fontShadow[' . $i . ']')
		->setValue((isset($item->fontShadow) ? $item->fontShadow : ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Text Shadow(#333 1px 1px, #fff -1px -1px)'. '</td>' .
		'<td>' . $textInput5->draw() . '</td>' .
		'</tr>';

	$textInput311 = htmlBase::newElement('input')
		->setName('fontFamilyHover[' . $i . ']')
		->setValue((isset($item->fontFamilyHover) ? $item->fontFamilyHover : ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Hover Font Family'. '</td>' .
		'<td>' . $textInput311->draw() . '</td>' .
		'</tr>';

	$textInput31 = htmlBase::newElement('input')
		->setName('fontSizeHover[' . $i . ']')
		->setValue((isset($item->fontSizeHover) ? $item->fontSizeHover : ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Hover Font Size'. '</td>' .
		'<td>' . $textInput31->draw() . '</td>' .
		'</tr>';

	$textInput41 = htmlBase::newElement('input')
		->setName('fontColorHover[' . $i . ']')
		->setValue((isset($item->fontColorHover) ? $item->fontColorHover : ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Hover Font Color'. '</td>' .
		'<td>' . $textInput41->draw() . '</td>' .
		'</tr>';

	$textInput51 = htmlBase::newElement('input')
		->setName('fontShadowHover[' . $i . ']')
		->setValue((isset($item->fontShadowHover) ? $item->fontShadowHover : ''));

	$textInputs .= '<tr>' .
		'<td>' . 'Hover Text Shadow(#333 1px 1px, #fff -1px -1px)'. '</td>' .
		'<td>' . $textInput51->draw() . '</td>' .
		'</tr>';


	$textInputs .= '</table>';

	$itemTemplate = '<li id="font_' . $i . '" data-input_key="' . $i . '">' .
		'<div><table cellpadding="2" cellspacing="0" border="0" width="100%">' .
		'<tr>' .
		'<td valign="top">' . $textInputs . '</td>' .
		'<td valign="top"><span class="ui-icon ui-icon-closethick fontDelete" tooltip="Delete Font"></span></td>' .
		'</tr>' .
		'<tr>' .
		'<td valign="top" colspan="2"><table cellpadding="2" cellspacing="0" border="0" width="100%">';

	$itemTemplate .= '</table></td></tr></table></div>';

	$i++;

	$itemTemplate .= '</li>';

	return $itemTemplate;
}

$Fonts = '';
//echo 'aa'.print_r($WidgetSettings->fonts);
if (isset($WidgetSettings->fonts)){
	$i = 0;
	foreach($WidgetSettings->fonts as $iInfo){
		$Fonts .= parseFont($iInfo, &$i);
	}
}

$editTable->addBodyRow(array(
		'columns' => array(
			array('text' => '<ol id="fontSortable" class="ui-widget sortable">' . $Fonts . '</ol>')
		)
	));

echo $editTable->draw();
echo '<input type="hidden" name="fontSortable" value="">';
$fileContent = ob_get_contents();
ob_end_clean();

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array(
				'colspan' => 2,
				'text'    => $fileContent
			)
		)
	));

  ?>


<?php
 /*
   	$selectedElements = isset($WidgetSettings->applied_elements)?$WidgetSettings->applied_elements:'';
    $selectedFont = isset($WidgetSettings->applied_font)?$WidgetSettings->applied_font:'';

	$fontElements = htmlBase::newElement('textarea')
	->setName('applied_elements')
	->html($selectedElements)
	->attr('rows','10')
	->attr('cold','5');

	$elementFont = htmlBase::newElement('input')
	->setName('applied_font')
	->setValue($selectedFont)
	->setLabelPosition('before');

	$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('colspan' => 2, 'text' => sysLanguage::get('TEXT_INFOBOX_CUFON_HEADING_NAME'))
	)
));

$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_INFOBOX_CUFON_FONT_FILE')),
		array('text' => $elementFont->draw())
	)
));
$WidgetSettingsTable->addBodyRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TEXT_INFOBOX_CUFON_ELEMENTS')),
		array('text' => $fontElements->draw())
	)
));

$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'cufon font text shadow (e.g. 1px 1px #ccc)'),
			array('text' => '<input type="text" name="cufon_text_shadow" value="'. (isset($WidgetSettings->cufon_text_shadow)  ? $WidgetSettings->cufon_text_shadow : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'cufon font text shadow on hover (e.g. 1px 1px #ccc)'),
			array('text' => '<input type="text" name="cufon_text_shadow_hover" value="'. (isset($WidgetSettings->cufon_text_shadow_hover)  ? $WidgetSettings->cufon_text_shadow_hover : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'cufon font hover color'),
			array('text' => '<input type="text" name="cufon_hover_color" value="'. (isset($WidgetSettings->cufon_hover_color)  ? $WidgetSettings->cufon_hover_color : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>' cufon font hover font size'),
			array('text' => '<input type="text" name="cufon_hover_font_size" value="'. (isset($WidgetSettings->cufon_hover_font_size)  ? $WidgetSettings->cufon_hover_font_size : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font hover font weight'),
			array('text' => '<input type="text" name="cufon_hover_font_weight" value="'. (isset($WidgetSettings->cufon_hover_font_weight)  ? $WidgetSettings->cufon_hover_font_weight : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font hover font family'),
			array('text' => '<input type="text" name="cufon_hover_font_family" value="'. (isset($WidgetSettings->cufon_hover_font_family)  ? $WidgetSettings->cufon_hover_font_family : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font hover font style'),
			array('text' => '<input type="text" name="cufon_hover_font_style" value="'. (isset($WidgetSettings->cufon_hover_font_style)  ? $WidgetSettings->cufon_hover_font_style : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font color'),
			array('text' => '<input type="text" name="cufon_color" value="'. (isset($WidgetSettings->cufon_color)  ? $WidgetSettings->cufon_color : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font font family'),
			array('text' => '<input type="text" name="cufon_font_family" value="'. (isset($WidgetSettings->cufon_font_family)  ? $WidgetSettings->cufon_font_family : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font size'),
			array('text' => '<input type="text" name="cufon_font_size" value="'. (isset($WidgetSettings->cufon_font_size)  ? $WidgetSettings->cufon_font_size : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'  cufon font stretch'),
			array('text' => '<input type="text" name="cufon_font_stretch" value="'. (isset($WidgetSettings->cufon_font_stretch)  ? $WidgetSettings->cufon_font_stretech : '') . '">')
		)
));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'   cufon font style'),
			array('text' => '<input type="text" name="cufon_font_style" value="'. (isset($WidgetSettings->cufon_font_style)  ? $WidgetSettings->cufon_font_style : '') . '">')
		)
	));
$WidgetSettingsTable->addBodyRow(array(
		'columns' => array(
			array('text' =>'   cufon font weight'),
			array('text' => '<input type="text" name="cufon_font_weight" value="'. (isset($WidgetSettings->cufon_font_weight)  ? $WidgetSettings->cufon_font_weight : '') . '">')
		)
	));
  */

?>