<?php
	$ProductsOptions = Doctrine_Core::getTable('ProductsOptions')->findOneByProductsOptionsId((int)$_GET['option_id']);
	if ($ProductsOptions){
		$ProductsOptions->delete();
	
		$response = array(
			'success'   => true,
			'option_id' => (int)$_GET['option_id']
		);
	}else{
		$response = array(
			'success' => false
		);
	}
	
	EventManager::attachActionResponse($response, 'json');
?>