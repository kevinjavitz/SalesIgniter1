<?php
	$error = false;
    $appendMsg = '';
	if (empty($_POST['name'])) {
		$error = true;
	    $appendMsg .= 'Name is empty';
	}

	if ($_POST['membership_days'] < 0 && $_POST['membership_months'] < 0) {
		$error = true;
		$appendMsg .= 'Membership months or days must be more than 0';
	}

	if ($_POST['no_of_titles'] <= 0) {
		$error = true;
		$appendMsg .= 'Number of titles must be more than 0';
	}

	if ($_POST['price'] <= 0) {
		$error = true;
		$appendMsg .= 'Price must be more than 0';
	}

	if ($error === false){
		$Membership = Doctrine_Core::getTable('Membership');
		if (isset($_GET['pID'])){
			$Plan = $Membership->findOneByPlanId((int)$_GET['pID']);
		}else{
			$Plan = $Membership->create();
		}
				
		$Description = $Plan->MembershipPlanDescription;
		foreach($_POST['name'] as $langId => $Name){
			$Description[$langId]->language_id = $langId;
			$Description[$langId]->name = $Name;
		}
		$Plan->sort_order = $_POST['sort_order'];
		$Plan->membership_days = $_POST['membership_days'];
		$Plan->membership_months = $_POST['membership_months'];
		$Plan->free_trial = $_POST['free_trial'];
		$Plan->free_trial_amount = $_POST['free_trial_amount'];
		$Plan->no_of_titles = $_POST['no_of_titles'];
		$Plan->price = $_POST['price'];
		$Plan->rent_tax_class_id = $_POST['rent_tax_class_id'];
		$Plan->reccurring = (isset($_POST['not_reccurring'])?0:1);
		$Plan->save();
		
		if (isset($_POST['default_plan'])){
			Doctrine_Query::create()->update('Membership')->set('default_plan', '?', '0')->execute();
			
			Doctrine_Query::create()
			->update('Membership')
			->set('default_plan', '?', '1')
			->where('plan_id = ?', $Plan->plan_id)
			->execute();
		}
		
		EventManager::attachActionResponse(array(
			'success' => true,
			'pID' => $Plan->plan_id
		), 'json');
	}else{
		EventManager::attachActionResponse(array(
			'success' => false,
			'message' => sprintf(sysLanguage::get('TEXT_ERROR_MESSAGE'), $appendMsg)
		), 'json');
	}
?>