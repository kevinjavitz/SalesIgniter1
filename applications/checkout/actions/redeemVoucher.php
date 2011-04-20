<?php
	$error = false;
	$success = false;
	$code = (isset($_POST['code']) ? $_POST['code'] : false);
 	$errMsg = '';
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

			if ($error === false){
				Session::set('cc_id', $Qcoupon[0]['coupon_id']);
				$success = true;
			}
		}
	}
	if ($onePageCheckout->isMembershipCheckout()){
		$onePageCheckout->loadMembershipPlan();
	}
	OrderTotalModules::process();

	EventManager::attachActionResponse(array(
		'success' => true,
		'errorMsg' => $errMsg,
		'orderTotalRows' => OrderTotalModules::output()
	), 'json');
?>