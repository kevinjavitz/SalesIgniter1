<?php
	$OrderProduct = new OrderCreatorProduct();
	$OrderProduct->setProductsId($_GET['pID']);
	if($_GET['purchaseType'] != 'none'){
	    $OrderProduct->setPurchaseType($_GET['purchaseType']);
	}else{
		$OrderProduct->setPurchaseType('new');
	}
	$OrderProduct->setQuantity($_POST['qty']);

	$Editor->ProductManager->add($OrderProduct);
	$Product = $OrderProduct->productClass;
	$PurchaseType = $OrderProduct->purchaseTypeClass;

    if($_GET['purchaseType'] == 'reservation'){

		$reservationInfo = $OrderProduct->getPInfo();

		$start = date('m/d/Y H:i:s');

        if (!((isset($_POST['start_date']) && $_POST['start_date'] != 'undefined')&&(isset($_POST['end_date']) && $_POST['end_date'] != 'undefined'))){
			$_POST['start_date'] = date('m/d/Y H:i:s');
			$_POST['end_date'] = date('m/d/Y H:i:s');
		}
		$hasInv = $PurchaseType->hasInventoryForDates($_POST['qty'], $_POST['start_date'],$_POST['end_date']);
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'False'){
			if($PurchaseType->consumptionAllowed() === '1' && !isset($_POST['has_info'])){
				$starting_date = date('Y-m-d H:i:s');
				$ending_date = date('Y-m-d H:i:s');
				$resInfo['start_date'] = $starting_date;
				$resInfo['end_date'] = $ending_date;
			}
			if ((isset($_POST['start_date']) && $_POST['start_date'] != 'undefined')&&(isset($_POST['end_date']) && $_POST['end_date'] != 'undefined')){
				$resInfo['start_date'] = $_POST['start_date']; //. $myStartTime;
				$resInfo['end_date'] = $_POST['end_date'];//.$myEndTime;
				$starting_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
				$ending_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));
			}

		}

	if (isset($resInfo['start_date']) && isset($resInfo['end_date'])){

		if(isset($_POST['shipping']) && $_POST['shipping'] != 'undefined'){
			$shippingInfo = explode('_', $_POST['shipping']);
			$resInfo['shipping_module'] = $shippingInfo[0];
			$resInfo['shipping_method'] = $shippingInfo[1];
			$resInfo['days_before'] = (isset($_POST['days_before'])?$_POST['days_before']:0);
			$resInfo['days_after'] = (isset($_POST['days_after'])?$_POST['days_after']:0);
		}else{
			$resInfo['rental_shipping'] = false;
		}

		if (isset($_POST['qty']) && $_POST['qty'] != 'undefined'){
			$resInfo['quantity'] = $_POST['qty'];
		}

		if (isset($_POST['bar_id']) && $_POST['bar_id'] != 'undefined'){
			$resInfo['bar_id'] = $_POST['bar_id'];
		}

		$PurchaseType->processAddToCartNew($reservationInfo, $resInfo);

		if (isset($_POST['id']['reservation']) && !empty($_POST['id']['reservation'])){
			$attrValue = attributesUtil::getAttributeString($_POST['id']['reservation']);
			if(!empty($attrValue)){
				$reservationInfo['aID_string'] = $attrValue;
			}
		}

		$OrderProduct->setPInfo($reservationInfo);
	}
		}else{
			$currentStock = $PurchaseType->getCurrentStock();
			if($currentStock - $_POST['qty'] >= 0){
				$hasInv = true;
			}else{
				$hasInv = false;
			}
		}

if ($Editor->hasErrors() === false){
	$purchaseTypeName = (isset($_GET['purchaseType'])?$_GET['purchaseType']:'new');
	$html = '<tr data-product_id="' . (int)$_GET['pID'] . '" data-id="' . $OrderProduct->getId() . '" data-product_type="'.$purchaseTypeName.'">' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;">' . $OrderProduct->getQuantityEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;" >' .'<div class="pName">'. $OrderProduct->getNameEdit($Editor->ProductManager->getExcludedPurchaseTypes($OrderProduct)) .'</div>'. '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getBarcodeEdit() . '</td>';
	if($purchaseTypeName == 'reservation'){

		$html .= '<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getStartDateEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getStartTimeEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getEndDateEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getEndTimeEdit() . '</td>';
	}else{
		$html .= '<td colspan="4" class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . 'Not available' . '</td>';
	}
		//'<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none;">' . $OrderProduct->getModel() . '</td>' .
	$html .= '<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getTaxRateEdit() . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit() . '</td>' .
		//'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(false, true) . '</td>' .
		//'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(true, false) . '</td>' .
		//'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;">' . $OrderProduct->getPriceEdit(true, true) . '</td>' .
		'<td class="ui-widget-content" valign="top" align="right" style="border-top:none;border-left:none;"><span class="ui-icon ui-icon-closethick deleteProductIcon"></span></td>' .
	'</tr>';

	$response = array(
		'success' => true,
		'price' => $reservationInfo['price'],
		'hasInventory' => $hasInv,
		'hasError' => false,
		'html' => $html
	);
}
else {
	$response = array(
		'success' => true,
		'hasError' => true,
		'errorMessage' => $Editor->getErrors()
	);
}
EventManager::attachActionResponse($response, 'json');
?>