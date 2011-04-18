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
	->from('ModulesShippingZoneMethods')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$Table = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->addClass('ui-widget ui-widget-content')
	->attr('data-next_id', $Qcheck[0]['nextId'] + 1);
	
	$Table->addHeaderRow(array(
		'addCls' => 'ui-state-hover',
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_COUNTRIES')),
			array('text' => sysLanguage::get('TABLE_HEADING_COST')),
			array('text' => sysLanguage::get('TABLE_HEADING_HANDLING')),
			array('text' => htmlBase::newElement('icon')->setType('insert')->addClass('insertIcon'))
		)
	));
	
	$deleteIcon = htmlBase::newElement('icon')->setType('delete')->addClass('deleteIcon')->draw();
	foreach($Module->getMethods() as $methodId => $mInfo){
		$Countries = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][countries]')
		->val(implode(',', $mInfo['countries']));
		
		$Cost = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][cost]')
		->val(implode(',', $mInfo['cost']));
		
		$Handling = htmlBase::newElement('input')
		->addClass('ui-widget-content')
		->setName('method[' . $methodId . '][handling]')
		->val($mInfo['handling']);
		
		$Table->addBodyRow(array(
			'columns' => array(
				array('text' => $Countries->draw()),
				array('text' => $Cost->draw()),
				array('text' => $Handling->draw()),
				array('align' => 'right', 'text' => $deleteIcon)
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
			
			var $td1 = $('<td></td>').append('<input class="ui-widget-content" type="text" name="method[' + nextId + '][countries]">');
			var $td2 = $('<td></td>').append('<input class="ui-widget-content" type="text" name="method[' + nextId + '][cost]">');
			var $td3 = $('<td></td>').append('<input class="ui-widget-content" type="text" name="method[' + nextId + '][handling]">');
			var $td4 = $('<td></td>').attr('align', 'right').append('<a class="ui-icon ui-icon-closethick deleteIcon"></a>');
			var $newTr = $('<tr></tr>').append($td1).append($td2).append($td3).append($td4);
			$(this).parent().parent().parent().parent().find('tbody').append($newTr);
		});
	}
</script>
<?php
	$javascript = ob_get_contents();
	ob_end_clean();
	
	EventManager::attachActionResponse($infoBox->draw() . $javascript, 'html');
?>