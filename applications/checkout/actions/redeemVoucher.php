<?php
	$error = false;
	$success = false;
	$code = (isset($_POST['code']) ? $_POST['code'] : false);
 	$errMsg = '';
	if ($onePageCheckout->isMembershipCheckout()){
		$onePageCheckout->loadMembershipPlan();
	}
	if ($code) {
		$Qcoupon = Doctrine_Query::create()
			->from('Coupons')
			->where('coupon_code = ?', $code)
			->andWhere('coupon_active = ?', 'Y')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if (!$Qcoupon){
			$error = true;
			$errMsg = sysLanguage::get('ERROR_NO_INVALID_REDEEM_COUPON');
		}elseif ($Qcoupon[0]['coupon_type'] != 'G'){
			$startDateCheck = strtotime($Qcoupon[0]['coupon_start_date']);
			if ($startDateCheck > time()){
				$error = true;
				$errMsg = sysLanguage::get('ERROR_INVALID_STARTDATE_COUPON');
			}

			$endDateCheck = strtotime($Qcoupon[0]['coupon_expire_date']);
			if ($endDateCheck < time()){
				$error = true;
				$errMsg = sysLanguage::get('ERROR_INVALID_FINISDATE_COUPON');
			}

			$Qtrack = Doctrine_Query::create()
				->select('count(coupon_id) as total')
				->from('CouponRedeemTrack')
				->where('coupon_id = ?', $Qcoupon[0]['coupon_id'])
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$QcustomerTrack = Doctrine_Query::create()
				->select('count(coupon_id) as total')
				->from('CouponRedeemTrack')
				->where('coupon_id = ?', $Qcoupon[0]['coupon_id'])
				->andWhere('customer_id = ?', $userAccount->getCustomerId())
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if ($Qtrack[0]['total'] >= $Qcoupon[0]['uses_per_coupon'] && $Qcoupon[0]['uses_per_coupon'] > 0){
				$error = true;
				$errMsg = sysLanguage::get('ERROR_INVALID_USES_COUPON') . $Qcoupon[0]['uses_per_coupon'] . sysLanguage::get('TIMES');
			}

			if ($QcustomerTrack[0]['total'] >= $Qcoupon[0]['uses_per_user'] && $Qcoupon[0]['uses_per_user'] > 0) {
				$error = true;
				$errMsg = sysLanguage::get('ERROR_INVALID_USES_USER_COUPON') . $Qcoupon[0]['uses_per_user'] . sysLanguage::get('TIMES');
			}

			if($order->info['total'] < $Qcoupon[0]['coupon_minimum_order']){
				$error = true;
				$errMsg = sysLanguage::get('ERROR_MIN_ORDER_AMOUNT') . $Qcoupon[0]['coupon_minimum_order'];
			}
			if($order->info['total'] > $Qcoupon[0]['coupon_maximum_order'] && $Qcoupon[0]['coupon_maximum_order'] > 0){
				$error = true;
				$errMsg = sysLanguage::get('ERROR_MAX_ORDER_AMOUNT') . $Qcoupon[0]['coupon_maximum_order'];
			}
			if($Qcoupon[0]['restrict_to_purchase_type'] != '') {
				$foundPurchaseType = false;
				if(strstr($Qcoupon[0]['restrict_to_purchase_type'],',')){
					$allowedPurchaseTypes = explode(',',$Qcoupon[0]['restrict_to_purchase_type']);
				} else {
					$allowedPurchaseTypes = array($Qcoupon[0]['restrict_to_purchase_type']);
				}
				$purchaseTypeTotal = 0;
				if($onePageCheckout->isMembershipCheckout()) {
					if ((is_array($allowedPurchaseTypes) && in_array('membership', $allowedPurchaseTypes))){
						$foundPurchaseType = true;
					}
				} else {
					$ShoppingCart = &Session::getReference('ShoppingCart');
					foreach ($ShoppingCart->getProducts() as $cartProduct) {
						$purchaseType = $cartProduct->getPurchaseType();

						if ((is_array($allowedPurchaseTypes) && in_array($purchaseType, $allowedPurchaseTypes))){
							$foundPurchaseType = true;
						}

					}
				}
				if(!$foundPurchaseType){
					$error = true;
					$errMsg = sprintf(sysLanguage::get('ERROR_PURCHASE_TYPE_NOT_ALLOWED'), implode(',',$allowedPurchaseTypes));
				}
			}

			if ($error === false){
				Session::set('cc_id', $Qcoupon[0]['coupon_id']);
				$success = true;
			}
		}
	}

	OrderTotalModules::process();

	EventManager::attachActionResponse(array(
		'success' => true,
		'errorMsg' => $errMsg,
		'orderTotalRows' => OrderTotalModules::output()
	), 'json');
?>