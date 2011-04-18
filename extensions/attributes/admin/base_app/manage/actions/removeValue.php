<?php
	$ProductsOptionsValues = Doctrine_Core::getTable('ProductsOptionsValues')
	->findOneByProductsOptionsValuesId((int)$_GET['value_id']);
	if ($ProductsOptionsValues){
		$ProductsOptionsValues->delete();
	
		$response = array(
			'success'  => true,
			'value_id' => (int)$_GET['value_id']
		);
	}else{
		$response = array(
			'success' => false
		);
	}
	EventManager::attachActionResponse($response, 'json');
?>