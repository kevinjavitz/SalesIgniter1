<?php
	$planId = $_POST['planID'];
	Session::set('plan_id', $planId);
	Session::set('planid', $planId);
	$paymentTerm = 'M';

	$Qmembeshipplan = Doctrine_Query::create()
	->from('Membership m')
	->leftJoin('m.MembershipPlanDescription md')
	->leftJoin('m.TaxRates tt')
	->where('m.plan_id=?', $planId)
	->andWhere('md.language_id=?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$onePageCheckout->onePage['rentalPlan'] = array(
		'id'                => $planId,
		'pay_term'          => $paymentTerm,
		'name'              => $Qmembeshipplan[0]['MembershipPlanDescription'][0]['name'],
		'months'            => $Qmembeshipplan[0]['membership_months'],
		'days'              => $Qmembeshipplan[0]['membership_days'],
		'no_of_titles'      => $Qmembeshipplan[0]['no_of_titles'],
		'price'             => ($paymentTerm == 'M' ? $Qmembeshipplan[0]['price'] : $Qmembeshipplan[0]['price_yearly']),
		'tax_class'         => $Qmembeshipplan[0]['rent_tax_class_id'],
		'tax_rate'          => (float)$Qmembeshipplan[0]['TaxRates']['tax_rate'],
		'free_trial'        => $Qmembeshipplan[0]['free_trial'],
		'free_trial_flag'   => $Qmembeshipplan[0]['free_trial'] > 0 ? 'Y' : 'N',
		'free_trial_amount' => $Qmembeshipplan[0]['free_trial_amount']
	);

	Session::set('payment_recurring', 'true');
	Session::set('payment_rental', 'true');

	$onePageCheckout->loadMembershipPlan();

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>