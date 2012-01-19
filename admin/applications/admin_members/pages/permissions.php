<?php
	$Qgroup = Doctrine_Query::create()
	->from('AdminGroups')
	->where('admin_groups_id = ?', (int) $_GET['gID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$groupId = $Qgroup[0]['admin_groups_id'];
	$groupName = $Qgroup[0]['admin_groups_name'];
	
	$Qpermissions = Doctrine_Query::create()
	->from('AdminApplicationsPermissions')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$perms = array();
	foreach($Qpermissions as $pInfo){
		$permissions = explode(',', $pInfo['admin_groups']);
		if (!empty($pInfo['extension'])){
			$perms['ext'][$pInfo['extension']][$pInfo['application']][$pInfo['page']] = in_array($groupId, $permissions);
		}else{
			$perms[$pInfo['application']][$pInfo['page']] = in_array($groupId, $permissions);
		}
	}

	$Applications = new DirectoryIterator(sysConfig::getFirFsAdmin() . 'applications/');
	$AppArray = array();
	foreach($Applications as $AppDir){
		if ($AppDir->isDot() || $AppDir->isFile()) continue;
		$appName = $AppDir->getBasename();
		
		$AppArray[$appName] = array();
		
		if (is_dir($AppDir->getPathname() . '/pages/')){
			$Pages = new DirectoryIterator($AppDir->getPathname() . '/pages/');
			foreach($Pages as $Page){
				if ($Page->isDot() || $Page->isDir()) continue;
				$pageName = $Page->getBasename();
				
				$AppArray[$appName][$pageName] = (isset($perms[$appName][$pageName]) ? $perms[$appName][$pageName] : false);
			}
		}
		ksort($AppArray[$appName]);
	}
		
	$Extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
	foreach($Extensions as $Extension){
		if ($Extension->isDot() || $Extension->isFile()) continue;
		
		if (is_dir($Extension->getPathName() . '/admin/base_app/')){
			$extName = $Extension->getBasename();
			
			$AppArray['ext'][$extName] = array();
			
			$ExtApplications = new DirectoryIterator($Extension->getPathname() . '/admin/base_app/');
			$AppArray['ext'][$extName]['configure']['configure.php'] = (isset($perms['ext'][$extName]['configure']['configure.php']) ? $perms['ext'][$extName]['configure']['configure.php'] : false);
			foreach($ExtApplications as $ExtApplication){
				if ($ExtApplication->isDot() || $ExtApplication->isFile()) continue;
				$appName = $ExtApplication->getBasename();
				
				$AppArray['ext'][$extName][$appName] = array();

				if (is_dir($ExtApplication->getPathname() . '/pages/')){
					$ExtPages = new DirectoryIterator($ExtApplication->getPathname() . '/pages/');
					foreach($ExtPages as $ExtPage){
						if ($ExtPage->isDot() || $ExtPage->isDir()) continue;
						$pageName = $ExtPage->getBasename();
						
						$AppArray['ext'][$extName][$appName][$pageName] = (isset($perms['ext'][$extName][$appName][$pageName]) ? $perms['ext'][$extName][$appName][$pageName] : false);
					}
				}
				ksort($AppArray['ext'][$extName][$appName]);
			}
			ksort($AppArray['ext']);
		}
	}
		
	$Extensions = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/');
	foreach($Extensions as $Extension){
		if ($Extension->isDot() || $Extension->isFile()) continue;
		
		if (is_dir($Extension->getPathName() . '/admin/ext_app/')){
			$ExtCheck = new DirectoryIterator($Extension->getPathname() . '/admin/ext_app/');
			foreach($ExtCheck as $eInfo){
				if ($eInfo->isDot() || $eInfo->isFile()) continue;
				
				if (is_dir($eInfo->getPathName() . '/pages')){
					$appName = $eInfo->getBasename();
					
					$Pages = new DirectoryIterator($eInfo->getPathname() . '/pages/');
					foreach($Pages as $Page){
						if ($Page->isDot() || $Page->isDir()) continue;
						$pageName = $Page->getBasename();
						
						if (!isset($AppArray[$appName][$pageName])){
							$AppArray[$appName][$pageName] = (isset($perms[$appName][$pageName]) ? $perms[$appName][$pageName] : false);
						}
					}
				}elseif (isset($AppArray['ext'][$eInfo->getBasename()])){
					$Apps = new DirectoryIterator($eInfo->getPathName());
					$extName = $eInfo->getBasename();
					
					foreach($Apps as $App){
						if ($App->isDot() || $App->isFile()) continue;
						$appName = $App->getBasename();
						
						if (is_dir($App->getPathname() . '/pages')){
							$Pages = new DirectoryIterator($App->getPathname() . '/pages/');
							foreach($Pages as $Page){
								if ($Page->isDot() || $Page->isDir()) continue;
								$pageName = $Page->getBasename();
								
								if (!isset($AppArray['ext'][$extName][$App->getBasename()])){
									$AppArray['ext'][$extName][$App->getBasename()] = array();
								}
								
								$AppArray['ext'][$extName][$appName][$pageName] = (isset($perms['ext'][$extName][$appName][$pageName]) ? $perms['ext'][$extName][$appName][$pageName] : false);
							}
						}
					}
				}
			}
		}
	}
	
	ksort($AppArray);
	
	//echo '<pre>';print_r($AppArray);
	
	$BoxesTable = htmlBase::newElement('table')
	->setCellPadding(3)
	->setCellSpacing(0)
	->css(array('width' => '100%'));
	$col = 0;
	foreach($AppArray as $appName => $aInfo){
		if ($appName == 'ext') continue;
		
		if (!empty($aInfo)){
			$checkboxes = '<div class="ui-widget-header" style="margin: .5em;"><input type="checkbox" class="appBox checkAllPages"> ' . $appName . '</div>';
			foreach($aInfo as $pageName => $pageChecked){
				$checkboxes .= '<div style="margin: 0 0 0 1.5em;"><input class="pageBox" type="checkbox" name="applications[' . $appName . '][]" value="' . $pageName . '"' . ($pageChecked === true ? ' checked="checked"' : '') . '> ' . $pageName . '</div>';
			}
			$bodyCols[] = array(
				'valign' => 'top',
				'text' => '<div class="ui-widget ui-widget-content">' . $checkboxes . '</div>'
			);
		
			$col++;
			if ($col > 2){
				$BoxesTable->addBodyRow(array(
					'columns' => $bodyCols
				));
				$bodyCols = array();
				$col = 0;
			}
		}
	}
	
	foreach($AppArray['ext'] as $ExtName => $eInfo){
		if (!empty($eInfo)){
			$checkboxes = '<div class="ui-widget-header" style="margin: .5em;"><input type="checkbox" class="extensionBox checkAllApps"> ' . $ExtName . '</div>';
			foreach($eInfo as $appName => $aInfo){
				$checkboxes .= '<div><div class="ui-state-hover" style="margin: .5em .5em 0 .5em"><input type="checkbox" class="appBox checkAllPages"> ' . $appName . '</div>';
				foreach($aInfo as $pageName => $pageChecked){
					$checkboxes .= '<div style="margin: 0 0 0 1.5em;"><input type="checkbox" class="pageBox" name="applications[ext][' . $ExtName . '][' . $appName . '][]" value="' . $pageName . '"' . ($pageChecked === true ? ' checked="checked"' : '') . '> ' . $pageName . '</div>';
				}
				$checkboxes .= '</div>';
			}
			$bodyCols[] = array(
				'valign' => 'top',
				'text' => '<div class="ui-widget ui-widget-content">' . $checkboxes . '</div>'
			);
		
			$col++;
			if ($col > 2){
				$BoxesTable->addBodyRow(array(
					'columns' => $bodyCols
				));
				$bodyCols = array();
				$col = 0;
			}
		}
	}
	
	if (!empty($bodyCols)){
		$BoxesTable->addBodyRow(array(
			'columns' => $bodyCols
		));
	}
	
	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setButtonBarLocation('top');

	$allGetParams = tep_get_all_get_params(array('action', 'gID'));
	
	$formLink = itw_app_link('gID=' . $_GET['gID']);
	if ($groupId != 1){
		$formLink .= '&action=defineGroup';
	}
	
	$infoBox->setHeader('<b>"' . $groupName . '" ' . sysLanguage::get('TABLE_HEADING_GROUPS_DEFINE') . '</b>');
	$infoBox->setForm(array(
		'name'   => 'permissions',
		'action' => $formLink
	));
	
	$infoBox->addContentRow($BoxesTable->draw());

	EventManager::notify('AdminExtraPermissions', &$infoBox, $_GET['gID']);

	if ($groupId != 1){
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link('gID=' . $groupId, 'admin_members', 'groups'));

		$saveButton = htmlBase::newElement('button')->usePreset('save')->setType('submit');
		
		$infoBox->addButton($saveButton)->addButton($cancelButton);
	}else{
		$backButton = htmlBase::newElement('button')->usePreset('back')
		->setHref(itw_app_link('gID=' . $groupId, 'admin_members', 'groups'));
		
		$infoBox->addButton($backButton);
	}
	
	echo $infoBox->draw();
?>