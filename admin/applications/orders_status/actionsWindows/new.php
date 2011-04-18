<?php
	$OrdersStatus = Doctrine_Core::getTable('OrdersStatus');
	if (isset($_GET['sID'])){
		$Status = $OrdersStatus->find((int) $_GET['sID']);
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_EDIT_ORDERS_STATUS');
		$boxIntro = sysLanguage::get('TEXT_INFO_EDIT_INTRO');
	}else{
		$Status = $OrdersStatus->getRecord();
		$boxHeading = sysLanguage::get('TEXT_INFO_HEADING_NEW_ORDERS_STATUS');
		$boxIntro = sysLanguage::get('TEXT_INFO_INSERT_INTRO');
	}

	$infoBox = htmlBase::newElement('infobox');
	$infoBox->setHeader('<b>' . $boxHeading . '</b>');
	$infoBox->setButtonBarLocation('top');

	$saveButton = htmlBase::newElement('button')->addClass('saveButton')->usePreset('save');
	$cancelButton = htmlBase::newElement('button')->addClass('cancelButton')->usePreset('cancel');

	$infoBox->addButton($saveButton)->addButton($cancelButton);

	$infoBox->addContentRow($boxIntro);
	
	$orders_status_inputs_string = '';
	foreach(sysLanguage::getLanguages() as $lInfo){
		$orders_status_inputs_string .= '<br>' . $lInfo['showName']('&nbsp;') . tep_draw_input_field('orders_status_name[' . $lInfo['id'] . ']', (isset($Status['OrdersStatusDescription'][$lInfo['id']]) ? $Status['OrdersStatusDescription'][$lInfo['id']]->orders_status_name : ''));
	}
      
	$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_ORDERS_STATUS_NAME') . $orders_status_inputs_string);
	
	if (isset($_GET['sID'])){
		if (sysConfig::get('DEFAULT_ORDERS_STATUS_ID') != $Status->orders_status_id){
			$infoBox->addContentRow(tep_draw_checkbox_field('default') . ' ' . sysLanguage::get('TEXT_SET_DEFAULT'));
		}
	}else{
		$infoBox->addContentRow(tep_draw_checkbox_field('default') . ' ' . sysLanguage::get('TEXT_SET_DEFAULT'));
	}
	
	EventManager::attachActionResponse($infoBox->draw(), 'html');
?>