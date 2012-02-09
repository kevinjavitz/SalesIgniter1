<?php
$code = $_GET['module'];
$Module = OrderShippingModules::getModule($code, true);
if (is_dir(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $code . '/Doctrine/')){
	Doctrine_Core::loadModels(sysConfig::getDirFsCatalog() . 'includes/modules/orderShippingModules/' . $code . '/Doctrine/', Doctrine_Core::MODEL_LOADING_AGGRESSIVE);
}
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . sprintf(sysLanguage::get('TEXT_INFO_HEADING_EDIT_MODULE'), $Module->getTitle()) . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

$Config = new ModuleConfigReader(
	$_GET['module'],
	$_GET['moduleType'],
	(isset($_GET['modulePath']) ? $_GET['modulePath'] : false)
);

$tabs = array();
$tabsPages = array();
$tabId = 1;
foreach($Config->getConfig() as $cfg){
	if (!isset($tabs[$cfg->getTab()])){
		$tabs[$cfg->getTab()] = array(
			'panelId' => 'page-' . $tabId,
			'panelHeader' => $cfg->getTab(),
			'panelTable' => htmlBase::newElement('table')
				->addClass('configTable')
				->setCellPadding(5)
				->setCellSpacing(0)
		);
		$tabId++;
	}

	if ($cfg->hasSetFunction() === true){
		$function = $cfg->getSetFunction();
		switch(true){
			case (stristr($function, 'tep_cfg_select_option')):
				$type = 'radio';
				$function = str_replace(
					'tep_cfg_select_option',
					'tep_cfg_select_option_elements',
					$function
				);
				break;
			case (stristr($function, 'tep_cfg_pull_down_order_statuses')):
				$type = 'drop';
				$function = str_replace(
					'tep_cfg_pull_down_order_statuses',
					'tep_cfg_pull_down_order_statuses_element',
					$function
				);
				break;
			case (stristr($function, 'tep_cfg_pull_down_zone_classes')):
				$type = 'drop';
				$function = str_replace(
					'tep_cfg_pull_down_zone_classes',
					'tep_cfg_pull_down_zone_classes_element',
					$function
				);
				break;
			case (stristr($function, 'tep_cfg_select_multioption')):
			case (stristr($function, '_selectOptions')):
				$type = 'checkbox';
				$function = str_replace(
					array(
						'tep_cfg_select_multioption',
						'_selectOptions'
					),
					'tep_cfg_select_multioption_element',
					$function
				);
				break;
		}
		eval('$inputField = ' . $function . "'" . $cfg->getValue() . "', '" . $cfg->getKey() . "');");

		if (is_object($inputField)){
			if ($type == 'checkbox'){
				$inputField->setName('configuration[' . $cfg->getKey() . '][]');
			}
			else {
				$inputField->setName('configuration[' . $cfg->getKey() . ']');
			}
		}
		elseif (substr($inputField, 0, 3) == '<br') {
			$inputField = substr($inputField, 4);
		}
	}
	else {
		$inputField = tep_draw_input_field('configuration[' . $cfg->getKey() . ']', $cfg->getValue());
	}

	$tabs[$cfg->getTab()]['panelTable']->addBodyRow(array(
			'columns' => array(
				array(
					'text' => '<b>' . $cfg->getTitle() . '</b>',
					'addCls' => 'main',
					'valign' => 'top'
				),
				array(
					'text' => $inputField,
					'addCls' => 'main',
					'valign' => 'top'
				),
				array(
					'text' => $cfg->getDescription(),
					'addCls' => 'main',
					'valign' => 'top'
				)
			)
		));
}

EventManager::notify(
	'ModuleEditWindowAddFields',
	&$tabs,
	$_GET['module'],
	$_GET['moduleType'],
	(isset($_GET['modulePath']) ? $_GET['modulePath'] : false)
);

$tabPanel = htmlBase::newElement('tabs')
	->addClass('makeTabPanel')
	->setId('module_tabs');
foreach($tabs as $pInfo){
	$tabPanel->addTabHeader($pInfo['panelId'], array('text' => $pInfo['panelHeader']))
		->addTabPage($pInfo['panelId'], array('text' => $pInfo['panelTable']));
}

EventManager::notify(
	'ModuleEditWindowBeforeDraw',
	&$tabPanel,
	$_GET['module'],
	$_GET['moduleType'],
	(isset($_GET['modulePath']) ? $_GET['modulePath'] : false)
);

$infoBox->addContentRow($tabPanel->draw());
	
	$Qcheck = Doctrine_Query::create()
	->select('MAX(method_id) as nextId')
	->from('ModulesShippingInventoryCenterMethods')
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