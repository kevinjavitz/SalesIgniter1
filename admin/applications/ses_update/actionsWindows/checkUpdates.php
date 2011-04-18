<?php
	$wholeWindow = true;
	if (isset($_GET['fromUpdater']) && $_GET['fromUpdater'] == '1'){
		$wholeWindow = false;
	}
	
	if ($wholeWindow === true){
		$infoBox = htmlBase::newElement('infobox');
		$infoBox->setHeader('<b>' . sysLanguage::get('WINDOW_HEADING_CHECKING_UPDATES') . '</b>');
		$infoBox->setButtonBarLocation('top');

		$installButton = htmlBase::newElement('button')->addClass('installButton')->usePreset('save')->setText('Install');
		$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

		$infoBox->addButton($installButton)->addButton($cancelButton);
	}
	
	if (sysConfig::exists('SYSTEM_LAST_UPDATE') === false){
		$Config = new Configuration();
		$Config->configuration_group_id = 6;
		$Config->configuration_title = 'Last Update';
		$Config->configuration_key = 'SYSTEM_LAST_UPDATE';
		$Config->configuration_description = 'Last Installed Update Package';
		$Config->configuration_value = sysConfig::get('SYSTEM_INSTALL_DATE');
		$Config->save();
		
		sysConfig::set('SYSTEM_LAST_UPDATE', sysConfig::get('SYSTEM_INSTALL_DATE'));
	}
	
	if (sysConfig::exists('SYSTEM_UPDATER_LAST_UPDATE') === false){
		$Config = new Configuration();
		$Config->configuration_group_id = 6;
		$Config->configuration_title = 'Last Updater Update';
		$Config->configuration_key = 'SYSTEM_UPDATER_LAST_UPDATE';
		$Config->configuration_description = 'Last Installed Updater Package';
		$Config->configuration_value = '58';
		$Config->save();
		
		sysConfig::set('SYSTEM_UPDATER_LAST_UPDATE', '58');
	}
	
	$RequestObj = new CurlRequest('https://' . sysConfig::get('SYSTEM_UPGRADE_SERVER') . '/sesUpgrades/getUpdates.php');
	$RequestObj->setSendMethod('post');
	$RequestObj->setData(array(
		'action' => 'process',
		'version' => 1,
		'last_update' => sysConfig::get('SYSTEM_LAST_UPDATE'),
		'last_updater_update' => sysConfig::get('SYSTEM_UPDATER_LAST_UPDATE'),
		'username' => sysConfig::get('SYSTEM_UPGRADE_USERNAME'),
		'password' => sysConfig::get('SYSTEM_UPGRADE_PASSWORD'),
		'domain' => $_SERVER['HTTP_HOST']
	));

	$ResponseObj = $RequestObj->execute();

	$json = json_decode($ResponseObj->getResponse());
	if ($json->success === true){
		$updatesTable = htmlBase::newElement('table')
		->addClass('updatesTable')
		->setCellPadding(2)
		->setCellSpacing(0);
	
		if (isset($json->forUpdater)){
			$updatesTable->addClass('updaterUpdate');
			foreach ($json->updates as $uInfo){
				$updates[strtotime($uInfo->date)] = array(
					'number' => $uInfo->update_num,
					'name' => $uInfo->name,
					'date' => $uInfo->date,
					'description' => $uInfo->description
				);
			}
		}else{
			$updates = array();
			foreach ($json->updates as $uInfo){
				$updates[strtotime($uInfo->date)] = array(
					'number' => $uInfo->update_num,
					'name' => $uInfo->name,
					'date' => $uInfo->date,
					'description' => $uInfo->description
				);
			
				$SesUpdates = Doctrine_Core::getTable('SesUpdates');
				$Update = $SesUpdates->findOneByUpdateNumber($uInfo->update_num);
				if (!$Update){
					$Update = $SesUpdates->create();
					$Update->update_number = $uInfo->update_num;
					$Update->update_name = strip_tags($uInfo->name);
					$Update->update_description = $uInfo->description;
					$Update->update_status = 0;
					$Update->update_latest_commit = $uInfo->latest_commit;
					$Update->save();
				}
			}
		}
		
		if (!empty($updates)){
			ksort($updates);
			foreach($updates as $uInfo){
				$updatesTable->addBodyRow(array(
					'addCls' => $uInfo['number'],
					'columns' => array(
						array(
							'valign' => 'top',
							'text' => '<input type=checkbox style="display:none;" name="update[]" value="' . $uInfo['number'] . '" checked=checked>'
						),
						array(
							'text' => $uInfo['name'] . '<br>' . 
										sysLanguage::get('TEXT_INFO_DATE_RELEASED') . $uInfo['date'] . '<br>' . 
										$uInfo['description']
						)
					)
				));
			}
		}else{
			$updatesTable->addBodyRow(array(
				'columns' => array(
					array(
						'valign' => 'top',
						'text' => sysLanguage::get('TEXT_INFO_UP_TO_DATE')
					)
				)
			));
		}
	
		if ($wholeWindow === true){
			$infoBox->addContentRow($updatesTable->draw());
			$responseHtml = $infoBox->draw();
		}else{
			$responseHtml = $updatesTable->draw();
		}
	}else{
		if ($wholeWindow === true){
			$infoBox->addContentRow(sprintf(sysLanguage::get('TEXT_ERROR_RETRIEVING_UPDATES'), $json->errorMsg));
			$responseHtml = $infoBox->draw();
		}else{
			$responseHtml = sprintf(sysLanguage::get('TEXT_ERROR_RETRIEVING_UPDATES'), $json->errorMsg);
		}
	}
	
	EventManager::attachActionResponse($responseHtml, 'html');
?>