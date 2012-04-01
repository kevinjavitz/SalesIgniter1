<script type="text/javascript">
	var templateName = '<?php echo $templateName;?>';
	var layoutId = '<?php echo (int)$Layout->layout_id;?>';
</script>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_LAYOUT_EDITOR');
	?></div>
<br />
<div id="topButtonBar" style="text-align:right;margin:.5em;"><?php
	echo htmlBase::newElement('button')->usePreset('back')->setText('Back To Layout Listing')
		->setHref(itw_app_link('appExt=templateManager&tID=' . $Layout->Template->template_id, 'layout_manager', 'layouts'))
		->draw();
	?></div>
<div id="saveLayoutText" style="display:none;"><?php
	echo '<span class="changedText">Layout Has Been Changed</span>';

	$htmlSaveLayoutT = htmlBase::newElement('button')
		->setName('saveLayout2')
		->setText('Save Layout')
		->attr('id', 'saveLayoutT');

	$noSave = htmlBase::newElement('button')
		->setName('noSaveLayout')
		->setText('Continue Without Saving')
		->attr('id', 'noSaveLayout');
	echo $htmlSaveLayoutT->draw() . '&nbsp;' . $noSave->draw();
	?></div>
<div class="unselectable" id='construct-header'>
	<div class="inside">
		<ul id='construct-actionMenu'>
			<li><a href='#' id='construct-addContainer' title='Add container'>Add Container</a></li>
			<li><a href='#' id='construct-addColumn' title='Add column'>Add Column</a></li>
			<li><a href='#' id='construct-addSubColumn' title='Add a column inside selected column'>Add Sub-Column</a></li>
			<li><a href='#' id='construct-widgets' title='Add widgets to template'>Add widgets</a></li>
			<li><a href='#' id='construct-link' title='Create a linked container to use on other templates' class="ui-state-disabled">Create Link</a></li>
			<li><a href='#' id='construct-importlink' title='Import a linked container'>Import Link</a></li>
			<li><a href='#' id='construct-borders' title='Show Outline Around Columns And Containers'>Show Outline</a>
			<li><a href='#' id='construct-zoomMode' title='Turn Zoom Mode On/Off' data-zoommode="false">Zoom Mode Off</a>
			</li>
		</ul>
		<div class="containerBreadcrumb"></div>
		<div id="scrollInfo"></div>
	</div>
</div>
<div id="widgetsForm" style="display:none;">
	<div class="ui-widget-header">
		<a id="hideWidgets" class="ui-icon ui-icon-closethick"></a>
	</div>
	<div class="editWindow centerColumn" style="display:none; position: relative; ;"></div>
	<div class="boxListing centerColumn" style="position: relative;">
		<div class="ui-widget ui-widget-content ui-corner-bottom" style="padding: 0.3em;border-top: none;"><?php
			foreach($TemplateManager->getWidgetPaths() as $widgetCode => $widgetPath){
				echo '<div class="ui-widget ui-widget-content ui-corner-all draggableField" id="' . $widgetCode . '" style="margin:.2em;float:left;width:175px;padding:.3em;">' .
					'<div class="ui-widget-header ui-corner-all" style="padding:.2em;padding-left:.5em;">' .
					$widgetCode .
					'</div>' .
					'</div>';
			}
			echo '<div class="ui-helper-clearfix"></div>';
			?></div>
	</div>
</div>
<br style="clear:both" />

<?php
$Profile = SES_Profiler::newProfile('templateLoad', true);
$Construct = htmlBase::newElement('div')
	->attr('id', 'construct')
	->addClass($Layout->layout_type);
if ($Layout->Styles->count() > 0){
	addStyles($Construct, $Layout->Styles);
}
else {
	$Construct->attr('data-styles', '{}');
}

if ($Layout->Configuration->count() > 0){
	addInputs($Construct, $Layout->Configuration);
}
else {
	$Construct->attr('data-inputs', '{}');
}

if ($Layout->Containers && $Layout->Containers->count() > 0){
	foreach($Layout->Containers as $MainObj){
		if ($MainObj->Parent->container_id > 0) {
			continue;
		}

		$MainEl = htmlBase::newElement('div')
			->attr('data-container_id', $MainObj->container_id)
			->attr('data-sort_order', (int) $MainObj->sort_order)
			->addClass('container');
		if ($MainObj->link_id > 0){
			$MainEl->attr('data-link_id', $MainObj->link_id);
			$LinkedObj = $MainObj->Link->Container;

			if ($LinkedObj->Styles->count() > 0){
				addStyles($MainEl, $LinkedObj->Styles);
			}

			if ($LinkedObj->Configuration->count() > 0){
				addInputs($MainEl, $LinkedObj->Configuration);
			}

			processContainerColumns($MainEl, $LinkedObj->Columns);
			if ($LinkedObj->Children->count() > 0){
				processContainerChildren($LinkedObj, $MainEl);
			}
		}else{
			if ($MainObj->Styles->count() > 0){
				addStyles($MainEl, $MainObj->Styles);
			}

			if ($MainObj->Configuration->count() > 0){
				addInputs($MainEl, $MainObj->Configuration);
			}

			processContainerColumns($MainEl, $MainObj->Columns);
			if ($MainObj->Children->count() > 0){
				processContainerChildren($MainObj, $MainEl);
			}
		}

		$Construct->append($MainEl);
	}
}
$Profile->end();
echo '<div id="construct-container">';
echo $Construct->draw();
echo '</div>';
echo '<div id="zoomOverlay"></div>'
?>
<div id="importableLinks" title="Import A Linked Container" style="display:none;"><select name="link_id"><?php
	$Qlinks = Doctrine_Query::create()
		->from('TemplateManagerContainerLinks')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qlinks){
		foreach($Qlinks as $lInfo){
			echo '<option value="' . $lInfo['link_id'] . '">' . $lInfo['link_name'] . '</option>';
		}
	}
	?></select></div>
<div id="elementProperties" style="display:none;">
<div id="mainTabPanel" class="tabs">
<ul>
	<li><a href="#settings"><span>Settings</span></a></li>
	<li><a href="#font"><span>Font</span></a></li>
	<li><a href="#margin"><span>Margin/Padding</span></a></li>
	<li><a href="#border"><span>Border</span></a></li>
	<li><a href="#shadow"><span>Box Shadow</span></a></li>
	<li><a href="#background"><span>Background</span></a></li>
	<li><a href="#style"><span>Custom Styling</span></a></li>
	<!--<li><a href="#advanced"><span>Advanced Styling</span></a></li>-->
	<li style="float: right; margin: 0; padding: 0;">
		<a class="closeAdjustPopup"><span class="ui-icon ui-icon-closethick"></span></a></li>
</ul>

<div id="settings">
	<table cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td>Id:</td>
			<td><input type="text" name="id"></td>
		</tr>
		<tr>
			<td>Width:</td>
			<td><input type="text" name="width" size="5"> <select name="width_unit">
				<option value="auto">Auto</option>
				<option value="px" selected>Pixels</option>
				<!--<option value="em">Em</option>-->
				<option value="%">Percent</option>
			</td>
		</tr>
	</table>
	<div class="widthSlider"></div>
</div>

<div id="font">
<ul>
	<li><a href="#fontSettings"><span>Font Settings</span></a></li>
	<li><a href="#textSettings"><span>Text Settings</span></a></li>
	<li><a href="#linkSettings"><span>Link Settings</span></a></li>
</ul>

<div id="fontSettings">
	<table cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_font_font-family.asp" target="_blank"></a>
			</td>
			<td>Family:</td>
			<td colspan="2"><select name="font_family"><?php
				echo '<option value="Arial">Arial</option>';
				echo '<option value="New Times Roman">New Times Roman</option>';
				echo '<option value="Tahoma">Tahoma</option>';
				echo '<option value="Verdana">Verdana</option>';
				?></select></td>
		</tr>
		<tr>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_font_font-size.asp" target="_blank"></a>
			</td>
			<td>Size:</td>
			<td><input type="text" size="3" name="font_size" value="12"></td>
			<td><select name="font_size_unit">
				<option value="px" selected>Pixels</option>
				<option value="em">Em</option>
				<option value="%">Percent</option>
				<option value="inherit">Inherit</option>
			</select></td>
		</tr>
		<tr>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_font_font-style.asp" target="_blank"></a>
			</td>
			<td>Style:</td>
			<td colspan="2"><select name="font_style">
				<option value="normal" selected>Normal</option>
				<option value="italic">Italic</option>
				<option value="oblique">Oblique</option>
				<option value="inherit">Inherit</option>
			</select></td>
		</tr>
		<tr>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_font_font-variant.asp" target="_blank"></a>
			</td>
			<td>Variant:</td>
			<td colspan="2"><select name="font_variant">
				<option value="normal" selected>Normal</option>
				<option value="small-caps">Small Caps</option>
				<option value="inherit">Inherit</option>
			</select></td>
		</tr>
		<tr>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_font_weight.asp" target="_blank"></a>
			</td>
			<td>Weight:</td>
			<td colspan="2"><select name="font_weight">
				<option value="normal" selected>Normal</option>
				<option value="bold">Bold</option>
				<option value="bolder">Bolder</option>
				<option value="lighter">Lighter</option>
				<option value="100">100</option>
				<option value="200">200</option>
				<option value="300">300</option>
				<option value="400">400</option>
				<option value="500">500</option>
				<option value="600">600</option>
				<option value="700">700</option>
				<option value="800">800</option>
				<option value="900">900</option>
			</select></td>
		</tr>
	</table>
</div>

<div id="textSettings">
	<table cellpadding="6" cellspacing="0" border="0">
		<tr>
			<td valign="top"><table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_text_text-align.asp" target="_blank"></a>
					</td>
					<td>Align:</td>
					<td colspan="2"><select name="text_align">
						<option value="left" selected>Left</option>
						<option value="right">Right</option>
						<option value="center">Center</option>
						<option value="justify">Justify</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_text_text-decoration.asp" target="_blank"></a>
					</td>
					<td>Decoration:</td>
					<td colspan="2"><select name="text_decoration">
						<option value="none" selected>None</option>
						<option value="underline">Underline</option>
						<option value="overline">Overline</option>
						<option value="line-through">Line Through</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_text_text-indent.asp" target="_blank"></a>
					</td>
					<td>Indent:</td>
					<td><input type="text" size="3" name="text_indent" value="0"></td>
					<td><select name="text_indent_unit">
						<option value="px" selected>Pixels</option>
						<option value="em">Em</option>
						<option value="%">Percent</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_text_text-transform.asp" target="_blank"></a>
					</td>
					<td>Transform:</td>
					<td colspan="2"><select name="text_transform">
						<option value="none" selected>None</option>
						<option value="capitalize">Capitalize</option>
						<option value="uppercase">Uppercase</option>
						<option value="lowercase">Lowercase</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_text_color.asp" target="_blank"></a>
					</td>
					<td>Color:</td>
					<td colspan="2"><input class="makeColorPicker" type="text" name="color"></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_text_letter-spacing.asp" target="_blank"></a>
					</td>
					<td>Letter Spacing:</td>
					<td><input type="text" size="3" name="letter_spacing" value="0"></td>
					<td><select name="letter_spacing_unit">
						<option value="px">Pixels</option>
						<option value="normal" selected>Normal</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_dim_line-height.asp" target="_blank"></a>
					</td>
					<td>Line Height:</td>
					<td><input type="text" size="3" name="line_height" value="1"></td>
					<td><select name="line_height_unit">
						<option value="px">Pixels</option>
						<option value="em" selected>Em</option>
						<option value="%">Percent</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_pos_vertical-align.asp" target="_blank"></a>
					</td>
					<td>Vertical Align:</td>
					<td colspan="2"><select name="vertical_align">
						<option value="baseline" selected>Baseline</option>
						<option value="sub">Subscript</option>
						<option value="super">Superscript</option>
						<option value="top">Top</option>
						<option value="text-top">Text Top</option>
						<option value="middle">Middle</option>
						<option value="bottom">Bottom</option>
						<option value="text-bottom">Text Bottom</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_text_white-space.asp" target="_blank"></a>
					</td>
					<td>White Space:</td>
					<td colspan="2"><select name="white_space">
						<option value="normal" selected>Normal</option>
						<option value="nowrap">No Wrap</option>
						<option value="pre">Preserve</option>
						<option value="pre-line">Preserve Line</option>
						<option value="pre-wrap">Preserve Wrap</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
				<tr>
					<td align="right">
						<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/pr_text_word-spacing.asp" target="_blank"></a>
					</td>
					<td>Word Spacing:</td>
					<td><input type="text" size="3" name="word_spacing" value="1"></td>
					<td><select name="word_spacing_unit">
						<option value="px">Pixels</option>
						<option value="em">Em</option>
						<option value="%">Percent</option>
						<option value="normal" selected>Normal</option>
						<option value="inherit">Inherit</option>
					</select></td>
				</tr>
			</table></td>
			<td valign="top">
				<div style="height:2em;">
					<span class="ui-icon ui-icon-plusthick addTextShadow" tooltip="Add Shadow Configuration"></span> Add Shadow
				</div>
				<table class="textShadowConfigs" cellpadding="5" cellspacing="5" border="0">
					<thead>
					<tr>
						<th>Offset X</th>
						<th>Offset Y</th>
						<th>Blur</th>
						<th>Color</th>
						<th></th>
					</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</td>
		</tr>
	</table>
</div>

<div id="linkSettings">
	<table cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/css_link.asp" target="_blank"></a><b>Unvisited</b>
			</td>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/css_link.asp" target="_blank"></a><b>Visited</b>
			</td>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/css_link.asp" target="_blank"></a><b>Mouse Over</b>
			</td>
			<td>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/css_link.asp" target="_blank"></a><b>Selected</b>
			</td>
		</tr>
		<tr>
			<td>
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td>Font Weight:</td>
						<td><select name="link_unvisited_font_weight">
							<option value="normal" selected>Normal</option>
							<option value="bold">Bold</option>
							<option value="bolder">Bolder</option>
							<option value="lighter">Lighter</option>
							<option value="100">100</option>
							<option value="200">200</option>
							<option value="300">300</option>
							<option value="400">400</option>
							<option value="500">500</option>
							<option value="600">600</option>
							<option value="700">700</option>
							<option value="800">800</option>
							<option value="900">900</option>
						</select></td>
					</tr>
					<tr>
						<td>Text Decoration:</td>
						<td><select name="link_unvisited_text_decoration">
							<option value="none" selected>None</option>
							<option value="underline">Underline</option>
							<option value="overline">Overline</option>
							<option value="line-through">Line Through</option>
							<option value="inherit">Inherit</option>
						</select></td>
					</tr>
					<tr>
						<td>Text Color:</td>
						<td><input class="makeColorPicker" type="text" name="link_unvisited_color"></td>
					</tr>
					<tr>
						<td>Background Color:</td>
						<td><input class="makeColorPicker" type="text" name="link_unvisited_background_color"></td>
					</tr>
				</table>
			</td>
			<td>
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td>Font Weight:</td>
						<td><select name="link_visited_font_weight">
							<option value="normal" selected>Normal</option>
							<option value="bold">Bold</option>
							<option value="bolder">Bolder</option>
							<option value="lighter">Lighter</option>
							<option value="100">100</option>
							<option value="200">200</option>
							<option value="300">300</option>
							<option value="400">400</option>
							<option value="500">500</option>
							<option value="600">600</option>
							<option value="700">700</option>
							<option value="800">800</option>
							<option value="900">900</option>
						</select></td>
					</tr>
					<tr>
						<td>Text Decoration:</td>
						<td><select name="link_visited_text_decoration">
							<option value="none" selected>None</option>
							<option value="underline">Underline</option>
							<option value="overline">Overline</option>
							<option value="line-through">Line Through</option>
							<option value="inherit">Inherit</option>
						</select></td>
					</tr>
					<tr>
						<td>Text Color:</td>
						<td><input class="makeColorPicker" type="text" name="link_visited_color"></td>
					</tr>
					<tr>
						<td>Background Color:</td>
						<td><input class="makeColorPicker" type="text" name="link_visited_background_color"></td>
					</tr>
				</table>
			</td>
			<td>
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td>Font Weight:</td>
						<td><select name="link_hover_font_weight">
							<option value="normal" selected>Normal</option>
							<option value="bold">Bold</option>
							<option value="bolder">Bolder</option>
							<option value="lighter">Lighter</option>
							<option value="100">100</option>
							<option value="200">200</option>
							<option value="300">300</option>
							<option value="400">400</option>
							<option value="500">500</option>
							<option value="600">600</option>
							<option value="700">700</option>
							<option value="800">800</option>
							<option value="900">900</option>
						</select></td>
					</tr>
					<tr>
						<td>Text Decoration:</td>
						<td><select name="link_hover_text_decoration">
							<option value="none" selected>None</option>
							<option value="underline">Underline</option>
							<option value="overline">Overline</option>
							<option value="line-through">Line Through</option>
							<option value="inherit">Inherit</option>
						</select></td>
					</tr>
					<tr>
						<td>Text Color:</td>
						<td><input class="makeColorPicker" type="text" name="link_hover_color"></td>
					</tr>
					<tr>
						<td>Background Color:</td>
						<td><input class="makeColorPicker" type="text" name="link_hover_background_color"></td>
					</tr>
				</table>
			</td>
			<td>
				<table cellpadding="2" cellspacing="0" border="0">
					<tr>
						<td>Font Weight:</td>
						<td><select name="link_active_font_weight">
							<option value="normal" selected>Normal</option>
							<option value="bold">Bold</option>
							<option value="bolder">Bolder</option>
							<option value="lighter">Lighter</option>
							<option value="100">100</option>
							<option value="200">200</option>
							<option value="300">300</option>
							<option value="400">400</option>
							<option value="500">500</option>
							<option value="600">600</option>
							<option value="700">700</option>
							<option value="800">800</option>
							<option value="900">900</option>
						</select></td>
					</tr>
					<tr>
						<td>Text Decoration:</td>
						<td><select name="link_active_text_decoration">
							<option value="none" selected>None</option>
							<option value="underline">Underline</option>
							<option value="overline">Overline</option>
							<option value="line-through">Line Through</option>
							<option value="inherit">Inherit</option>
						</select></td>
					</tr>
					<tr>
						<td>Text Color:</td>
						<td><input class="makeColorPicker" type="text" name="link_active_color"></td>
					</tr>
					<tr>
						<td>Background Color:</td>
						<td><input class="makeColorPicker" type="text" name="link_active_background_color"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

</div>

<div id="margin">
	<table cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td>
				<table cellpadding="0" cellspacing="10" border="0">
					<tr>
						<td colspan="3" align="center">
							<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/css_margin.asp" target="_blank"></a><b>Margin</b>
						</td>
					</tr>
					<tr>
						<td>Top:</td>
						<td align="center" style="width:100px;">
							<input type="text" name="margin_top" size="5" value="0" style="margin-bottom: 5px;">

							<div id="margin_topSlider"></div>
						</td>
						<td><select name="margin_top_unit">
							<option value="px" selected>Pixels</option>
							<option value="em">Em</option>
							<option value="%">Percent</option>
						</select></td>
					</tr>
					<tr>
						<td>Right:</td>
						<td align="center">
							<input type="text" name="margin_right" size="5" value="0" style="margin-bottom: 5px;">

							<div id="margin_rightSlider"></div>
						</td>
						<td><select name="margin_right_unit">
							<option value="auto" selected>Auto</option>
							<option value="px">Pixels</option>
							<option value="em">Em</option>
							<option value="%">Percent</option>
						</select></td>
					</tr>
					<tr>
						<td>Bottom:</td>
						<td align="center">
							<input type="text" name="margin_bottom" size="5" value="0" style="margin-bottom: 5px;">

							<div id="margin_bottomSlider"></div>
						</td>
						<td><select name="margin_bottom_unit">
							<option value="px" selected>Pixels</option>
							<option value="em">Em</option>
							<option value="%">Percent</option>
						</select></td>
					</tr>
					<tr>
						<td>Left:</td>
						<td align="center">
							<input type="text" name="margin_left" size="5" value="0" style="margin-bottom: 5px;">

							<div id="margin_leftSlider"></div>
						</td>
						<td><select name="margin_left_unit">
							<option value="auto" selected>Auto</option>
							<option value="px">Pixels</option>
							<option value="em">Em</option>
							<option value="%">Percent</option>
						</select></td>
					</tr>
				</table>
			</td>
			<td>&nbsp;&nbsp;</td>
			<td>
				<table cellpadding="0" cellspacing="10" border="0">
					<tr>
						<td colspan="3" align="center">
							<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/css_padding.asp" target="_blank"></a><b>Padding</b>
						</td>
					</tr>
					<tr>
						<td>Top:</td>
						<td align="center" style="width:100px;">
							<input type="text" name="padding_top" size="5" value="0" style="margin-bottom: 5px;">

							<div id="padding_topSlider"></div>
						</td>
						<td><select name="padding_top_unit">
							<option value="px" selected>Pixels</option>
							<option value="em">Em</option>
							<option value="%">Percent</option>
						</select></td>
					</tr>
					<tr>
						<td>Right:</td>
						<td align="center">
							<input type="text" name="padding_right" size="5" value="0" style="margin-bottom: 5px;">

							<div id="padding_rightSlider"></div>
						</td>
						<td><select name="padding_right_unit">
							<option value="auto" selected>Auto</option>
							<option value="px">Pixels</option>
							<option value="em">Em</option>
							<option value="%">Percent</option>
						</select></td>
					</tr>
					<tr>
						<td>Bottom:</td>
						<td align="center">
							<input type="text" name="padding_bottom" size="5" value="0" style="margin-bottom: 5px;">

							<div id="padding_bottomSlider"></div>
						</td>
						<td><select name="padding_bottom_unit">
							<option value="px" selected>Pixels</option>
							<option value="em">Em</option>
							<option value="%">Percent</option>
						</select></td>
					</tr>
					<tr>
						<td>Left:</td>
						<td align="center">
							<input type="text" name="padding_left" size="5" value="0" style="margin-bottom: 5px;">

							<div id="padding_leftSlider"></div>
						</td>
						<td><select name="padding_left_unit">
							<option value="auto" selected>Auto</option>
							<option value="px">Pixels</option>
							<option value="em">Em</option>
							<option value="%">Percent</option>
						</select></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>

<div id="border">
	<table cellpadding="2" cellspacing="0" border="0">
		<thead>
		<tr>
			<th>
				<a class="ui-icon ui-icon-info" tooltip="More Info" href="http://www.w3schools.com/css/css_border.asp" target="_blank"></a>
			</th>
			<th>Width</th>
			<th>Unit</th>
			<th>Color</th>
			<th>Style</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>Top:</td>
			<td><input type="text" name="border_top_width" size="5" value="0"></td>
			<td><select name="border_top_width_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
			<td><input type="text" name="border_top_color" class="makeColorPicker" size="7"></td>
			<td><select name="border_top_style">
				<option value="none">None</option>
				<option value="dashed">Dashed</option>
				<option value="dotted">Dotted</option>
				<option value="double">Double</option>
				<option value="hidden">Hidden</option>
				<option value="groove">Groove</option>
				<option value="inherit">Inherit</option>
				<option value="inset">Inset</option>
				<option value="outset">Outset</option>
				<option value="ridge">Ridge</option>
				<option value="solid" selected>Solid</option>
			</select></td>
		</tr>
		<tr>
			<td>Right:</td>
			<td><input type="text" name="border_right_width" size="5" value="0"></td>
			<td><select name="border_right_width_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
			<td><input type="text" name="border_right_color" class="makeColorPicker" size="7"></td>
			<td><select name="border_right_style">
				<option value="none">None</option>
				<option value="dashed">Dashed</option>
				<option value="dotted">Dotted</option>
				<option value="double">Double</option>
				<option value="hidden">Hidden</option>
				<option value="groove">Groove</option>
				<option value="inherit">Inherit</option>
				<option value="inset">Inset</option>
				<option value="outset">Outset</option>
				<option value="ridge">Ridge</option>
				<option value="solid" selected>Solid</option>
			</select></td>
		</tr>
		<tr>
			<td>Bottom:</td>
			<td><input type="text" name="border_bottom_width" size="5" value="0"></td>
			<td><select name="border_bottom_width_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
			<td><input type="text" name="border_bottom_color" class="makeColorPicker" size="7"></td>
			<td><select name="border_bottom_style">
				<option value="none">None</option>
				<option value="dashed">Dashed</option>
				<option value="dotted">Dotted</option>
				<option value="double">Double</option>
				<option value="hidden">Hidden</option>
				<option value="groove">Groove</option>
				<option value="inherit">Inherit</option>
				<option value="inset">Inset</option>
				<option value="outset">Outset</option>
				<option value="ridge">Ridge</option>
				<option value="solid" selected>Solid</option>
			</select></td>
		</tr>
		<tr>
			<td>Left:</td>
			<td><input type="text" name="border_left_width" size="5" value="0"></td>
			<td><select name="border_left_width_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
			<td><input type="text" name="border_left_color" class="makeColorPicker" size="7"></td>
			<td><select name="border_left_style">
				<option value="none">None</option>
				<option value="dashed">Dashed</option>
				<option value="dotted">Dotted</option>
				<option value="double">Double</option>
				<option value="hidden">Hidden</option>
				<option value="groove">Groove</option>
				<option value="inherit">Inherit</option>
				<option value="inset">Inset</option>
				<option value="outset">Outset</option>
				<option value="ridge">Ridge</option>
				<option value="solid" selected>Solid</option>
			</select></td>
		</tr>
		</tbody>
	</table>
	<table cellpadding="2" cellspacing="0" border="0">
		<thead>
		<tr>
			<th></th>
			<th>Radius</th>
			<th>Unit</th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td>Top Left:</td>
			<td><input type="text" name="border_top_left_radius" size="5" value="0"></td>
			<td><select name="border_top_left_radius_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
		</tr>
		<tr>
			<td>Top Right:</td>
			<td><input type="text" name="border_top_right_radius" size="5" value="0"></td>
			<td><select name="border_top_right_radius_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
		</tr>
		<tr>
			<td>Bottom Left:</td>
			<td><input type="text" name="border_bottom_left_radius" size="5" value="0"></td>
			<td><select name="border_bottom_left_radius_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
		</tr>
		<tr>
			<td>Bottom Right:</td>
			<td><input type="text" name="border_bottom_right_radius" size="5" value="0"></td>
			<td><select name="border_bottom_right_radius_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
		</tr>
		</tbody>
	</table>
</div>

<div id="shadow">
	<div style="height:2em;">
		<span class="ui-icon ui-icon-plusthick addShadow" tooltip="Add Shadow Configuration"></span> Add Shadow
	</div>
	<table class="shadowConfigs" cellpadding="5" cellspacing="5" border="0">
		<thead>
		<tr>
			<th>Offset X</th>
			<th>Offset Y</th>
			<th>Blur</th>
			<th>Spread</th>
			<th>Color</th>
			<th>Inset</th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<div id="background">
	<div id="backgroundTabs">
		<ul>
			<li><a href="#Global"><span>Global</span></a></li>
			<li><a href="#IE8"><span>Internet Explorer 7 & 8</span></a></li>
			<li><a href="#IE9"><span>Internet Explorer 9</span></a></li>
			<li><a href="#Chrome"><span>Google Chrome</span></a></li>
			<li><a href="#Firefox"><span>Mozilla FireFox</span></a></li>
			<li><a href="#Opera"><span>Opera</span></a></li>
		</ul>

		<div id="Global" data-engine="global">
			<table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td>Background Type:</td>
					<td><select name="background_type">
						<option value="transparent">Transparent</option>
						<option value="solid">Solid Color</option>
						<option value="image">Image</option>
						<option value="gradient">Gradient</option>
					</select></td>
				</tr>
			</table>
			<div class="backgroundSettings" style="display:block;margin:.5em;"></div>
		</div>

		<div id="IE8" data-engine="trident">
			<table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td>Background Type:</td>
					<td><select name="background_type">
						<option value="global" selected>Use Global Settings</option>
						<option value="transparent">Transparent</option>
						<option value="solid">Solid Color</option>
						<option value="image">Image</option>
						<option value="gradient">Gradient</option>
					</select></td>
				</tr>
			</table>
			<div class="backgroundSettings" style="display:block;margin:.5em;"></div>
		</div>

		<div id="IE9" data-engine="trident">
			<table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td>Background Type:</td>
					<td><select name="background_type">
						<option value="global" selected>Use Global Settings</option>
						<option value="transparent">Transparent</option>
						<option value="solid">Solid Color</option>
						<option value="image">Image</option>
						<option value="gradient">Gradient</option>
					</select></td>
				</tr>
			</table>
			<div class="backgroundSettings" style="display:block;margin:.5em;"></div>
		</div>

		<div id="Chrome" data-engine="webkit">
			<table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td>Background Type:</td>
					<td><select name="background_type">
						<option value="global" selected>Use Global Settings</option>
						<option value="transparent">Transparent</option>
						<option value="solid">Solid Color</option>
						<option value="image">Image</option>
						<option value="gradient">Gradient</option>
					</select></td>
				</tr>
			</table>
			<div class="backgroundSettings" style="display:block;margin:.5em;"></div>
		</div>

		<div id="Firefox" data-engine="gecko">
			<table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td>Background Type:</td>
					<td><select name="background_type">
						<option value="global" selected>Use Global Settings</option>
						<option value="transparent">Transparent</option>
						<option value="solid">Solid Color</option>
						<option value="image">Image</option>
						<option value="gradient">Gradient</option>
					</select></td>
				</tr>
			</table>
			<div class="backgroundSettings" style="display:block;margin:.5em;"></div>
		</div>

		<div id="Opera" data-engine="presto">
			<table cellpadding="2" cellspacing="0" border="0">
				<tr>
					<td>Background Type:</td>
					<td><select name="background_type">
						<option value="global" selected>Use Global Settings</option>
						<option value="transparent">Transparent</option>
						<option value="solid">Solid Color</option>
						<option value="image">Image</option>
						<option value="gradient">Gradient</option>
					</select></td>
				</tr>
			</table>
			<div class="backgroundSettings" style="display:block;margin:.5em;"></div>
		</div>
	</div>
</div>

<div id="style">
	<table cellpadding="2" cellspacing="0" border="0">
		<!--<tr>
			<td>Classes:</td>
		</tr>
		<tr>
			<td><textarea name="classes" style="width:300px;height:100px;" rows="7"></textarea></td>
		</tr>-->
		<tr>
			<td>Custom Css:</td>
		</tr>
		<tr>
			<td><textarea style="width:300px;height:100px;" name="custom_css"></textarea></td>
		</tr>
	</table>
</div>

<!--<div id="advanced">
	<table cellpadding="2" cellspacing="0" border="0">
		<tr>
			<td colspan="3"><input type="checkbox" name="enable_advanced" value="1"> Enable Advanced</td>
		</tr>
		<tr>
			<td>Float:</td>
			<td colspan="2"><select name="float">
				<option value="inherit">Inherit</option>
				<option value="left">Left</option>
				<option value="right">Right</option>
				<option value="none">None</option>
			</select></td>
		</tr>
		<tr>
			<td>Position:</td>
			<td colspan="2"><select name="position">
				<option value="ignore">Default</option>
				<option value="relative">Relative</option>
				<option value="absolute">Absolute</option>
				<option value="fixed">Fixed</option>
				<option value="static">Static</option>
				<option value="inherit">Inherit</option>
			</select></td>
		</tr>
		<tr>
			<td>Top:</td>
			<td><input type="text" name="top" value="" size="5"></td>
			<td><select name="top_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
		</tr>
		<tr>
			<td>Right:</td>
			<td><input type="text" name="right" value="" size="5"></td>
			<td><select name="right_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
		</tr>
		<tr>
			<td>Bottom:</td>
			<td><input type="text" name="bottom" value="" size="5"></td>
			<td><select name="bottom_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
		</tr>
		<tr>
			<td>Left:</td>
			<td><input type="text" name="left" value="" size="5"></td>
			<td><select name="left_unit">
				<option value="px" selected>Pixels</option>
				<option value="%">Percent</option>
			</select></td>
		</tr>
		<tr>
			<td>Overflow X:</td>
			<td colspan="2"><select name="overflow_x">
				<option value="auto" selected>Auto</option>
				<option value="hidden">Hidden</option>
				<option value="scroll">Scroll</option>
				<option value="visible">Visible</option>
				<option value="inherit">Inherit</option>
			</select></td>
		</tr>
		<tr>
			<td>Overflow Y:</td>
			<td colspan="2"><select name="overflow_y">
				<option value="auto" selected>Auto</option>
				<option value="hidden">Hidden</option>
				<option value="scroll">Scroll</option>
				<option value="visible">Visible</option>
				<option value="inherit">Inherit</option>
			</select></td>
		</tr>
		<tr>
			<td>Z-Index:</td>
			<td><input type="text" name="z_index" value="" size="5"></td>
			<td></td>
		</tr>
	</table>
</div>-->
</div>
</div>

