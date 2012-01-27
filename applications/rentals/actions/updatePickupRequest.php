<?php

if(isset($_POST['pickupRequest'])){

		$RentedProductToPR = Doctrine_Core::getTable('CustomersToPickupRequests')->findOneByCustomersId($userAccount->getCustomerId());
		if(!$RentedProductToPR){
			$RentedProductToPR = new CustomersToPickupRequests;
		}
		$RentedProductToPR->customers_id = $userAccount->getCustomerId();
		$RentedProductToPR->pickup_requests_id = $_POST['pickupRequest'];
		$RentedProductToPR->save();
		$PickupReqStartDate = Doctrine_Core::getTable('PickupRequests')->find($_POST['pickupRequest']);
		$PickupReqType = Doctrine_Core::getTable('PickupRequestsTypes')->find($PickupReqStartDate->pickup_requests_types_id);
		$messageStack->addSession('pageStack', 'Your Pickup Request of: '.strftime(sysLanguage::getDateFormat('long'), strtotime($PickupReqStartDate->start_date)).' '.$PickupReqType->type_name.' has Been Selected');
}

$json = array(
	'success' => true
);
EventManager::attachActionResponse($json, 'json');
?>