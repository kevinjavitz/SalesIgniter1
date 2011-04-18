<?php
	$Option = Doctrine_Core::getTable('ProductsOptions')->find($_GET['option_id']);
	if ($Option){
		$OptionsValues =& $Option->ProductsOptionsValuesToProductsOptions;
		$error = false;
		foreach($OptionsValues as $ovInfo){
			if ($ovInfo['products_options_values_id'] == $_GET['value_id']){
				$error = true;
			}
		}
		if ($error === false){
			$OptionsValues[]->products_options_values_id = $_GET['value_id'];
			
			$Option->save();
			
			$success = true;
		}else{
			$success = false;
		}
	}else{
		$success = false;
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>