<?php
	$planId = $_POST['planID'];
	Session::set('plan_id', $planId);
	Session::set('planid', $planId);
	$paymentTerm = 'M';

	$Qplan = dataAccess::setQuery('select tm.*, tt.tax_rate from {members} tm left join {tax_rates} tt on tt.tax_rates_id = tm.rent_tax_class_id where plan_id = {plan_id}');
	$Qplan->setTable('{members}', TABLE_MEMBER);
	$Qplan->setTable('{tax_rates}', TABLE_TAX_RATES);
	$Qplan->setValue('{plan_id}', $planId);
	$Qplan->runQuery();

	$onePageCheckout->onePage['rentalPlan'] = array(
		'id'                => $planId,
		'pay_term'          => $paymentTerm,
		'name'              => $Qplan->getVal('package_name'),
		'months'            => $Qplan->getVal('membership_months'),
		'days'              => $Qplan->getVal('membership_days'),
		'no_of_titles'      => $Qplan->getVal('no_of_titles'),
		'price'             => ($paymentTerm == 'M' ? $Qplan->getVal('price') : $Qplan->getVal('price_yearly')),
		'tax_class'         => $Qplan->getVal('rent_tax_class_id'),
		'tax_rate'          => (float)$Qplan->getVal('tax_rate'),
		'free_trial'        => $Qplan->getVal('free_trial'),
		'free_trial_flag'   => $Qplan->getVal('free_trial') > 0 ? 'Y' : 'N',
		'free_trial_amount' => $Qplan->getVal('free_trial_amount')
	);

	Session::set('payment_recurring', 'true');
	Session::set('payment_rental', 'true');

	$onePageCheckout->loadMembershipPlan();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>