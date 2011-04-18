<?php
	$ProductsOptionsGroups = Doctrine_Core::getTable('ProductsOptionsGroups')
	->findOneByProductsOptionsGroupsId((int)$_GET['group_id']);
	if ($ProductsOptionsGroups){
		$ProductsOptionsGroups->delete();
	
		$response = array(
			'success'  => true,
			'group_id' => (int)$_GET['group_id']
		);
	}else{
		$response = array(
			'success' => false
		);
	}
	EventManager::attachActionResponse($response, 'json');
?>