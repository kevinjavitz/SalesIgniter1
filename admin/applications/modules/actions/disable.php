<?php
if (isset($_GET['moduleType']) && ($_GET['moduleType'] == 'orderTotal' || $_GET['moduleType'] == 'orderPayment' || $_GET['moduleType'] == 'orderShipping' || $_GET['moduleType'] == 'purchaseType' || $_GET['moduleType'] == 'productType')){
	$moduleCode = $_GET['module'];
	$moduleType = $_GET['moduleType'];
	$modulePath = $_GET['modulePath'];

	$Module = Doctrine_Core::getTable('Modules')
		->findOneByModulesCodeAndModulesType($moduleCode, $moduleType);
	if (!$Module){
		$Module = new Modules();
		$Module->modules_code = $moduleCode;
		$Module->modules_type = $moduleType;
	}

	$Configuration = $Module->ModulesConfiguration;
	$Configuration['STATUS']->configuration_value = 'False';

	if (file_exists($modulePath . '/actions/disable.php')){
		require($modulePath . '/actions/disable.php');
	}

	$Module->save();
}

EventManager::attachActionResponse(array(
		'success' => true
	), 'json');