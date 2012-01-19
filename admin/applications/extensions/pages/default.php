<?php
	$tableGrid = htmlBase::newElement('grid');

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_EXTENSIONS')),
			array('text' => 'Installed'),
			array('text' => 'Enabled'),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$dir = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
	$sorted = array();
	foreach($dir as $fileObj){
		if ($fileObj->isDot() || $fileObj->isFile()) continue;
		
		$sorted[] = $fileObj->getBasename();
	}
	sort($sorted);
	
	$extensions = array();
	foreach($sorted as $extensionName){
		$className = 'Extension_' . $extensionName;
		if (!class_exists($className)){
			require(sysConfig::getDirFsCatalog() . 'extensions/' . $extensionName . '/ext.php');
		}
		$classObj = new $className;
		if (sysPermissions::adminAccessAllowed('configure', 'configure', $classObj->getExtensionKey()) === true){
			$extensions[] = array(
				'name'        => $classObj->getExtensionName(),
				'dir'         => $classObj->getExtensionDir(),
				'version'     => $classObj->getExtensionVersion(),

				'key'         => $classObj->getExtensionKey(),
				'configKeys'  => $classObj->getExtensionConfigKeys(),
				'installed'   => $classObj->isInstalled(),
				'enabled'     => $classObj->isEnabled(),
				'canInstall'  => file_exists($classObj->getExtensionDir() . '/install/install.php')
			);
		}
	}

	$allowedUpgrades = false;
	if (Session::exists('AllowedUpgrades')){
		$allowedUpgrades = explode(',', Session::get('AllowedUpgrades'));
	}

	$gridRows = array();
	$infoBoxes = array();
	for ($i=0, $n=sizeof($extensions); $i<$n; $i++) {
		if ($extensions[$i]['canInstall'] === false) continue;
		$extension = $extensions[$i]['key'];
		
		if ($extensions[$i]['installed'] === true){
			$keys_extra = array();
			if (!empty($extensions[$i]['configKeys'])){
				$Qconfig = Doctrine_Query::create()
				->select('configuration_key, configuration_title, configuration_value, configuration_description, use_function, set_function')
				->from('Configuration')
				->whereIn('configuration_key', $extensions[$i]['configKeys'])
				->orderBy('sort_order')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				if ($Qconfig){
					foreach($Qconfig as $cInfo){
						$key = $cInfo['configuration_key'];
						$keys_extra[$key]['title'] = $cInfo['configuration_title'];
						$keys_extra[$key]['value'] = $cInfo['configuration_value'];
						$keys_extra[$key]['description'] = $cInfo['configuration_description'];
						$keys_extra[$key]['use_function'] = $cInfo['use_function'];
						$keys_extra[$key]['set_function'] = $cInfo['set_function'];
					}
				}
			}
			$extensions[$i]['keys'] = $keys_extra;
		
			if ($extensions[$i]['installed'] === true){
				foreach($extensions[$i]['configKeys'] as $key){
					if (!isset($extensions[$i]['keys'][$key])){
						$extensions[$i]['missingKeys'] = true;
						break;
					}
				}
			}
		}
			
		if ((!isset($_GET['ext']) || (isset($_GET['ext']) && ($_GET['ext'] == $extension))) && !isset($eInfo)){
			$eInfo = new objectInfo($extensions[$i]);
		}

		$onClickLink = itw_app_link('ext=' . $extension);
		
		$arrowIcon = htmlBase::newElement('icon')->setType('info')
		->setHref($onClickLink);

		$installIcon = htmlBase::newElement('icon')->setType('circleClose')->setTooltip('Click to install')
		->setHref($onClickLink . '&action=install');

		$uninstallIcon = htmlBase::newElement('icon')->setType('circleCheck')->setTooltip('Click to uninstall')
		->setHref($onClickLink . '&action=uninstall');

		if ($extensions[$i]['installed'] === true){
			$installIcon->hide();
		} else {
			$uninstallIcon->hide();
		}

		$enabledIcon = htmlBase::newElement('icon')->setType('circleClose');
		if ($extensions[$i]['enabled'] === true){
			$enabledIcon = htmlBase::newElement('icon')->setType('circleCheck');
		}

		$uninstallIcon = htmlBase::newElement('icon')->setType('circleCheck')->setTooltip('Click to uninstall')
		->setHref($onClickLink . '&action=uninstall');

		if ($extensions[$i]['installed'] === true){
			$installIcon->hide();
		} else {
			$uninstallIcon->hide();
		}

		$addCls = '';
		if ($allowedUpgrades !== false){
			if (in_array($extension, $allowedUpgrades)){
				$addCls .= ' ui-state-warning';
			}
		}
		
		$tableGrid->addBodyRow(array(
			'addCls'  => $addCls,
			'rowAttr' => array(
				'infobox_id' => $extension
			),
			'columns' => array(
				array('text' => $extensions[$i]['name']),
				array('text' => $installIcon->draw() . $uninstallIcon->draw(), 'align' => 'center'),
				array('text' => $enabledIcon->draw(), 'align' => 'center'),
				array('text' => $arrowIcon->draw(), 'align' => 'right')
			)
		));
		
		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setButtonBarLocation('top');
		$infoBox->setHeader('<b>' . $extensions[$i]['name'] . '</b>');
		$infoBox->addContentRow('Version: ' . $extensions[$i]['version']);

		if (!empty($extensions[$i]['description'])){
			$infoBox->addContentRow($extensions[$i]['description']);
		}
		
		if ($extensions[$i]['installed'] === true) {
			$removeButton = htmlBase::newElement('button')->usePreset('uninstall')
			->setHref(itw_app_link('ext=' . $extensions[$i]['key'] . '&action=pre_uninstall'));

			$editButton = htmlBase::newElement('button')->usePreset('edit')
			->setHref(itw_app_link('ext=' . $extensions[$i]['key'] . '&action=edit'));

			$infoBox->addButton($removeButton)->addButton($editButton);

			$allowedUpgrades = false;
			if (Session::exists('AllowedUpgrades')){
				$allowedUpgrades = explode(',', Session::get('AllowedUpgrades'));
			}
				
			if ($allowedUpgrades === false || !in_array($extensions[$i]['key'], $allowedUpgrades)){
				$checkUpgradeButton = htmlBase::newElement('button')->setText('Check For Upgrades')
				->setHref(itw_app_link('ext=' . $extensions[$i]['key'] . '&action=checkUpgrades'))
				->css('margin-top', '.3em');
				
				$infoBox->addButton($checkUpgradeButton, true);
			}elseif (in_array($extensions[$i]['key'], $allowedUpgrades)){
				$upgradeButton = htmlBase::newElement('button')->setText('Upgrade')
				->setHref(itw_app_link('ext=' . $extensions[$i]['key'] . '&action=runUpgrades'))
				->css('margin-top', '.3em');
					
				$infoBox->addButton($upgradeButton, true);
			}
				
			if (isset($extensions[$i]['missingKeys']) && $extensions[$i]['missingKeys'] === true){
				$addMissing = htmlBase::newElement('button')->setText('Add Missing Config')
				->setHref(itw_app_link('ext=' . $extensions[$i]['key'] . '&action=addMissingConfig'))
				->css('margin-top', '.3em');
					
				$infoBox->addButton($addMissing, true);
			}

			$keys = '';
			foreach($extensions[$i]['keys'] as $value){
				$keys .= '<b>' . $value['title'] . '</b><br>';
				if ($value['use_function']) {
					$use_function = $value['use_function'];
					if (stristr($use_function, '->')) {
						$class_method = explode('->', $use_function);
						if (!is_object(${$class_method[0]})) {
							include(sysConfig::get('DIR_WS_CLASSES') . $class_method[0] . '.php');
							${$class_method[0]} = new $class_method[0]();
						}
						$keys .= tep_call_function($class_method[1], $value['value'], ${$class_method[0]});
					} else {
						$keys .= tep_call_function($use_function, $value['value']);
					}
				} else {
					$keys .= $value['value'];
				}
				$keys .= '<br><br>';
			}
			$keys = substr($keys, 0, strrpos($keys, '<br><br>'));

			$infoBox->addContentRow($keys);
		} else {
			$installButton = htmlBase::newElement('button')->setText(sysLanguage::get('IMAGE_INSTALL'))
			->setHref(itw_app_link('ext=' . $extensions[$i]['key'] . '&action=install&showErrors'));

			$infoBox->addButton($installButton);
		}
		$infoBoxes[$extension] = $infoBox->draw();
		unset($infoBox);
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	switch ($action) {
		case 'edit':
			$keys = '';
			reset($eInfo->keys);
			while (list($key, $value) = each($eInfo->keys)) {
				$keys .= '<b>' . $value['title'] . '</b><br>' . $value['description'] . '<br>';

				if (!empty($value['set_function']) && $value['set_function'] != 'isArea') {
					eval('$keys .= ' . $value['set_function'] . '"' . $value['value'] . '", "' . $key . '");');
				}else if (!empty($value['set_function']) && $value['set_function'] == 'isArea') {
					$keys .= tep_draw_textarea_field('configuration[' . $key . ']','soft','40','15', $value['value']);
				}else {
					$keys .= tep_draw_input_field('configuration[' . $key . ']', $value['value']);
				}
				$keys .= '<br><br>';
			}
			$keys = substr($keys, 0, strrpos($keys, '<br><br>'));

			$infoBox->setHeader('<b>' . $eInfo->name . '</b>');
			$infoBox->setForm(array(
				'name' => 'modules',
				'action' => itw_app_link('ext=' . $eInfo->key . '&action=save')
			));

			$updateButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
			$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
			->setHref(itw_app_link('ext=' . $eInfo->key));

			$infoBox->addButton($updateButton)->addButton($cancelButton);

			$infoBox->addContentRow($keys);
			$infoBoxes[$eInfo->key] = $infoBox->draw();
			break;
		case 'pre_uninstall':
			$infoBox->setHeader('<b>' . $eInfo->name . '</b>');
			$infoBox->setForm(array(
				'name' => 'modules',
				'action' => itw_app_link('ext=' . $eInfo->key . '&action=uninstall')
			));

			$uninstallButton = htmlBase::newElement('button')->setType('submit')->usePreset('uninstall');
			$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
			->setHref(itw_app_link('ext=' . $eInfo->key));

			$infoBox->addButton($uninstallButton)->addButton($cancelButton);

			$checkBox = htmlBase::newElement('checkbox')
			->setName('remove')
			->setLabel('Remove all settings/tables')
			->setLabelPosition('after')
			->setValue('1');
			
			$infoBox->addContentRow('Removing all settings will remove all database tables and settings associated with this plugin, it is not reversable.');
			$infoBox->addContentRow($checkBox->draw());
			
			$infoBoxes[$eInfo->key] = $infoBox->draw();
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
    <?php echo $tableGrid->draw();?>
    <div class="smallText" style="padding:5px;"><?php echo sysLanguage::get('TEXT_EXTENSION_DIRECTORY') . ' ' . sysConfig::getDirFsCatalog() . 'extensions/';?></div>
   </div>
  </div>
 </div>
 <div style="width:25%;float:right;" id="infobox"><?php
 	if (sizeof($infoBoxes) > 0){
 		foreach($infoBoxes as $extension => $html){
 			echo '<div class="infoboxContainer" id="infobox_' . $extension . '" style="display:none;">' . $html . '</div>';
 		}
 	}
 ?></div>