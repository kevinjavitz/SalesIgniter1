<?php
	$response = array(
		'success' => false,
		'message' => sysLanguage::get('TEXT_ERROR_NOT_LOGGED_IN')
	);
	
	if ($userAccount->isLoggedIn()){
		$Qcheck = Doctrine_Query::create()
		->select('count(*) as total')
		->from('WaitingList')
		->where('customers_id = ?', $userAccount->getCustomerId())
		->andWhere('products_id = ?', (int) $_GET['pID'])
		->andWhere('purchase_type = ?', $_GET['purchaseType'])
		->andWhere('status = ?', 1)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($Qcheck[0]['total'] <= 0){
			$WaitingList = new WaitingList();
			$WaitingList->customers_id = $userAccount->getCustomerId();
			$WaitingList->customers_email_address = $userAccount->getEmailAddress();
			$WaitingList->products_id = (int) $_GET['pID'];
			$WaitingList->purchase_type = $_GET['purchaseType'];
			$WaitingList->status = 1;
			$WaitingList->save();
			
			$response = array(
				'success' => true,
				'message' => sysLanguage::get('TEXT_ADDED_TO_LIST')
			);
		}else{
			$response = array(
				'success' => true,
				'message' => sysLanguage::get('TEXT_ALREADY_ON_LIST')
			);
		}
	}
	
	EventManager::attachActionResponse($response, 'json');
?>