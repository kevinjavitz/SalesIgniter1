<?php
$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT') . '</b>');
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

EventManager::attachActionResponse($infoBox->draw(), 'html');
?>