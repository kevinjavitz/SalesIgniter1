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
		$messageStack->addSession('pageStack', sysLanguage::get('EXTENSION_CUSTOMER_FAVORITES_USER_LOGGEDIN'), 'error');
		$redirectPage = itw_app_link(null, 'account', 'login');
		$success = true;
	}else{
		$QcustomerFavorites = Doctrine_Query::create()
		->from('CustomerFavorites cf')
		->leftJoin('cf.CustomersFavoritesProductAttributes cfpa')
		->where('cf.customers_id=?', $userAccount->getCustomerId())
		->andWhere('cf.products_id=?', $prodID)
		->andWhere('cf.purchase_type=?', $_POST['favPurchaseType'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$prodExists = false;
		if ((attributesUtil::productHasAttributes($prodID, $_POST['favPurchaseType']))){

			foreach($QcustomerFavorites as $iFavorites){

				foreach($iFavorites['CustomersFavoritesProductAttributes'] as $iAttribute){
					if(in_array($iAttribute['products_attributes_id'], $attributes)){
						$prodExists = true;
						break;
					}
				}
			}
		}elseif(count($QcustomerFavorites) > 0){
			$prodExists = true;
		}

		if($prodExists === false){
			$CustomerFavorites = new CustomerFavorites();
			$CustomerFavorites->customers_id = $userAccount->getCustomerId();
			$CustomerFavorites->purchase_type = $_POST['favPurchaseType'];
			$CustomerFavorites->products_id = $prodID;
			$CustomerFavorites->save();
			if(count($attributes) > 0){
				foreach($attributes as $iAttribute){
					$CustomerFavoritesProductAttrributes = new CustomersFavoritesProductAttributes();
					$CustomerFavoritesProductAttrributes->customers_favorites_id = $CustomerFavorites->customer_favorites_id;
					$CustomerFavoritesProductAttrributes->products_attributes_id = $iAttribute;
					$CustomerFavoritesProductAttrributes->save();
				}
			}

			$success = true;
			$redirectPage = itw_app_link('appExt=customerFavorites', 'account_addon', 'manage_favorites');
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