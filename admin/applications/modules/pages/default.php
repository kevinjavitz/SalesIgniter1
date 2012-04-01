<?php
$tableGrid = htmlBase::newElement('newGrid');

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->usePreset('install')->addClass('installButton')->disable(),
	htmlBase::newElement('button')->usePreset('uninstall')->addClass('uninstallButton')->disable(),
	htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable()
));

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TABLE_HEADING_MODULES')),
		array('text' => sysLanguage::get('TABLE_HEADING_SORT_ORDER')),
		array('text' => sysLanguage::get('TABLE_HEADING_INSTALLED')),
		array('text' => sysLanguage::get('TABLE_HEADING_ENABLED')),
		array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
	)
));

$moduleDirs = array(
	sysConfig::getDirFsCatalog() . 'includes/modules/' . $moduleDirectory . '/'
);
$extensions = $appExtension->getExtensions();
foreach($extensions as $extCls){
	if ($extCls->isEnabled()){
		if (is_dir($extCls->getExtensionDir() . $moduleDirectory . '/')){
			$moduleDirs[] = $extCls->getExtensionDir() . $moduleDirectory . '/';
		}
	}
}

$modules = array();
foreach($moduleDirs as $dirName){
	$dirObj = new DirectoryIterator($dirName);
	foreach($dirObj as $dir){
		if ($dir->isDot() || $dir->isFile()) {
			continue;
		}

		$modules[$dir->getBasename()] = $accessorClass::getModule($dir->getBasename(), true);
	}
}

ksort($modules);

$gridRows = array();
$infoBoxes = array();
foreach($modules as $moduleCode => $moduleCls){
	$installedIcon = htmlBase::newElement('icon')
		->addClass('installedIcon');
	if ($moduleCls->isInstalled() === true){
		$installedIcon->setType('circleCheck');
	}
	else {
		$installedIcon->setType('circleClose');
	}

	$enabledIcon = htmlBase::newElement('icon')
		->addClass('enabledIcon');
	if ($moduleCls->isEnabled() === true){
		$enabledIcon->setType('circleCheck');
	}
	else {
		$enabledIcon->setType('circleClose');
	}

	$tableGrid->addBodyRow(array(
		'rowAttr' => array(
			'data-module_code' => $moduleCls->getCode(),
			'data-module_path' => $moduleCls->getPath(),
			'data-module_type' => $moduleCls->getModuleType(),
			'data-installed'   => ($moduleCls->isInstalled() === true ? 'true' : 'false')
		),
		'columns' => array(
			array('text' => $moduleCls->getTitle()),
			array('align' => 'center', 'text' => ($moduleCls->imported('SortedDisplay') ? $moduleCls->getDisplayOrder() : '')),
			array('align' => 'center', 'text' => $installedIcon->draw()),
			array('align' => 'center', 'text' => $enabledIcon->draw()),
			array('align' => 'center', 'text' => htmlBase::newElement('icon')->setType('info')->draw())
		)
	));

	$tableGrid->addBodyRow(array(
		'addCls'  => 'gridInfoRow',
		'columns' => array(
			array('colspan' => 5,
			      'text'    => '<table cellpadding="1" cellspacing="0" border="0" width="75%">' .
				      '<tr>' .
				      '<td>' . $moduleCls->getDescription() . '</td>' .
				      '</tr>' .
				      '</table>')
		)
	));
}
?>
<div class="pageHeading"><?php
	echo $headingTitle;
	?></div>
<br />
<div>
	<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
		<div style="margin:5px;"><?php echo $tableGrid->draw();?></div>
	</div>
</div>
