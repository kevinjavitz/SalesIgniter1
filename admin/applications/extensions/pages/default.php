<?php
$tableGrid = htmlBase::newElement('newGrid');

$tableGrid->addButtons(array(
	htmlBase::newElement('button')->usePreset('install')->addClass('installButton')->disable(),
	htmlBase::newElement('button')->usePreset('uninstall')->addClass('uninstallButton')->disable(),
	htmlBase::newElement('button')->usePreset('edit')->addClass('editButton')->disable()
));

$tableGrid->addHeaderRow(array(
	'columns' => array(
		array('text' => sysLanguage::get('TABLE_HEADING_EXTENSIONS')),
		array('text' => sysLanguage::get('TABLE_HEADING_INSTALLED')),
		array('text' => sysLanguage::get('TABLE_HEADING_ENABLED')),
		array('text' => sysLanguage::get('TABLE_HEADING_INFO'))
	)
));

$extensions = array();
$dirObj = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
foreach($dirObj as $dir){
	if ($dir->isDot() || $dir->isFile()) {
		continue;
	}

	$classObj = $appExtension->getExtension($dir->getBasename());
	if (!$classObj){
		$className = 'Extension_' . $dir->getBasename();
		if (!class_exists($className)){
			require($dir->getPathname() . '/ext.php');
		}

		$classObj = new $className;
	}

	if (sysPermissions::adminAccessAllowed('configure', 'configure', $classObj->getExtensionKey()) === true){
		$extensions[$dir->getBasename()] = $classObj;
	}
}

ksort($extensions);

foreach($extensions as $extCode => $extCls){
	$installedIcon = htmlBase::newElement('icon');
	if ($extCls->isInstalled() === true){
		$installedIcon->setType('circleCheck');
	}
	else {
		$installedIcon->setType('circleClose');
	}

	$enabledIcon = htmlBase::newElement('icon');
	if ($extCls->isEnabled() === true){
		$enabledIcon->setType('circleCheck');
	}
	else {
		$enabledIcon->setType('circleClose');
	}

	$tableGrid->addBodyRow(array(
		'rowAttr' => array(
			'data-extension_key' => $extCls->getExtensionKey(),
			'data-extension_path' => $extCls->getExtensionDir(),
			'data-can_install' => (file_exists($extCls->getExtensionDir() . '/install/install.php') === true ? 'true' : 'false'),
			'data-installed' => ($extCls->isInstalled() === true ? 'true' : 'false')
		),
		'columns' => array(
			array('text' => $extCls->getExtensionName()),
			array('align' => 'center', 'text' => $installedIcon->draw()),
			array('align' => 'center', 'text' => $enabledIcon->draw()),
			array('align' => 'center', 'text' => htmlBase::newElement('icon')->setType('info')->draw())
		)
	));

}
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<div>
	<div class="ui-widget ui-widget-content ui-corner-all" style="margin-right:5px;margin-left:5px;">
		<div style="margin:5px;">
			<?php echo $tableGrid->draw();?>
			<div class="smallText" style="padding:5px;"><?php echo sysLanguage::get('TEXT_EXTENSION_DIRECTORY') . ' ' . sysConfig::getDirFsCatalog() . 'extensions/';?></div>
		</div>
	</div>
</div>
