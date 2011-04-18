<?php
	$sent = 0;
	$Qcheck = Doctrine_Query::create()
	->from('WaitingList')
	->where('status = ?', 1)
	->execute();
	if ($Qcheck->count() > 0){
		$products = array();
		$users = array();
		foreach($Qcheck as $notify){
			$pID = $notify->products_id;
			$pType = $notify->purchase_type;
			$cID = $notify->customers_id;
			$emailAddress = $notify->customers_email_address;
			
			if (!isset($products[$pID])){
				$products[$pID] = new Product($pID);
			}
			if ($cID > 0 && !isset($users[$cID])){
				$users[$cID] = new RentalStoreUser($cID);
				$userName = $userAccount->getFullName();
				$userEmail = $userAccount->getEmailAddress();
				$useLangId = $userAccount->getLanguageId();
			}else{
				$userName = $emailAddress;
				$userEmail = $emailAddress;
				$useLangId = Session::get('languages_id');
			}
			
			$PurchaseType = $products[$pID]->getPurchaseType($pType);
			if ($PurchaseType->hasInventory() === true){
				$emailEvent = new emailEvent('waiting_list_notify', $useLangId);
				$emailEvent->setVars(array(
					'fullName' => $userName,
					'productName' => $products[$pID]->getName(),
					'productInfoLink' => itw_app_link('products_id=' . $pID, 'product', 'info')
				));
				
				$emailEvent->sendEmail(array(
					'name' => $userName,
					'email' => $userEmail
				));
				
				$notify->status = 0;
				$notify->first_notified = date('Y-m-d h:i:s');
				$notify->last_notified = date('Y-m-d h:i:s');
				$notify->save();
				
				$sent++;
			}
		}
	}
	echo 'Notifications Sent ( ' . $sent . ' )';
?>