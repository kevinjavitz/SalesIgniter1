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

	$calendar = '';
	$message = '';
	$resArr = array();
	$purchaseTypeClasses = array();
	$custWishlistReservation = '';
	if(isset($_POST['customerWishlistSelect'])){
		//add this wishlist item to list of items to remove from wishlist if successful checkout
		$_items = Session::get('wishlistAddedToCart') ? explode(',', Session::get('wishlistAddedToCart')) : Array();
		
		//foreach new, used, rental, download, stream type add to cart now... for reservation types returen a calendar and assign js actions to them
 	    $QcustomerWishlist = Doctrine_Query::create()
		->from('CustomerWishlist cf')
		->leftJoin('cf.CustomersWishlistProductAttributes cfpa')
		->whereIn('cf.customer_wishlist_id', $_POST['customerWishlistSelect'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach($QcustomerWishlist as $iWishlist){
			$_items[] = $iWishlist['products_id'].'|'.$iWishlist['customer_wishlist_id'];
			$productsId = $iWishlist['products_id'];

			switch($iWishlist['purchase_type']){

				case 'new' :

					loadPostAttributes($productsId, 'new', $iWishlist['CustomersWishlistProductAttributes']);
					$ShoppingCart->addProduct($productsId, 'new', 1);
					break;
				case 'used':
					loadPostAttributes($productsId, 'used', $iWishlist['CustomersWishlistProductAttributes']);
					$ShoppingCart->addProduct($productsId, 'used', 1);
					break;
				case 'rental':
					loadPostAttributes($productsId, 'rental', $iWishlist['CustomersWishlistProductAttributes']);
					$pID = $productsId;
					$attribs = array();

					if (isset($_GET['id']) && isset($_GET['id']['rental'])){
						$attribs = $_GET['id']['rental'];
					}elseif (isset($_POST['id']) && isset($_POST['id']['rental'])){
						$attribs = $_POST['id']['rental'];
					}


					$customerCanRent = $rentalQueue->rentalAllowed($userAccount->getCustomerId());
					$errorMsg = '';
					if ($customerCanRent !== true){
							switch($customerCanRent){
								case 'membership':
									if (Session::exists('account_action') === true){
										Session::remove('account_action');
									}

									$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default', 'SSL'), itw_app_link(null,'account','login'));
									break;
								case 'inactive':
									$errorMsg = sprintf(sysLanguage::get('TEXT_NOT_ACTIVE_CUSTOMER'), itw_app_link('checkoutType=rental','checkout','default','SSL'));
									break;
							}
						$message .= $errorMsg;
					}

					$rentalQueue->addToQueue($pID, $attribs);
					break;
				case 'download':
					loadPostAttributes($productsId, 'download', $iWishlist['CustomersWishlistProductAttributes']);
					$ShoppingCart->addProduct($productsId, 'download', 1);
					break;
				case 'stream':
					loadPostAttributes($productsId, 'stream', $iWishlist['CustomersWishlistProductAttributes']);
					$ShoppingCart->addProduct($productsId, 'stream', 1);
					break;
				case 'reservation':
					loadPostAttributes($productsId, 'reservation', $iWishlist['CustomersWishlistProductAttributes']);
					$product = new product($productsId);
					$purchaseTypeClasses[] = $product->getPurchaseType('reservation');
					$resArr[] = $productsId;
					$custWishlistReservation .= '<input class="custReservation" name="customerWishlistSelect[]" type="hidden" value="'.$iWishlist['customer_wishlist_id'].'">';
					break;
			}
		}
		
		Session::set('wishlistAddedToCart', implode(',', $_items));
		$success = true;
	}else{
		$success = false;
	}

	if(count($resArr) > 0){
		$calendar = ReservationUtilities::getCalendar($resArr, $purchaseTypeClasses, 1, true);
	}

	EventManager::attachActionResponse(array(
		'success' => $success,
		'hasReservation' => (count($resArr) > 0),
		'calendar' => $calendar,
		'customerWishlistReservation' => $custWishlistReservation
	), 'json');

?>