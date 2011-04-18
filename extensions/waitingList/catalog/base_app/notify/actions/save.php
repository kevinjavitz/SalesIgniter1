<?php
	$error = true;
	$Qcheck = Doctrine_Query::create()
	->select('count(*) as total')
	->from('WaitingList')
	->where('customers_email_address = ?', $_POST['email_address'])
	->andWhere('products_id = ?', (int) $_GET['pID'])
	->andWhere('purchase_type = ?', $_GET['purchaseType'])
	->andWhere('status = ?', 1)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qcheck[0]['total'] <= 0){
		$WaitingList = new WaitingList();
		$WaitingList->customers_email_address = $_POST['email_address'];
		$WaitingList->products_id = (int) $_GET['pID'];
		$WaitingList->purchase_type = $_GET['purchaseType'];
		$WaitingList->status = 1;
		$WaitingList->save();
		
		$error = false;
		$message = sysLanguage::get('TEXT_ADDED_TO_LIST');
	}else{
		$message = sysLanguage::get('TEXT_ALREADY_ON_LIST');
	}
	
	if ($error === false){
		$messageStack->addSession('pageStack', $message, 'success');
	}else{
		$messageStack->addSession('pageStack', $message, 'error');
	}
	
	EventManager::attachActionResponse(itw_app_link('products_id=' . (int) $_GET['pID'], 'product', 'info'));
?>