    <?php
function loadPostAttributes($productsId, $purchaseType, $CustomerWishlistAttributes){
	global $appExtension;
	$extAttributes = $appExtension->getExtension('attributes');
	if ($extAttributes && $extAttributes->isEnabled() === true) {
		if ((attributesUtil::productHasAttributes($productsId, $purchaseType))) {

			foreach ($CustomerWishlistAttributes as $iAttr) {
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


    if(isset($_POST['customerWishlistSelect'])){
		//foreach new, used, rental, download, stream type add to cart now... for reservation types returen a calendar and assign js actions to them
 	    $QcustomerWishlist = Doctrine_Query::create()
		->from('CustomerWishlist cf')
		->leftJoin('cf.CustomersWishlistProductAttributes cfpa')
		->whereIn('cf.customer_wishlist_id', $_POST['customerWishlistSelect'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($QcustomerWishlist as $iWishlist){
			$productsId = $iWishlist['products_id'];

			switch($iWishlist['purchase_type']){
				case 'reservation':
					loadPostAttributes($productsId, 'reservation', $iWishlist['CustomersWishlistProductAttributes']);
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