<?php
	$tableGrid = htmlBase::newElement('newGrid');

$tableGrid->addButtons(array(
		htmlBase::newElement('button')->setText('Enable')->addClass('enableButton')->disable(),
		htmlBase::newElement('button')->setText('Disable')->addClass('disableButton')->disable(),
		htmlBase::newElement('button')->setText('Edit')->addClass('editButton')->disable()
	));

$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_MODULES')),
			array('text' => sysLanguage::get('TABLE_HEADING_ENABLED')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
		)
	));

$moduleDirs = array(
	sysConfig::getDirFsCatalog() . 'includes/modules/purchaseTypeModules/'
);
$extensions = $appExtension->getExtensions();
foreach($extensions as $extCls){
	if ($extCls->isEnabled()){
		if (is_dir($extCls->getExtensionDir() . 'purchaseTypeModules/')){
			$moduleDirs[] = $extCls->getExtensionDir() . 'purchaseTypeModules/';
		}
	}
}

$modules = array();
foreach($moduleDirs as $dirName){
	$dirObj = new DirectoryIterator($dirName);
	foreach($dirObj as $dir){
		if ($dir->isDot() || $dir->isFile()) continue;

		$className = 'PurchaseType_' . ucfirst($dir->getBasename());
		if (!class_exists($className)){
			require($dir->getPathname() . '/module.php');
		}
		$modules[$dir->getBasename()] = new $className;
	}
}

ksort($modules);

$gridRows = array();
$infoBoxes = array();
foreach($modules as $moduleCode => $moduleCls){
	$installedIcon = htmlBase::newElement('icon');
	if ($moduleCls->isEnabled() === true) {
		$installedIcon->setType('circleCheck');
	} else {
		$installedIcon->setType('circleClose');
	}

	$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'data-module_code' => $moduleCode,
				'data-module_type' => 'purchaseType',
				'data-enabled'   => ($moduleCls->isEnabled() === true ? 'true' : 'false')
			),
			'columns' => array(
				array('text' => $moduleCls->getTitle()),
				array('align' => 'center', 'text' => $installedIcon->draw()),
				array('align' => 'center', 'text' => htmlBase::newElement('icon')->setType('info')->draw())
			)
		));

	$tableGrid->addBodyRow(array(
			'addCls' => 'gridInfoRow',
			'columns' => array(
				array('colspan' => 4, 'text' => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' .
					'<tr>' .
					'<td>' . $moduleCls->getDescription() . '</td>' .
					'</tr>' .
					'</table>')
			)
		));
}
?>
<div class="pageHeading"><?php
	echo sysLanguage::get('HEADING_TITLE_MODULES_PURCHASE_TYPE');
	?></div>
<br />
<div class="gridContainer">
	<div style="width:100%;float:left;">
		<div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
			<div style="width:99%;margin:5px;"><?php echo $tableGrid->draw();?></div>
		</div>
	</div>
</div>