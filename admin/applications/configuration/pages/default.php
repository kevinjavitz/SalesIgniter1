<?php
$tableGrid = htmlBase::newElement('newGrid');

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TABLE_HEADING_CONFIGURATION_TITLE')),
		array('text' => sysLanguage::get('TABLE_HEADING_CONFIGURATION_VALUE')),
		array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
	)
));

$tabs = array();
$tabId = 1;

$Configuration = new MainConfigReader($_GET['key']);
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

$tabPanel = htmlBase::newElement('tabs')
	->addClass('makeTabPanel')
	->setId('config_tabs');
foreach($tabs as $pInfo){
	$tabPanel->addTabHeader($pInfo['panelId'], array('text' => $pInfo['panelHeader']))
		->addTabPage($pInfo['panelId'], array('text' => $pInfo['panelTable']));
}
?>
<script>
	var CONFIGURATION_GROUP_KEY = '<?php echo $_GET['key'];?>';
</script>
<div class="pageHeading"><?php
	echo $Configuration->getTitle();
	?></div>
<div style="margin:.5em;"><?php
	echo $Configuration->getDescription();
	?></div>
<div style="margin:.5em;text-align: right;"><?php
	echo htmlBase::newElement('button')->addClass('saveButton')->usePreset('save')->setText('Save Changes')->draw();
	?></div>
<br />
<div class="gridContainer">
	<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
		<div style="margin:5px;"><?php echo $tabPanel->draw();?></div>
	</div>
</div>
