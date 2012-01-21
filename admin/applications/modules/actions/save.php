<?php
	if (isset($_GET['moduleType']) && ($_GET['moduleType'] == 'orderTotal' || $_GET['moduleType'] == 'orderPayment' || $_GET['moduleType'] == 'orderShipping')){
		while (list($key, $value) = each($_POST['configuration'])){
			if (is_array($value)){
				$value = implode(',', $value);

				if (substr($value, -1) == ','){
					$value = substr($value, 0, -1);
				}
			}

			Doctrine_Query::create()->update('ModulesConfiguration')
			->set('configuration_value', '?', $value)
			->where('configuration_key = ?', $key)
			->execute();
		}
		
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
	
		if (file_exists(sysConfig::getDirFsCatalog() . 'includes/modules/' . $moduleDir . '/' . $moduleCode . '/actions/save.php')){
			require(sysConfig::getDirFsCatalog() . 'includes/modules/' . $moduleDir . '/' . $moduleCode . '/actions/save.php');
		}
	}else{
		while (list($key, $value) = each($_POST['configuration'])){
			if (is_array($value)){
				$value = implode(',', $value);

				if (substr($value, -1) == ','){
					$value = substr($value, 0, -1);
				}
			}

			Doctrine_Query::create()->update('Configuration')
			->set('configuration_value', '?', $value)->where('configuration_key = ?', $key)->execute();
		}
	}
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>