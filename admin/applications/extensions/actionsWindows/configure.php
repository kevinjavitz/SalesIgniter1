<?php
$infoBox = htmlBase::newElement('infobox');
$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_EDIT') . '</b>');
$infoBox->setButtonBarLocation('top');

$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

$infoBox->addButton($saveButton)->addButton($cancelButton);

$Configuration = new ExtensionConfigReader($_GET['extension']);

$tabs = array();
$tabsPages = array();
$tabId = 1;
foreach($Configuration->getConfig() as $tabKey => $tabInfo){
	if (!isset($tabs[$tabKey])){
		$tabs[$tabKey] = array(
			'panelId' => 'page-' . $tabId,
			'panelHeader' => $tabInfo['title'],
			'panelDescription' => $tabInfo['description'],
			'panelTable' => htmlBase::newElement('table')
				->addClass('configTable')
				->setCellPadding(5)
				->setCellSpacing(0)
		);
		$tabId++;
	}

	foreach($tabInfo['config'] as $Config){
		$tabs[$tabKey]['panelTable']->addBodyRow(array(
			'columns' => array(
				array(
					'text'   => '<span class="ui-icon ui-icon-blue ui-icon-alert" style="display:none" tooltip="This field has been edited"></span>',
					'addCls' => 'editedInfo',
					'valign' => 'top'
				),
				array(
					'text'   => '<b>' . $Config->getTitle() . '</b>',
					'addCls' => 'main',
					'valign' => 'top'
				),
				array(
					'text'   => $Configuration->getInputField($Config),
					'addCls' => 'main',
					'valign' => 'top'
				),
				array(
					'text'   => $Config->getDescription(),
					'addCls' => 'main',
					'valign' => 'top'
				)
			)
		));
	}
}

EventManager::notify(
	'ExtensionConfigureWindowAddFields',
	&$tabs,
	$_GET['extension']
);

$tabPanel = htmlBase::newElement('tabs')
	->addClass('makeTabPanel')
	->setId('module_tabs');
foreach($tabs as $pInfo){
	$tabPanel->addTabHeader($pInfo['panelId'], array('text' => $pInfo['panelHeader']))
		->addTabPage($pInfo['panelId'], array('text' => $pInfo['panelTable']));
}

EventManager::notify(
	'ExtensionConfigureWindowBeforeDraw',
	&$tabPanel,
	$_GET['extension']
);

$infoBox->addContentRow($tabPanel->draw());

EventManager::attachActionResponse($infoBox->draw(), 'html');
?>