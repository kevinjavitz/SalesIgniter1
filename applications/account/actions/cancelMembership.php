<?php
	/*
	$sql = "select c.customers_firstname,md.name,c.customers_lastname,customers_email_address,c.plan_id,payment_method,membership_days,no_of_titles,price,free_trial,DATE_FORMAT(membership_date,'%m/%d/%Y') as mem_date from ".TABLE_CUSTOMERS." c, ".TABLE_MEMBERSHIP." m left join membership_plan_description md on m.plan_id=md.plan_id where md.language_id=".Session::get('languages_id')." and customers_id=".$userAccount->getCustomerId()." and c.plan_id=m.plan_id";
	$rs_customer = tep_db_query($sql);
	$row_customer = tep_db_fetch_array($rs_customer);

	//$update_sql = "update ".TABLE_CUSTOMERS . " set canceled = 1, cancel_date = Now() where customers_id=".$customer_id;
	$update_sql = "update " . TABLE_CUSTOMERS . " set canceled='0', ismember='U', activate='N' where customers_id=".$userAccount->getCustomerId();
	*/
	$membership =& $userAccount->plugins['membership'];
	$planInfo = $membership->getPlanInfo($membership->getPlanId());
	$adminEditLink = itw_admin_app_link('cID=' . $userAccount->getCustomerId(),'customers','edit',SSL);

	$emailEvent = new emailEvent('membership_cancel_request');
	$emailEvent->setVars(array(
		'customerID' => $userAccount->getCustomerId(),
		'full_name' => $userAccount->getFullName(),
		'emailAddress' => $userAccount->getEmailAddress(),
		'paymentMethod' => $membership->getPaymentMethod(),
		'subscriptionDate' => $membership->getMembershipDate(),
		'planID' => $membership->getPlanId(),
		'packageName' =>  $membership->getPlanName(),
		'numberOfRentals' => $membership->getRentalsAllowed(),
		'freeTrialPeriod' => $planInfo['free_trial'],
		'adminEditLink' => $adminEditLink,
		'price' => $currencies->format($membership->getMembershipPrice(), true),
		'membershipIsDays' => false,
		'membershipIsMonths' => false
	));

	if ($membership->getMembershipDays() > 0){
		$emailEvent->setVar('membershipIsDays', true);
		$emailEvent->setVar('membershipPeriod', $membership->getMembershipDays());
	}else{
		$emailEvent->setVar('membershipIsMonths', true);
		$emailEvent->setVar('membershipPeriod', $membership->getMembershipMonths());
	}

	$emailEvent->sendEmail(array(
		'email' => STORE_OWNER_EMAIL_ADDRESS,
		'name' => STORE_OWNER
	));

	$messageStack->add_session('pageStack', sysLanguage::get('CANCELLATION_EMAIL_SENT'), 'success');
	EventManager::attachActionResponse(itw_app_link(null, 'account', 'default', 'SSL'), 'redirect');
?>