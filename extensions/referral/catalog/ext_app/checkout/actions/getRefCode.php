<?php
	$error = true;
	$errMsg = sysLanguage::get('ERROR_WRONG_REFERRAL_CODE');
	$userAccount = $userAccount = &Session::getReference('userAccount');
	$chkrefCode = Doctrine_Core::getTable('customersReferrer')
				->findOneByReferrerCode($_POST['refcode']);

	if(isset($_POST['refcode']) && strstr($_POST['refcode'],'!') && $chkrefCode->customers_id != $userAccount->getCustomerId()) {
		$refCode = explode('!',$_POST['refcode']);
		$customersFirstName = $refCode[0];
		$customersId = $refCode[1];

		if(is_array($refCode) &&  count($refCode) == 2){
			$cInfo = Doctrine_Core::getTable('customers')
							->findOneByCustomersId($customersId);
			if($customersFirstName == $cInfo->customers_firstname && $customersId == $cInfo->customers_id){
				$couponCode = create_coupon_code($userAccount->getEmailAddress());
				$Coupon = new Coupons;
				$Coupon->coupon_type = 'S';
				$Coupon->coupon_amount = 0;
				$Coupon->coupon_active = 'Y';
				$Coupon->coupon_code = $couponCode;
				$Coupon->uses_per_coupon = 1;
				$Coupon->uses_per_user = 1;
				$Coupon->coupon_start_date = date('Y-m-d');
				$Coupon->coupon_expire_date = date('Y-m-d', strtotime('+1 Years'));
				$Coupon->save();

				$customersReferrer = new customersReferrer;
				$customersReferrer->customers_id = $userAccount->getCustomerId();
				$customersReferrer->referrer_code = $_POST['refcode'];
				$customersReferrer->save();

				$referralExtesnion = $appExtension->getExtension('referral');
				//$referralExtesnion->sendReferrerFreeShippingCouponEmail($userAccount->getCustomerId(),$couponCode,$_POST['refcode']);
				$referralExtesnion->sendReferralEarnedEmail($_POST['refcode'],&$Coupon);


				$error = false;
				$errMsg = '';
			}
		}
	}
	EventManager::attachActionResponse(array(
											'success' => !$error,
											'couponcode' => $couponCode,
											'errMsg'	=> $errMsg
									   ), 'json');
?>