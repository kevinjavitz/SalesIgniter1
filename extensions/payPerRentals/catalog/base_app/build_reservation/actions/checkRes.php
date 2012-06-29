<?php
	$starting_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
	$ending_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));
 	//$isSemester = (isset($_POST['isSemester'])?true:false);
  	$semName = (isset($_POST['semester_name'])?$_POST['semester_name']:'');
	$success = false;
	$price = 0;
	$totalPrice = 0;
	$totalPrice1 = 0;
	$message = '';
	$htmlShipping = '';
    foreach($_POST['products_id'] as $pElem){
		$product = new product($pElem);
		$purchaseTypeClass = $product->getPurchaseType('reservation');
		global $total_weight;
		$total_weight = (int)$_POST['rental_qty'] * $product->getWeight();
		OrderShippingModules::calculateWeight();
	    $rInfo = '';

	    $onlyShow = true;
	    if(sysconfig::get('EXTENSION_PAY_PER_RENTALS_SHORT_PRICE') == 'True'){
		    $onlyShow = false;
	    }

        if(isset($_GET['freeTrialButton']) && $_GET['freeTrialButton'] == '1'){
            $freeOn = explode(',',$_GET['freeTrial']);
            $pricing = $purchaseTypeClass->getReservationPrice($starting_date, $ending_date, $rInfo, $semName, false, true ,true);
        }
        else{
            $pricing = $purchaseTypeClass->getReservationPrice($starting_date, $ending_date, $rInfo, $semName, isset($_POST['hasInsurance'])?true:false, $onlyShow);
        }

		$Module = OrderShippingModules::getModule($purchaseTypeClass->shipModuleCode);
		$selectedMethod = '';

		$weight = 0;
		if($Module->getType() == 'Order' && $App->getEnv() == 'catalog'){
			if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_SHIPPING_ON_CALENDAR_IF_ORDER') == 'False'){
				$dontShow = 'none';
			}
			foreach($ShoppingCart->getProducts() as $cartProduct) {
				if ($cartProduct->hasInfo('reservationInfo') === true){
					$reservationInfo1 = $cartProduct->getInfo('reservationInfo');
					if(isset($reservationInfo1['shipping']) && isset($reservationInfo1['shipping']['module']) && $reservationInfo1['shipping']['module'] == 'zonereservation'){
						$selectedMethod = $reservationInfo1['shipping']['id'];
						$cost = 0;
						if(isset($reservationInfo1['shipping']['cost'])){
							$cost = $reservationInfo1['shipping']['cost'];
						}
						$totalPrice1 += $cartProduct->getFinalPrice(true) * $cartProduct->getQuantity() - $cost * $cartProduct->getQuantity();
						$dontShow = '';
						break;
					}
					$weight += $cartProduct->getWeight();

				}
			}

		}

		if(isset($_POST['rental_qty'])){
			$prod_weight = (int)$_POST['rental_qty'] * $product->getWeight();
		}else{
			$prod_weight = $product->getWeight();
		}

		$weight += $prod_weight;
	    $totalPrice1 += $pricing['price'];

		$quotes = array($Module->quote($selectedMethod, $weight, $totalPrice1));

		if (sizeof($quotes[0]['methods']) > 0 && ($Module->getType() == 'Product' || sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_SHIPPING_ON_CALENDAR_IF_ORDER') == 'True')){
			$htmlShipping .=  $purchaseTypeClass->parseQuotes($quotes) ;
		}

	    if (is_array($pricing) && is_numeric($pricing['price'])){
		    $price += $pricing['price'];
		    $totalPrice += $pricing['totalPrice'];
		    $message .= strip_tags($pricing['message']);
		    $success = true;
	    }
	}


	EventManager::attachActionResponse(array(
		'success' => $success,
		'price'   => $currencies->format($price),
		'totalPrice'   => $currencies->format($_POST['rental_qty'] * $totalPrice),
		'htmlShipping' => $htmlShipping,
		'message' => $message
	), 'json');
?>