<?php
	$Group = Doctrine_Core::getTable('ProductsOptionsGroups')->find($_GET['group_id']);
	if ($Group){
		$GroupOptions =& $Group->ProductsOptionsToProductsOptionsGroups;
		$error = false;
		foreach($GroupOptions as $gInfo){
			if ($gInfo['products_options_id'] == $_GET['option_id']){
				$error = true;
			}
		}
		if ($error === false){
			$GroupOptions[]->products_options_id = $_GET['option_id'];
			
			$GroupOptions->save();
			
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