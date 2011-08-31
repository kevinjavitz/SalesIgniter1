<?php
    $term = $_POST['term'];

	$QModels = Doctrine_Query::create()
	->from('Products')
	->andWhere('products_model LIKE ?', $term.'%')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$jsonData = array();
foreach($QModels as $model){

	$jsonData[] = array(
			'value' => $model['products_model'],
			'label' => $model['products_model']
	);

}
EventManager::attachActionResponse($jsonData, 'json');
?>