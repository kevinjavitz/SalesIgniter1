<?php
	$tableGrid = htmlBase::newElement('grid');

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_MODULES')),
			array('text' => sysLanguage::get('TABLE_HEADING_SORT_ORDER')),
			array('text' => 'Installed'),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$modules = array();
	$dir = new DirectoryIterator($module_directory);
	foreach($dir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isDir() === true) continue;

		$className = $fileObj->getBasename('.php');
		if (!class_exists($className)){
			sysLanguage::loadDefinitions(sysConfig::getDirFsCatalog() . 'includes/languages_phar/' . Session::get('language') . '/osc/modules/shipping/' . $fileObj->getBasename('.php') . '.xml');
			require($fileObj->getPathname());
		}
		$classObj = new $className;
		$modules[$className] = array(
			'title'       => $classObj->title,
			'code'        => $classObj->code,
			'description' => $classObj->description,
			'installed'   => ($classObj->check() > 0),
			'configKeys'  => $classObj->keys(),
			'enabled'     => $classObj->enabled,
			'sort_order'  => ($classObj->check() > 0 ? $classObj->sort_order : '')
		);

		if ($modules[$className]['installed'] === true){
			if ($classObj->sort_order > 0){
				$installedModules[$classObj->sort_order] = $fileObj->getBasename();
			}else{
				$installedModules[] = $fileObj->getBasename();
			}
		}
	}

	$extDir = new DirectoryIterator($ext_module_directory);
	foreach($extDir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isFile()) continue;

		$extModuleDir = $fileObj->getPathname() . '/' . $module_type . '/modules/';
		$extModuleLangDir = $fileObj->getPathname() . '/' . $module_type . '/language_defines/';
		if (is_dir($extModuleDir)){
			$extModuleDir = new DirectoryIterator($extModuleDir);
			foreach($extModuleDir as $extModule){
				if ($extModule->isDot() || $extModule->isDir()) continue;

				$className = $extModule->getBasename('.php');
				if (!class_exists($className)){
					require($extModule->getPathname());
				}
				$classObj = new $className;
				$modules[$className] = array(
					'title'       => $classObj->title,
					'code'        => $classObj->code,
					'description' => $classObj->description,
					'installed'   => ($classObj->check() > 0),
					'configKeys'  => $classObj->keys(),
					'enabled'     => $classObj->enabled,
					'sort_order'  => ($classObj->check() > 0 ? $classObj->sort_order : ''),
					'extName'     => $fileObj->getBasename()
				);

				if ($modules[$className]['installed'] === true){
					if ($classObj->sort_order > 0){
						$installedModules[$classObj->sort_order] = $extModule->getBasename();
					}else{
						$installedModules[] = $extModule->getBasename();
					}
				}
			}
		}

	}
	ksort($modules);

	$gridRows = array();
	$infoBoxes = array();
	foreach($modules as $className => $moduleInfo){
		$module = $moduleInfo['code'];

		$linkParams = 'set=' . $set . '&module=' . $module;
		if (isset($moduleInfo['extName'])){
			$linkParams .= '&extName=' . $moduleInfo['extName'];
		}

		$Qconfig = Doctrine_Query::create()
		->select('configuration_key, configuration_title, configuration_value, configuration_description, use_function, set_function')
		->from('Configuration')
		->orderBy('sort_order')
		->whereIn('configuration_key',  $moduleInfo['configKeys'])
		->execute();
		$keys_extra = array();
		if ($Qconfig->count() > 0){
			foreach($Qconfig->toArray() as $cInfo){
				$key = $cInfo['configuration_key'];
				$keys_extra[$key]['title'] = $cInfo['configuration_title'];
				$keys_extra[$key]['value'] = $cInfo['configuration_value'];
				$keys_extra[$key]['description'] = $cInfo['configuration_description'];
				$keys_extra[$key]['use_function'] = $cInfo['use_function'];
				$keys_extra[$key]['set_function'] = $cInfo['set_function'];
			}
		}
		$moduleInfo['keys'] = $keys_extra;

		if ($moduleInfo['installed'] === true){
			foreach($moduleInfo['configKeys'] as $key){
				if (!isset($moduleInfo['keys'][$key])){
					$moduleInfo['missingKeys'] = true;
					break;
				}
			}
		}

		if ((isset($_GET['module']) && $_GET['module'] == $module) && !isset($mInfo)){
			$mInfo = new objectInfo($moduleInfo);
		}

		$onClickLink = itw_app_link($linkParams);

		$arrowIcon = htmlBase::newElement('icon')->setType('info')
		->setHref($onClickLink);

		$installIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to install')
		->setHref($onClickLink . '&action=install');

		$uninstallIcon = htmlBase::newElement('icon')->setType('circleCheck')->setTooltip('Click to uninstall')
		->setHref($onClickLink . '&action=remove');

		if ($moduleInfo['installed'] === true) {
			$installIcon->hide();
		} else {
			$uninstallIcon->hide();
		}

		$sortOrder = '';
		if (is_numeric($moduleInfo['sort_order'])){
			$sortOrder = $moduleInfo['sort_order'];
		}

		$tableGrid->addBodyRow(array(
			'rowAttr' => array(
				'infobox_id' => $module
			),
			'columns' => array(
				array('text' => $moduleInfo['title']),
				array('text' => $sortOrder, 'align' => 'center', 'addCls' => 'sort-order-col'),
				array('text' => $installIcon->draw() . $uninstallIcon->draw(), 'align' => 'center'),
				array('text' => $arrowIcon->draw(), 'align' => 'right')
			)
		));

		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setButtonBarLocation('top');

		$infoBox->setHeader('<b>' . $moduleInfo['title'] . '</b>');
		$infoBox->addContentRow($moduleInfo['description']);

		if ($moduleInfo['installed'] === true) {
			$removeButton = htmlBase::newElement('button')->setText(sysLanguage::get('IMAGE_MODULE_REMOVE'))
			->setHref(itw_app_link($linkParams . '&action=remove'));

			$editButton = htmlBase::newElement('button')->setText(sysLanguage::get('IMAGE_EDIT'))
			->setHref(itw_app_link($linkParams . '&action=edit'));
			if ($set == 'payment'){
				$editButton->setHref(itw_app_link($linkParams, null, 'edit'));
			}

			$infoBox->addButton($removeButton)->addButton($editButton);

			if (isset($moduleInfo['missingKeys']) && $moduleInfo['missingKeys'] === true){
				$addMissing = htmlBase::newElement('button')->setText('Add Missing Config')
				->setHref(itw_app_link($linkParams . '&action=addMissingConfig'))
				->css('margin-top', '.3em');

				$infoBox->addButton($addMissing, true);
			}

			$keys = '';
			reset($moduleInfo['keys']);
			foreach($moduleInfo['keys'] as $value){
				$keys .= '<b>' . $value['title'] . '</b><br />';
				if ($value['use_function'] && $value['use_function'] != 'isArea') {
					$use_function = $value['use_function'];
					if (ereg('->', $use_function)) {
						$class_method = explode('->', $use_function);
						if (!is_object(${$class_method[0]})) {
							include(DIR_WS_CLASSES . $class_method[0] . '.php');
							${$class_method[0]} = new $class_method[0]();
						}
						$keys .= tep_call_function($class_method[1], $value['value'], ${$class_method[0]});
					} else {
						$keys .= tep_call_function($use_function, $value['value']);
					}
				} else {
					$keys .= $value['value'];
				}
				$keys .= '<br /><br />';
			}
			$keys = substr($keys, 0, strrpos($keys, '<br /><br />'));

			$infoBox->addContentRow($keys);
		} else {
			$installButton = htmlBase::newElement('button')->setText(sysLanguage::get('IMAGE_MODULE_INSTALL'))
			->setHref(itw_app_link($linkParams . '&action=install'));

			$infoBox->addButton($installButton);
		}

		$infoBoxes[$module] = $infoBox->draw();
		unset($infoBox);
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	switch ($action) {
		case 'edit':
			$keys = '';
			reset($mInfo->keys);
			while (list($key, $value) = each($mInfo->keys)) {
				$keys .= '<b>' . $value['title'] . '</b><br>' . $value['description'] . '<br>';

				if ($value['set_function'] && $value['set_function'] != 'isArea' ) {
					eval('$keys .= ' . $value['set_function'] . "'" . $value['value'] . "', '" . $key . "');");
				} else if ($value['set_function'] && $value['set_function'] == 'isArea' ) {
					$keys .= tep_draw_textarea_field('configuration[' . $key . ']','hard',30,5, $value['value'],'class="makeModFCK"');
				}else {
					$keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
				}
				$keys .= '<br><br>';
			}
			$keys = substr($keys, 0, strrpos($keys, '<br><br>'));

			$infoBox->setHeader('<b>' . $mInfo->title . '</b>');
			$infoBox->setForm(array(
				'name' => 'modules',
				'action' => itw_app_link('set=' . $set . '&module=' . $_GET['module'] . '&action=save')
			));

			$updateButton = htmlBase::newElement('button')->setType('submit')->setText(sysLanguage::get('IMAGE_UPDATE'));
			$cancelButton = htmlBase::newElement('button')->setText(sysLanguage::get('IMAGE_CANCEL'))
			->setHref(itw_app_link('set=' . $set . '&module=' . $_GET['module']));

			$infoBox->addButton($updateButton)->addButton($cancelButton);

			$infoBox->addContentRow($keys);

			$infoBoxes[$_GET['module']] = $infoBox->draw();
			break;
	}

	if (isset($installedModules) && is_array($installedModules)){
		ksort($installedModules);
	}

	$Qcheck = Doctrine_Query::create()
	->select('configuration_value')
	->from('Configuration')
	->where('configuration_key = ?', $module_key)
	->orderBy('sort_order')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qcheck){
		$installedString = (isset($installedModules) && is_array($installedModules) ? implode(';', $installedModules) : '');
		if ($Qcheck[0]['configuration_value'] != $installedString) {
			$QmodulesUpdate = Doctrine_Query::create()
			->update('Configuration')
			->set('configuration_value', '?', (!empty($installedString) ? $installedString : ''))
			->where('configuration_key = ?', $module_key)
			->execute();
		}
	}else{
		$newConfig = new Configuration();
		$newConfig->configuration_title = 'Installed Modules';
		$newConfig->configuration_key = $module_key;
		$newConfig->configuration_value = implode(';', $installedModules);
		$newConfig->configuration_description = 'This is automatically updated. No need to edit.';
		$newConfig->configuration_group_id = '6';
		$newConfig->sort_order = '0';
		$newConfig->save();
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
    <?php echo $tableGrid->draw();?>
    <div class="smallText" style="padding:5px;"><?php echo sysLanguage::get('TEXT_MODULE_DIRECTORY') . ' ' . $module_directory;?></div>
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