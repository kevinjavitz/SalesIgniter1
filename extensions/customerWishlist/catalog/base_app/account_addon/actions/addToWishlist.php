<?php
	$prodID = $_POST['products_id'];
	$extAttributes = $appExtension->getExtension('attributes');
	$hasAttributes = true;
	$attributes = array();
    if ($extAttributes && $extAttributes->isEnabled() === true) {
		if ((attributesUtil::productHasAttributes($prodID, $_POST['favPurchaseType']))){
			$hasAttributes = false;
			if(isset($_POST['id'][$_POST['favPurchaseType']])){
				foreach($_POST['id'][$_POST['favPurchaseType']] as $valueId => $optionId){
					if($optionId != ''){
						$QAttributes = attributesUtil::getAttributes($prodID, $valueId, $optionId, $_POST['favPurchaseType']);
						$attributes[] = $QAttributes[0]['products_attributes_id'];
						$hasAttributes = true;
					}
				}
			}
		}
    }

	$success = $hasAttributes;
    $redirectPage = 'no_attributes';

	if(!$userAccount->isLoggedIn()){
		$messageStack->addSession('pageStack', sysLanguage::get('EXTENSION_CUSTOMER_WISHLIST_USER_LOGGEDIN'), 'error');
		$redirectPage = itw_app_link(null, 'account', 'login');
		$success = true;
	}else{
		$QcustomerWishlist = Doctrine_Query::create()
		->from('CustomerWishlist cf')
		->leftJoin('cf.CustomersWishlistProductAttributes cfpa')
		->where('cf.customers_id=?', $userAccount->getCustomerId())
		->andWhere('cf.products_id=?', $prodID)
		->andWhere('cf.purchase_type=?', $_POST['favPurchaseType'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$prodExists = false;
		if ((attributesUtil::productHasAttributes($prodID, $_POST['favPurchaseType']))){

			foreach($QcustomerWishlist as $iWishlist){

				foreach($iWishlist['CustomersWishlistProductAttributes'] as $iAttribute){
					if(in_array($iAttribute['products_attributes_id'], $attributes)){
						$prodExists = true;
						break;
					}
				}
			}
		}elseif(count($QcustomerWishlist) > 0){
			$prodExists = true;
		}

		if($prodExists === false){
			$CustomerWishlist = new CustomerWishlist();
			$CustomerWishlist->customers_id = $userAccount->getCustomerId();
			$CustomerWishlist->purchase_type = $_POST['favPurchaseType'];
			$CustomerWishlist->products_id = $prodID;
			$CustomerWishlist->save();
			if(count($attributes) > 0){
				foreach($attributes as $iAttribute){
					$CustomerWishlistProductAttrributes = new CustomersWishlistProductAttributes();
					$CustomerWishlistProductAttrributes->customers_wishlist_id = $CustomerWishlist->customer_wishlist_id;
					$CustomerWishlistProductAttrributes->products_attributes_id = $iAttribute;
					$CustomerWishlistProductAttrributes->save();
				}
			}

			$success = true;
			$redirectPage = itw_app_link('appExt=customerWishlist', 'account_addon', 'manage_wishlist');
		}else{
			$success = false;
			$redirectPage = 'product_exists';
		}
	}

	EventManager::attachActionResponse(array(
		'success' => $success,
		'redirect' => $redirectPage
	), 'json');
?>