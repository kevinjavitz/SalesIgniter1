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
		'terms_page'        => $Qmembeshipplan[0]['terms_page'],
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
	if(sysConfig::get('TERMS_CONDITIONS_PACKAGES') == 'true' && $Qmembeshipplan[0]['terms_page'] > 0){
		$Qpages = Doctrine_Query::create()
		->from('Pages p')
		->andWhere('p.pages_id = ?', $Qmembeshipplan[0]['terms_page'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$html = tep_draw_checkbox_field('terms_packages', '1', false) . '&nbsp;<a href="' . itw_app_link('appExt=infoPages', 'show_page', 'conditions', 'SSL') . '" onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', $Qpages[0]['page_key'], 'SSL') . '\',\'800\',\'600\');return false;">' . sysLanguage::get('TEXT_AGREE_TO_TERMS') . '</a>';
	}else{
		$html = '';
	}
	EventManager::attachActionResponse(array(
		'success' => true,
		'html' => $html
	), 'json');
?>