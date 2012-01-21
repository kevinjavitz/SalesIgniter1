<?php
/*
	Multi Stores Extension Version 1.1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$Stores = Doctrine::getTable('Stores');
	if (isset($_GET['sID'])){
		$Store = $Stores->findOneByStoresId((int)$_GET['sID']);
	}else{
		$Store = $Stores->create();
	}

	$Store->stores_name = $_POST['stores_name'];
	$Store->stores_domain = $_POST['stores_domain'];
	$Store->stores_ssl_domain = $_POST['stores_ssl_domain'];
	$Store->stores_email = $_POST['stores_email'];
	$Store->stores_template = $_POST['stores_template'];
	$Store->stores_zip = $_POST['stores_zip'];
	$Store->stores_location = $_POST['stores_location'];

/* Auto Upgrade ( Version 1.0 to 1.1 ) --BEGIN-- */
				$Store->stores_owner = $_POST['stores_owner'];
/* Auto Upgrade ( Version 1.0 to 1.1 ) --END-- */

	$CategoriesToStores = $Store->CategoriesToStores;
	//$ProductsToStores = $Store->ProductsToStores;
	
	if (isset($_GET['sID'])){
		$CategoriesToStores->delete();
		//$ProductsToStores->delete();
	}
	
	if (isset($_POST['categories'])){
		$addedProducts = array();
		$addedCategories = array();
		foreach($_POST['categories'] as $categoryId){
			$CategoriesToStores[]->categories_id = $categoryId;
			
			/*$ProductsToCategories = Doctrine_Query::create()
			->select('products_id')
			->from('ProductsToCategories')
			->where('categories_id = ?', $categoryId)
			->execute();
			if ($ProductsToCategories){
				foreach($ProductsToCategories->toArray() as $product){
					$productId = $product['products_id'];
					if (in_array($productId, $addedProducts) === false){
						$ProductsToStores[]->products_id = $productId;
					}
					$addedProducts[] = $productId;
				}
			}*/
		}
	}
	//print_r($Store->toArray());
	$Store->save();
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'sID=' . $Store->stores_id, null, 'default'), 'redirect');
?>