<?php
	$Providers = Doctrine_Core::getTable('ProductsStreamProviders');
	if (isset($_GET['pID'])){
		$Provider = $Providers->find((int) $_GET['pID']);
	}else{
		$Provider = $Providers->create();
	}
	
	$Provider->provider_name = $_POST['provider_name'];
	$Provider->provider_module = $_POST['provider_module'];
	
	$providerSettings = array();
	if (isset($_POST['configuration'])){
		while (list($key, $value) = each($_POST['configuration'])){
			if (is_array($value)){
				$value = implode(',', $value);

				if (substr($value, -1) == ','){
					$value = substr($value, 0, -1);
				}
			}

			$Qcheck = Doctrine_Query::create()
			->select('configuration_value')
			->from('ModulesConfiguration')
			->where('configuration_key = ?', $key)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			if ($Qcheck){
				if ($value != $Qcheck[0]['configuration_value']){
					$providerSettings[$key] = $value;
				}
			}
		}
	}
	
	if (!empty($providerSettings)){
		$Provider->provider_module_settings = serialize($providerSettings);
	}
	
	$Provider->save();
	
	EventManager::attachActionResponse(array(
		'success' => true,
		'pID' => $Provider->provider_id
	), 'json');
?>