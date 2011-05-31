<?php

function loadPostAttributes($productsId, $purchaseType, $CustomerFavoritesAttributes, &$custFavoritesReservation, &$purchaseTypeClasses){
	global $appExtension, $Editor;

	$OrderProduct = new OrderCreatorProduct();
	$OrderProduct->setProductsId($productsId);
	$OrderProduct->setPurchaseType($purchaseType);
	$OrderProduct->setQuantity(1);

	$Editor->ProductManager->add($OrderProduct);

	$extAttributes = $appExtension->getExtension('attributes');
	if ($extAttributes && $extAttributes->isEnabled() === true) {
		if ((attributesUtil::productHasAttributes($productsId, $purchaseType))) {
			$pInfo = $OrderProduct->getPInfo();
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

				//$OrderProduct['OrdersProductsAttributes'][] = $Query[0]['ProductsOptions']['products_options'];
				$arr = array();
				$arr['products_options'] = $Query[0]['ProductsOptions']['ProductsOptionsDescription'][0]['products_options_name'];
				$arr['options_id'] = $Query[0]['ProductsOptionsValues']['products_options_values_id'];
				$pInfo['OrdersProductsAttributes'][] = $arr;
				//here I have to init $OrderProduct->info[OrdersProductsAttributes] to have the attributes i need
			}
			$OrderProduct->setPInfo($pInfo);
		}
	}
	if($purchaseType == 'reservation'){
		$custFavoritesReservation .= '<input class="ocFavoritesSelect" name="ocFavoritesSelect['.$productsId.']" type="hidden" value="'.$OrderProduct->getId().'">';
		$OrderProduct->setPurchaseType($purchaseType);
		$purchaseTypeClasses[] =  $OrderProduct->purchaseTypeClass;
	}
	$html = '<tr data-id="' . $OrderProduct->getId() . '">' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;">' . $OrderProduct->getQuantityEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getNameEdit($Editor->ProductManager->getExcludedPurchaseTypes($OrderProduct)) . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . ($OrderProduct->hasBarcode() ? $OrderProduct->getBarcode() : '') . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getModel() . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getTaxRateEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(false, true) . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(true, false) . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(true, true) . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;"><span class="ui-icon ui-icon-closethick deleteProductIcon"></span></td>' .
	'</tr>';
	return $html;
}

	$calendar = '';
	$message = '';
	$resArr = array();
	$purchaseTypeClasses = array();
	$custFavoritesReservation = '';
	$html = '';
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

				case 'new' :

					$html .= loadPostAttributes($productsId, 'new', $iFavorites['CustomersFavoritesProductAttributes'], &$custFavoritesReservation, &$purchaseTypeClasses);

					break;
				case 'used':
					$html .=loadPostAttributes($productsId, 'used', $iFavorites['CustomersFavoritesProductAttributes'], &$custFavoritesReservation, &$purchaseTypeClasses);

					break;
				case 'rental':
					//loadPostAttributes($productsId, 'rental', $iFavorites['CustomersFavoritesProductAttributes']);
					break;
				case 'download':
					$html .=loadPostAttributes($productsId, 'download', $iFavorites['CustomersFavoritesProductAttributes'], &$custFavoritesReservation, &$purchaseTypeClasses);
					break;
				case 'stream':
					$html .= loadPostAttributes($productsId, 'stream', $iFavorites['CustomersFavoritesProductAttributes'],&$custFavoritesReservation, &$purchaseTypeClasses);
					break;
				case 'reservation':
					$html .= loadPostAttributes($productsId, 'reservation', $iFavorites['CustomersFavoritesProductAttributes'], &$custFavoritesReservation, &$purchaseTypeClasses);
					$resArr[] = $productsId;
					$custFavoritesReservation .= '<input class="ocProductReservation" name="customerFavoritesSelect[]" type="hidden" value="'.$iFavorites['customer_favorites_id'].'">';
					break;
			}
		}

		$success = true;
	}else{
		$success = false;
	}

	if(count($resArr) > 0){
		$calendar = ReservationUtilities::getCalendar($resArr, $purchaseTypeClasses, 1, true);
	}

	EventManager::attachActionResponse(array(
		'success' => true,
		'html'  => $html,
		'calendar'  => $calendar,
		'custFavoritesReservation' => $custFavoritesReservation
	), 'json');

?>