    <?php
function loadPostAttributes($productsId, $purchaseType, $CustomerFavoritesAttributes){
	global $appExtension;
	$extAttributes = $appExtension->getExtension('attributes');
	if ($extAttributes && $extAttributes->isEnabled() === true) {
		if ((attributesUtil::productHasAttributes($productsId, $purchaseType))) {

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

				if (isset($_POST['id'][$purchaseType])) {
					unset($_POST['id'][$purchaseType]);
				}

				$_POST['id'][$purchaseType][$Query[0]['ProductsOptions']['products_options_id']] = $Query[0]['ProductsOptionsValues']['products_options_values_id'];
			}
		}
	}
}


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
					loadPostAttributes($productsId, 'reservation', $iFavorites['CustomersFavoritesProductAttributes']);
					ReservationUtilities::addReservationProductToCart($productsId, 1);
					break;
			}
		}
		$success = true;
	}else{
		$success = false;
	}


	EventManager::attachActionResponse(array(
		'success' => $success,
	), 'json');

    ?>