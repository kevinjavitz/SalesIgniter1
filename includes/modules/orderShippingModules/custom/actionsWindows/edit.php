<?php
	$Module = OrderShippingModules::getModule($_GET['module']);
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . sprintf(sysLanguage::get('TEXT_INFO_HEADING_EDIT_MODULE'), $Module->getTitle()) . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

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
	->from('ModulesShippingCustomMethods')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$Table = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->addClass('ui-widget ui-widget-content')
	->css(array(
		'width' => '100%'
	))
	->attr('data-next_id', $Qcheck[0]['nextId'] + 1);
	
	$Table->addHeaderRow(array(
		'addCls' => 'ui-state-hover',
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_TEXT')),
			array('text' => sysLanguage::get('TABLE_HEADING_COST')),
			array('text' => sysLanguage::get('TABLE_HEADING_STATUS')),
			array('text' => sysLanguage::get('TABLE_HEADING_SORT_ORDER')),
			array('text' => sysLanguage::get('TABLE_HEADING_DEFAULT')),
			array('text' => htmlBase::newElement('icon')->setType('insert')->addClass('insertIcon'))
		)
	));
	
	$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIcon')->draw();
	foreach($Module->getMethods() as $methodId => $mInfo){
		$Text = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][text]')
		->css(array(
			'width' => '100%'
		))
		->val($mInfo['text']);
		
		$Cost = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][cost]')
		->attr('size', '8')
		->val($mInfo['cost']);
		
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
				array('align' => 'center', 'text' => $Cost->draw()),
				array('align' => 'center', 'text' => $Status->draw()),
				array('align' => 'center', 'text' => $SortOrder->draw()),
				array('align' => 'center', 'text' => $Default->draw()),
				array('align' => 'center', 'text' => $deleteIcon)
			)
		));
	}
	
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_EDIT_INTRO'));
	$infoBox->addContentRow($Table->draw());
	
	ob_start();
?>
<script>
	function editWindowOnLoad(){
		$(this).find('.insertIcon').click(function (){
			var nextId = $(this).parent().parent().parent().parent().attr('data-next_id');
			$(this).parent().parent().parent().parent().attr('data-next_id', parseInt(nextId)+1);
			
			var $td1 = $('<td></td>').append('<input class="ui-widget-content" style="width:100%;" type="text" name="method[' + nextId + '][text]">');
			var $td2 = $('<td></td>').attr('align', 'center').append('<input class="ui-widget-content" size="8" type="text" name="method[' + nextId + '][cost]">');
			var $td3 = $('<td></td>').attr('align', 'center').append('<input class="ui-widget-content" type="radio" name="method[' + nextId + '][status]" value="True">True<br><input class="ui-widget-content" type="radio" name="method[' + nextId + '][status]" value="False" checked="checked">False');
			var $td4 = $('<td></td>').attr('align', 'center').append('<input size="3" class="ui-widget-content" type="text" name="method[' + nextId + '][sort_order]">');
			var $td5 = $('<td></td>').attr('align', 'center').append('<input class="ui-widget-content" type="radio" name="method_default" value="' + nextId + '">');
			var $td6 = $('<td></td>').attr('align', 'center').append('<a class="ui-icon ui-icon-closethick deleteIcon"></a>');
			var $newTr = $('<tr></tr>').append($td1).append($td2).append($td3).append($td4).append($td5).append($td6);
			$(this).parent().parent().parent().parent().find('tbody').append($newTr);
		});
	}
</script>
<?php
	$javascript = ob_get_contents();
	ob_end_clean();
	
	EventManager::attachActionResponse($infoBox->draw() . $javascript, 'html');
?>