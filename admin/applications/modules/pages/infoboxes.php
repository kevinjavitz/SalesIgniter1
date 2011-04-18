<?php
	$tableGrid = htmlBase::newElement('grid');

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_INFOBOXES_CODE')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFOBOXES_HEADING')),
			array('text' => sysLanguage::get('TABLE_HEADING_INFOBOXES_LOCATION')),
			array('text' => 'Installed')
		)
	));

	$modules = array();
	$dir = new DirectoryIterator($module_directory);
	foreach($dir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isFile() === true) continue;

		$className = 'InfoBox' . ucfirst($fileObj->getBaseName());
		if (!class_exists($className)){
			require($fileObj->getPathName() . '/infobox.php');
		}
		$classObj = new $className;
		$modules[$className] = array(
			'title'       => $classObj->getBoxHeading(),
			'code'        => $classObj->getBoxCode(),
			'installed'   => $classObj->isInstalled()
		);
	}

	$extDir = new DirectoryIterator($ext_module_directory);
	foreach($extDir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isFile()) continue;

		$extModuleDir = $fileObj->getPathname() . '/catalog/' . $module_type . '/';
		if (is_dir($extModuleDir)){
			$extModuleDir = new DirectoryIterator($extModuleDir);
			foreach($extModuleDir as $extModule){
				if ($extModule->isDot() || $extModule->isFile()) continue;

				$className = 'InfoBox' . ucfirst($extModule->getBaseName());
				if (!class_exists($className)){
					require($extModule->getPathname() . '/infobox.php');
				}
				$classObj = new $className;
				$modules[$className] = array(
					'title'     => $classObj->getBoxHeading(),
					'code'      => $classObj->getBoxCode(),
					'installed' => $classObj->isInstalled(),
					'extName'   => $extModule->getBasename()
				);
			}
		}
	}
	ksort($modules);

	$gridRows = array();
	$infoBoxes = array();
	foreach($modules as $className => $moduleInfo){
		$module = $moduleInfo['code'];

		$location = 'Core';
		$linkParams = 'module=' . $module;
		if (isset($moduleInfo['extName'])){
			$location = 'Extension: ' . $moduleInfo['extName'];
			$linkParams .= '&extName=' . $moduleInfo['extName'];
		}

		$onClickLink = itw_app_link($linkParams);

		$installIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to install')
		->setHref($onClickLink . '&action=install');

		$uninstallIcon = htmlBase::newElement('icon')->setType('circleCheck')->setTooltip('Click to uninstall')
		->setHref($onClickLink . '&action=remove');

		if ($moduleInfo['installed'] === true) {
			$installIcon->hide();
		} else {
			$uninstallIcon->hide();
		}

		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'infobox_id' => $module
			),
			'columns' => array(
				array('text' => $moduleInfo['code']),
				array('text' => $moduleInfo['title']),
				array('text' => $location),
				array('text' => $installIcon->draw() . $uninstallIcon->draw(), 'align' => 'center')
			)
		));

		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setButtonBarLocation('top');

		$infoBox->setHeader('<b>' . $moduleInfo['title'] . '</b>');

		if ($moduleInfo['installed'] === true) {
			$removeButton = htmlBase::newElement('button')->usePreset('uninstall')
			->setHref(itw_app_link($linkParams . '&action=remove'));

			$infoBox->addButton($removeButton);
		} else {
			$installButton = htmlBase::newElement('button')->usePreset('install')
			->setHref(itw_app_link($linkParams . '&action=install'));

			$infoBox->addButton($installButton);
		}

		$infoBoxes[$module] = $infoBox->draw();
		unset($infoBox);
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_MODULES_INFOBOXES');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
    <?php echo $tableGrid->draw();?>
   </div>
  </div>
 </div>
 <div style="width:25%;float:right;" id="infobox"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $module => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $module . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>