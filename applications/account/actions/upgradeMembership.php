<?php
	if (!isset($_POST['plan_id'])){
		$messageStack->addSession('pageStack', 'You must select a membership to change to.', 'error');
		EventManager::attachActionResponse(itw_app_link(null, 'account', 'membership_upgrade', 'SSL'), 'redirect');
	}else{
		$membership =& $userAccount->plugins['membership'];
		$planInfo = $membership->getPlanInfo((int)$_POST['plan_id']);

		if (RENTAL_UPGRADE_CYCLE == 'true'){
			Doctrine_Query::create()
			->delete('MembershipUpdate')
			->where('customers_id = ?', $userAccount->getCustomerId())
			->execute();

			$Upgrade = new MembershipUpdate();
			$Upgrade->customers_id = $userAccount->getCustomerId();
			$Upgrade->plan_id = (int)$_POST['plan_id'];
			$Upgrade->upgrade_date = date('Y-m-d', $membership->membershipInfo['next_bill_date']);
			$Upgrade->save();

			$messageStack->addSession('pageStack', 'Your new membership will take effect on "' . tep_date_short(date('Y-m-d', $membership->membershipInfo['next_bill_date'])) . '"', 'success');
		}else{
			$row_cust = '';
			if ($membership->planInfo['membership_months'] > 0){
				$row_cust .= $membership->planInfo['membership_months'];
			}
			if ($membership->planInfo['membership_days'] > 0){
				$row_cust .= $membership->planInfo['membership_days'];
			}

			$row_day='';
			if ($planInfo['membership_months'] > 0){
				$row_day .= $planInfo['membership_months'];
			}
			if ($planInfo['membership_days'] > 0){
				$row_day .= $planInfo['membership_days'];
			}

			if ($membership->membershipInfo['payment_method'] == 'usaepay'){
				$paymentMethod = 'USAePay';
			}elseif ($membership->membershipInfo['payment_method'] == 'authorizenet'){
				$paymentMethod = 'Authorize.Net';
			}elseif($membership->membershipInfo['payment_method'] == 'cc'){
				$paymentMethod = 'Credit Card';
			}

			// send a mail request to admin
			$subject="New request for subscription upgradation";
			$body = "Hello Admin,\nThe following customer has submited the request for upgrading his/her subscription.\nCustomer ID: ".$userAccount->getCustomerId().
			"\nCustomer Name: ".$userAccount->getFullName().
			"\nCustomer Email: ".$userAccount->getEmailAddress().
			"\n-------------------------------------------------".
			"\n\nCurrent Plan".
			"\n\nPlan ID: ".$membership->planInfo['plan_id'].
			"\nPackage Name: ".$membership->planInfo['package_name'].
			$row_cust.
			"\nNumber of Titles That Can Be Issued: ".$membership->planInfo['no_of_titles'].
			"\nFree Trial Period: ".$membership->planInfo['free_trial'].
			"\nPrice: ".$membership->planInfo['price'].
			"\nPayment Method: " . $paymentMethod .
			"\n-------------------------------------------------".
			"\n\nNew Plan Requested".
			"\n\nPlan ID: ".$_POST['plan_id'].
			"\nPackage Name: ".$planInfo['package_name'].
			$row_day.
			"\nNumber of Titles That Can Be Issued: ".$planInfo['no_of_titles'].
			"\nFree Trial Period: ".$planInfo['free_trial'].
			"\nPrice: ".$planInfo['price'].
			"\nPayment Method: " . $paymentMethod;

			//STORE_OWNER_EMAIL_ADDRESS;
			mail(sysConfig::get('STORE_OWNER_EMAIL_ADDRESS'), $subject, $body ,"From:" . sysConfig::get('EMAIL_FROM'));
			$messageStack->addSession('pageStack', sysLanguage::get('UPGRADE_EMAIL_SENT'), 'success');
		}

		EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');
	}
?>