<?php
	$error = false;
	$success = false;
	$code = (isset($_POST['code']) ? $_POST['code'] : false);
 	$errMsg = '';
	if ($code) {
		$Qcoupon = dataAccess::setQuery('select coupon_id, coupon_amount, coupon_type, coupon_minimum_order, uses_per_coupon, uses_per_user, restrict_to_products, restrict_to_categories from {coupons} where coupon_code = {code} and coupon_active = "Y"');
		$Qcoupon->setTable('{coupons}', TABLE_COUPONS);
		$Qcoupon->setValue('{code}', $code);
		$Qcoupon->runQuery();
		if ($Qcoupon->numberOfRows() == 0){
			$error = true;
			$errMsg = sysLanguage::get('ERROR_NO_INVALID_REDEEM_COUPON');
		}elseif ($Qcoupon->getVal('coupon_type') != 'G'){
			$QstartDate = dataAccess::setQuery('select coupon_start_date from {coupons} where coupon_start_date <= now() and coupon_code = {code}');
			$QstartDate->setTable('{coupons}', TABLE_COUPONS);
			$QstartDate->setValue('{code}', $code);
			$QstartDate->runQuery();
			if ($QstartDate->numberOfRows() == 0){
				$error = true;
				$errMsg = sysLanguage::get('ERROR_INVALID_STARTDATE_COUPON');
			}

			$QexpireDate = dataAccess::setQuery('select coupon_expire_date from {coupons} where coupon_expire_date >= now() and coupon_code = {code}');
			$QexpireDate->setTable('{coupons}', TABLE_COUPONS);
			$QexpireDate->setValue('{code}', $code);
			$QexpireDate->runQuery();
			if ($QexpireDate->numberOfRows() == 0){
				$error = true;
				$errMsg = sysLanguage::get('ERROR_INVALID_FINISDATE_COUPON');
			}

			$Qtrack = dataAccess::setQuery('select coupon_id from {table} where coupon_id = {coupon}');
			$Qtrack->setTable('{table}', TABLE_COUPON_REDEEM_TRACK);
			$Qtrack->setValue('{coupon}', $Qcoupon->getVal('coupon_id'));
			$Qtrack->runQuery();

			$QcustomerTrack = dataAccess::setQuery('select coupon_id from {table} where coupon_id = {coupon} and customer_id = {customer}');
			$QcustomerTrack->setTable('{table}', TABLE_COUPON_REDEEM_TRACK);
			$QcustomerTrack->setValue('{coupon}', $Qcoupon->getVal('coupon_id'));
			$QcustomerTrack->setValue('{customer}', $userAccount->getCustomerId());
			$QcustomerTrack->runQuery();

			if ($Qtrack->numberOfRows() >= $Qcoupon->getVal('uses_per_coupon') && $Qcoupon->getVal('uses_per_coupon') > 0){
				$error = true;
				$errMsg = sysLanguage::get('ERROR_INVALID_USES_COUPON') . $Qcoupon->getVal('uses_per_coupon') . sysLanguage::get('TIMES');
			}

			if ($QcustomerTrack->numberOfRows() >= $Qcoupon->getVal('uses_per_user') && $Qcoupon->getVal('uses_per_user') > 0) {
				$error = true;
				$errMsg = sysLanguage::get('ERROR_INVALID_USES_USER_COUPON') . $Qcoupon->getVal('uses_per_user') . sysLanguage::get('TIMES');
			}

			if ($error === false){
				Session::set('cc_id', $Qcoupon->getVal('coupon_id'));				
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