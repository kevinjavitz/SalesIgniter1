<?php
	$Orders = Doctrine_Core::getTable('Orders');
	if (isset($_GET['oID'])){
		$NewOrder = $Orders->find((int) $_GET['oID']);
	}else{
		$NewOrder = new Orders();
		$createAccount = false;
		if (isset($_POST['customers_id'])){
			$NewOrder->customers_id = $_POST['customers_id'];
		}elseif (isset($_POST['account_password']) && !empty($_POST['account_password'])){
			$Editor->setEmailAddress($_POST['email']);
			$Editor->setTelephone($_POST['telephone']);
			$Editor->createCustomerAccount($NewOrder->Customers);
		}
	}
	
	$NewOrder->customers_email_address = $Editor->getEmailAddress();
	$NewOrder->customers_telephone = $Editor->getTelephone();
	$NewOrder->orders_status = $_POST['status'];
	$NewOrder->currency = $Editor->getCurrency();
	$NewOrder->currency_value = $Editor->getCurrencyValue();
	$NewOrder->shipping_module = $Editor->getShippingModule();
	$NewOrder->usps_track_num = $_POST['usps_track_num'];
	$NewOrder->usps_track_num2 = $_POST['usps_track_num2'];
	$NewOrder->ups_track_num = $_POST['ups_track_num'];
	$NewOrder->ups_track_num2 = $_POST['ups_track_num2'];
	$NewOrder->fedex_track_num = $_POST['fedex_track_num'];
	$NewOrder->fedex_track_num2 = $_POST['fedex_track_num2'];
	$NewOrder->dhl_track_num = $_POST['dhl_track_num'];
	$NewOrder->dhl_track_num2 = $_POST['dhl_track_num2'];
//	$NewOrder->payment_module = $Editor->getPaymentModule();

	$Editor->AddressManager->updateFromPost();
	$Editor->AddressManager->addAllToCollection($NewOrder->OrdersAddresses);

	$Editor->ProductManager->updateFromPost();
	$Editor->ProductManager->addAllToCollection($NewOrder->OrdersProducts);

	$Editor->TotalManager->updateFromPost();
	$Editor->TotalManager->addAllToCollection($NewOrder->OrdersTotal);

	EventManager::notify('OrderSaveBeforeSave', $NewOrder);
	//echo '<pre>';print_r($NewOrder->toArray());itwExit();

	$success = true;
	if (!isset($_GET['oID'])){
		$NewOrder->bill_attempts = 1;
		$NewOrder->payment_module = $_POST['payment_method'];
		$success = $Editor->PaymentManager->processPayment($_POST['payment_method'], $NewOrder);
	}
	
	if ($success === true){
		if (isset($_POST['notify_comments'])){
			$StatusHistory = new OrdersStatusHistory();
			$StatusHistory->orders_status_id = $_POST['status'];
			$StatusHistory->customer_notified = (int) (isset($_POST['notify']));
			$StatusHistory->comments = $_POST['comments'];
			
			$NewOrder->OrdersStatusHistory->add($StatusHistory);
		}
		$NewOrder->save();
		if (!isset($_GET['oID'])){
			$NewOrder->Customers->customers_default_address_id = $NewOrder->Customers->AddressBook[0]->address_book_id;
			$NewOrder->save();
			
			$Editor->sendNewOrderEmail($NewOrder);
		}
		
		EventManager::attachActionResponse(itw_app_link('oID=' . $NewOrder->orders_id, 'orders', 'details'), 'redirect');
	}else{
		$Editor->addErrorMessage($success['error_message']);
		if (isset($_GET['oID'])){
			EventManager::attachActionResponse(itw_app_link('appExt=orderCreator&error=true&oID=' . $_GET['oID'], 'default', 'new'), 'redirect');
		}else{
			EventManager::attachActionResponse(itw_app_link('appExt=orderCreator&error=true', 'default', 'new'), 'redirect');
		}
	}
?>