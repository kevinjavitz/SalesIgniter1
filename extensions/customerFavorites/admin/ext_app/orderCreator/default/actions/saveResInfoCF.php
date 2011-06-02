<?php
function loadPostAttributes($ocProductsId, $productsId, $purchaseTypeSelect, $CustomerFavoritesAttributes){
	global $appExtension, $Editor;


	$extAttributes = $appExtension->getExtension('attributes');
	if ($extAttributes && $extAttributes->isEnabled() === true) {
		if ((attributesUtil::productHasAttributes($productsId, $purchaseTypeSelect))) {

			foreach ($CustomerFavoritesAttributes as $iAttr) {
				$Query = Doctrine_Query::create()
				->from('ProductsAttributes a')
				->leftJoin('a.ProductsOptions o')
				->leftJoin('o.ProductsOptionsDescription od')
				->leftJoin('a.ProductsOptionsValues ov')
				->leftJoin('ov.ProductsOptionsValuesDescription ovd')
				->leftJoin('ov.ProductsOptionsValuesToProductsOptions v2o')
				->where('a.products_attributes_id=?', $iAttr['products_attributes_id'])
				->andWhere('od.language_id=?', Session::get('languages_id'))
				->andWhere('ovd.language_id=?', Session::get('languages_id'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				if (isset($_POST['id'][$purchaseTypeSelect])) {
					unset($_POST['id'][$purchaseTypeSelect]);
				}

				$_POST['id'][$purchaseTypeSelect][$Query[0]['ProductsOptions']['products_options_id']] = $Query[0]['ProductsOptionsValues']['products_options_values_id'];

			}
		}
	}
	$OrderProduct = $Editor->ProductManager->get($ocProductsId);
	$Product = $OrderProduct->productClass;
	$PurchaseType = $OrderProduct->purchaseTypeClass;
 	$reservationInfo = $OrderProduct->getPInfo();
    if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
		if ((isset($_POST['start_date']) && $_POST['start_date'] != 'undefined')&&(isset($_POST['end_date']) && $_POST['end_date'] != 'undefined')){
			$resInfo['start_date'] = $_POST['start_date'];
			$resInfo['end_date'] = $_POST['end_date'];
			$starting_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
			$ending_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));
		}

	}
	if (isset($resInfo['start_date']) && isset($resInfo['end_date'])){

		if(isset($_POST['shipping']) && $_POST['shipping'] != 'undefined'){
			$resInfo['rental_shipping'] = 'zonereservation_'.$_POST['shipping'];
		}else{
			$resInfo['rental_shipping'] = false;
		}
		if (isset($_POST['qty']) && $_POST['qty'] != 'undefined'){
			$resInfo['rental_qty'] = $_POST['qty'];
		}

		$PurchaseType->processAddToCartNew($reservationInfo, $resInfo);

		if (isset($_POST['id']['reservation']) && !empty($_POST['id']['reservation'])){
			unset($reservationInfo['aID_string']);
			$reservationInfo['aID_string'] = attributesUtil::getAttributeString($_POST['id']['reservation']);//'{1}2';
		}
		$OrderProduct->setPInfo($reservationInfo);
		return (isset($reservationInfo['price'])?$reservationInfo['price']:0);
	}
	return 0;
}

    $priceArr = array();
    if(isset($_POST['customerFavoritesSelect'])){
		//foreach new, used, rental, download, stream type add to cart now... for reservation types returen a calendar and assign js actions to them
 	    $QcustomerFavorites = Doctrine_Query::create()
		->from('CustomerFavorites cf')
		->leftJoin('cf.CustomersFavoritesProductAttributes cfpa')
		->whereIn('cf.customer_favorites_id', $_POST['customerFavoritesSelect'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($QcustomerFavorites as $iFavorites){
			$productsId = $iFavorites['products_id'];
			switch($iFavorites['purchase_type']){
				case 'reservation':
					$priceArr[$_POST['ocFavoritesSelect'][$productsId]] = loadPostAttributes($_POST['ocFavoritesSelect'][$productsId], $productsId,  'reservation', $iFavorites['CustomersFavoritesProductAttributes']);
					break;
			}
		}
		$success = true;
	}else{
		$success = false;
	}

    EventManager::attachActionResponse(array(
		'success' => true,
		'price'	=> $priceArr
	), 'json');

?>