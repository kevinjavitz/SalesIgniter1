<?php
	$productsId = $_POST['products_id'];
	$productsPrice = $_POST['products_price'];
	$specialsPrice = $_POST['specials_price'];

	if (substr($specialsPrice, -1) == '%'){
		$QproductsPrice = Doctrine_Query::create()
		->select('p.products_price')
		->from('Products p')
		->where('products_id = ?', $productsId);
		
		$Result = $QproductsPrice->execute(array(), Doctrine::HYDRATE_ARRAY);
		
		$productsPrice = $Result[0]['products_price'];
		$specialsPrice = ($productsPrice - (($specialsPrice / 100) * $productsPrice));
	}
	
	$expiresDate = '';
	if (!empty($_POST['expires_date'])){
		$expiresDate = $_POST['expires_date'];
	}
	
	$dataArray = array(
		'products_id'                 => $productsId,
		'specials_new_products_price' => $specialsPrice,
		'expires_date'                => $expiresDate,
		'status'                      => '1'
	);
	
	
	if (isset($_POST['specials_id'])){
		$dataArray['specials_last_modified'] = date('Y-m-d');
		
		$newSpecial = Doctrine::getTable('Specials')->find((int)$_POST['specials_id']);
		$newSpecial->synchronizeWithArray($dataArray);
	}else{
		$dataArray['specials_date_added'] = date('Y-m-d');
		
		$newSpecial = new Specials();
		$newSpecial->fromArray($dataArray);
	}
	$newSpecial->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'), 'redirect');
?>