<?php
	$Module = OrderShippingModules::getModule($_GET['module']);
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . sprintf(sysLanguage::get('TEXT_INFO_HEADING_EDIT_MODULE'), $Module->getTitle()) . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

 	$typesArray = array(
				'1DM'    => 'Next Day Air Early AM',
				'1DML'   => 'Next Day Air Early AM Letter',
				'1DA'    => 'Next Day Air',
				'1DAL'   => 'Next Day Air Letter',
				'1DAPI'  => 'Next Day Air Intra (Puerto Rico)',
				'1DP'    => 'Next Day Air Saver',
				'1DPL'   => 'Next Day Air Saver Letter',
				'2DM'    => '2nd Day Air AM',
				'2DML'   => '2nd Day Air AM Letter',
				'2DA'    => '2nd Day Air',
				'2DAL'   => '2nd Day Air Letter',
				'3DS'    => '3 Day Select',
				'GND'    => 'Ground',
				'GNDCOM' => 'Ground Commercial',
				'GNDRES' => 'Ground Residential',
				'STD'    => 'Canada Standard',
				'XPR'    => 'Worldwide Express',
				'XPRL'   => 'worldwide Express Letter',
				'XDM'    => 'Worldwide Express Plus',
				'XDML'   => 'Worldwide Express Plus Letter',
				'XPD'    => 'Worldwide Expedited'
	);
    $typesString = '<table cellpadding="1" width="500"><tr><td><b>UPS Code</b></td><td><b> Value</b></td></tr><tr>';
	$tI = 0;
  	foreach($typesArray as $key => $value){
		$typesString .= '<td>'. $key. '</td><td> '. $value . '</td>';
		//$tI++;
		//if ($tI % 2 == 0){
		$typesString .= '</tr><tr>';
		//}
	}
   	$typesString .= '<td></td><td></td></tr></table>';

	foreach($Module->getConfigData() as $cInfo){
		$key = $cInfo['configuration_key'];
		$value = $cInfo['configuration_value'];
		
		if (isset($cInfo['set_function']) && $cInfo['set_function'] != 'isArea') {
			eval('$field = ' . $cInfo['set_function'] . "'" . $value . "', '" . $key . "');");
		} else if (isset($value['set_function']) && $value['set_function'] == 'isArea') {
			$field = tep_draw_textarea_field('configuration[' . $key . ']', 'hard', 30, 5, $value, 'class="makeModFCK"');
		}else {
			$field = tep_draw_input_field('configuration[' . $key . ']', $value);
		}
					
		$infoBox->addContentRow('<b>' . $cInfo['configuration_title'] . '</b><br>' . $cInfo['configuration_description'] . '<br>' . $field);
	}
	
	$Qcheck = Doctrine_Query::create()
	->select('MAX(method_id) as nextId')
	->from('ModulesShippingUpsReservationMethods')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$Table = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->addClass('ui-widget ui-widget-content')
	->css(array(
		'width' => '100%'
	))
	->attr('data-next_id', $Qcheck[0]['nextId'] + 1)
	->attr('language_id', Session::get('languages_id'));
	
	$Table->addHeaderRow(array(
		'addCls' => 'ui-state-hover',
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_TEXT')),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_DAYS_BEFORE')),
			array('text' => sysLanguage::get('TABLE_HEADING_DAYS_AFTER')),
			array('text' => sysLanguage::get('TABLE_HEADING_MARKUP')),
			array('text' => sysLanguage::get('TABLE_HEADING_UPSCODE')),
			array('text' => sysLanguage::get('TABLE_HEADING_SORT_ORDER')),
			array('text' => sysLanguage::get('TABLE_HEADING_DEFAULT')),
			array('text' => htmlBase::newElement('icon')->setType('insert')->addClass('insertIcon'))
		)
	));
	
	$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIcon')->draw();
	foreach($Module->getMethods() as $methodId => $mInfo){

		$Text = htmlBase::newElement('div');
		$br = htmlBase::newElement('br');
		foreach(sysLanguage::getLanguages() as $lInfo){
			$Textl = htmlBase::newElement('input')
			->addClass('ui-widget-content')
			->setLabel($lInfo['showName']())
			->setLabelPosition('before')
			->setName('method[' . $methodId . '][text]['.$lInfo['id'].']')
			->css(array(
				'width' => '100%'
			))
			->val($mInfo[$lInfo['id']]['text']);
			$Text->append($Textl)->append($br);
		}
		
		$Status = htmlBase::newElement('radio')
		->addClass('ui-widget-content')
		->addGroup(array(
			'name' => 'method[' . $methodId . '][status]',
			'checked' => $mInfo['status'],
			'separator' => '<br>',
			'data' => array(
				array('value' => 'True', 'label' => 'True', 'labelPosition' => 'after'),
				array('value' => 'False', 'label' => 'False', 'labelPosition' => 'after')
			)
		));
		
		$SortOrder = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][sort_order]')
		->attr('size', '3')
		->val($mInfo['sort_order']);
		
		$DaysBefore = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][days_before]')
		->attr('size', '3')
		->val($mInfo['days_before']);

		$DaysAfter = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][days_after]')
		->attr('size', '3')
		->val($mInfo['days_after']);

		$Markup = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][markup]')
		->attr('size', '3')
		->val($mInfo['markup']);

		$UpsCode = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][upscode]')
		->attr('size', '10')
		->val($mInfo['upscode']);

		$Default = htmlBase::newElement('radio')
		->addClass('ui-widget-content')
		->setName('method_default')
		->val($methodId);
		
		if ($mInfo['default'] == '1'){
			$Default->setChecked(true);
		}
		
		$Table->addBodyRow(array(
			'columns' => array(
				array('text' => $Text->draw()),
				array('align' => 'center', 'text' => $Status->draw()),
				array('align' => 'center', 'text' => $DaysBefore->draw()),
				array('align' => 'center', 'text' => $DaysAfter->draw()),
				array('align' => 'center', 'text' => $Markup->draw()),
				array('align' => 'center', 'text' => $UpsCode->draw()),
				array('align' => 'center', 'text' => $SortOrder->draw()),
				array('align' => 'center', 'text' => $Default->draw()),
				array('align' => 'center', 'text' => $deleteIcon)
			)
		));
	}
	$infoBox->addContentRow($typesString);
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_EDIT_INTRO'));
	$infoBox->addContentRow($Table->draw());
	
	ob_start();
?>
<script>
	function editWindowOnLoad(){
		$(this).find('.insertIcon').click(function (){
			var nextId = $(this).parent().parent().parent().parent().attr('data-next_id');
			var langId = $(this).parent().parent().parent().parent().attr('language_id');
			$(this).parent().parent().parent().parent().attr('data-next_id', parseInt(nextId)+1);
			
			var $td1 = $('<td></td>').append('<input class="ui-widget-content" style="width:100%;" type="text" name="method[' + nextId + '][text][' + langId + ']">');
			var $td2 = $('<td></td>').attr('align', 'center').append('<input class="ui-widget-content" type="radio" name="method[' + nextId + '][status]" value="True">True<br><input class="ui-widget-content" type="radio" name="method[' + nextId + '][status]" value="False" checked="checked">False');
			var $td3 = $('<td></td>').attr('align', 'center').append('<input size="3" class="ui-widget-content" type="text" name="method[' + nextId + '][days_before]">');
			var $td4 = $('<td></td>').attr('align', 'center').append('<input size="3" class="ui-widget-content" type="text" name="method[' + nextId + '][days_after]">');
			var $td5 = $('<td></td>').attr('align', 'center').append('<input size="3" class="ui-widget-content" type="text" name="method[' + nextId + '][markup]">');
			var $td6 = $('<td></td>').attr('align', 'center').append('<input size="3" class="ui-widget-content" type="text" name="method[' + nextId + '][upscode]">');
			var $td7 = $('<td></td>').attr('align', 'center').append('<input size="3" class="ui-widget-content" type="text" name="method[' + nextId + '][sort_order]">');
			var $td8 = $('<td></td>').attr('align', 'center').append('<input class="ui-widget-content" type="radio" name="method_default" value="' + nextId + '">');
			var $td9 = $('<td></td>').attr('align', 'center').append('<a class="ui-icon ui-icon-closethick deleteIcon"></a>');
			var $newTr = $('<tr></tr>').append($td1).append($td2).append($td3).append($td4).append($td5).append($td6).append($td7).append($td8).append($td9);
			$(this).parent().parent().parent().parent().find('tbody').append($newTr);
		});
	}
</script>
<?php
	$javascript = ob_get_contents();
	ob_end_clean();
	
	EventManager::attachActionResponse($infoBox->draw() . $javascript, 'html');
?>