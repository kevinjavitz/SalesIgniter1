<?php
	switch($_GET['moduleType']){
		case 'orderShipping':
			$moduleDir = 'orderShippingModules';
			break;
		case 'orderTotal':
			$moduleDir = 'orderTotalModules';
			break;
		case 'orderPayment':
			$moduleDir = 'orderPaymentModules';
			break;
		case 'infoboxes':
			$moduleDir = 'infoboxes';
			break;
	}
	$moduleCode = $_GET['module'];
	
	if (file_exists(sysConfig::getDirFsCatalog() . 'includes/modules/' . $moduleDir . '/' . $moduleCode . '/actionsWindows/' . $_GET['window'] . '.php')){
		require(sysConfig::getDirFsCatalog() . 'includes/modules/' . $moduleDir . '/' . $moduleCode . '/actionsWindows/' . $_GET['window'] . '.php');
	}else{
		require(sysConfig::getDirFsAdmin() . 'applications/modules/actionsWindows/' . $_GET['window'] . '.php');
	}
?>