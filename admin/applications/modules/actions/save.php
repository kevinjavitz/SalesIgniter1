<?php
if (isset($_GET['moduleType']) && ($_GET['moduleType'] == 'orderTotal' || $_GET['moduleType'] == 'orderPayment' || $_GET['moduleType'] == 'orderShipping' || $_GET['moduleType'] == 'purchaseType')){
	$moduleCode = $_GET['module'];
	$moduleType = $_GET['moduleType'];

	switch($moduleType){
		case 'purchaseType':
			$moduleDir = 'purchaseTypeModules';
			break;
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

	$Module = Doctrine_Core::getTable('Modules')
		->findOneByModulesCodeAndModulesType($moduleCode, $moduleType);
	if (!$Module){
		$Module = new Modules();
		$Module->modules_code = $moduleCode;
		$Module->modules_type = $moduleType;
	}

	$Configuration = $Module->ModulesConfiguration;
	$configKeys = array();
	while(list($key, $value) = each($_POST['configuration'])){
		if (is_array($value)){
			$value = implode(',', $value);

			if (substr($value, -1) == ','){
				$value = substr($value, 0, -1);
			}
		}

		$Configuration[$key]->configuration_key = $key;
		$Configuration[$key]->configuration_value = $value;

		$configKeys[] = $key;
	}

	if (file_exists(sysConfig::getDirFsCatalog() . 'includes/modules/' . $moduleDir . '/' . $moduleCode . '/actions/save.php')){
		require(sysConfig::getDirFsCatalog() . 'includes/modules/' . $moduleDir . '/' . $moduleCode . '/actions/save.php');
	}

	$Module->save();

	Doctrine_Query::create()
		->delete('ModulesConfiguration')
		->where('modules_id = ?', $Module->modules_id)
		->andWhereNotIn('configuration_key', $configKeys)
		->execute();
}
else {
	while(list($key, $value) = each($_POST['configuration'])){
		if (is_array($value)){
			$value = implode(',', $value);

			if (substr($value, -1) == ','){
				$value = substr($value, 0, -1);
			}
		}

		Doctrine_Query::create()->update('Configuration')
			->set('configuration_value', '?', $value)
			->where('configuration_key = ?', $key)
			->execute();
	}
}

EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>