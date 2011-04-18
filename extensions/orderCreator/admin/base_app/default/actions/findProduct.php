<?php
	$jsonData = array();

	$QproductName = Doctrine_Query::create()
	->from('ProductsDescription')
	->where('products_name LIKE ?', $_GET['term'] . '%')
	->andWhere('language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($QproductName){
		foreach($QproductName as $pInfo){
			$jsonData[] = array(
				'value' => $pInfo['products_id'],
				'label' => $pInfo['products_name']
			);
		}
	}
	
	EventManager::attachActionResponse($jsonData, 'json');
?>