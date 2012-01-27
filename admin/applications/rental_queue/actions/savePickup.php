<?php
$PickupRequestsTable = Doctrine_Core::getTable('PickupRequests');

if(isset($_POST['pickup'])){

	$exc = array();
	foreach($_POST['pickup'] as $pickupid => $iPickup){
		$PickupRequests = $PickupRequestsTable->find($pickupid);
		if(!$PickupRequests){
			$PickupRequests = $PickupRequestsTable->create();
		}
		$PickupRequests->start_date = $iPickup['start_date'];
		$PickupRequests->pickup_requests_types_id = $iPickup['type_name'];
		$PickupRequests->save();
		$exc[] = $PickupRequests->pickup_requests_id;
	}
	Doctrine_Query::create()
		->delete('PickupRequests')
		->whereNotIn('pickup_requests_id', $exc)
		->execute();
}
EventManager::attachActionResponse(itw_app_link(null, 'rental_queue', 'pickup_requests'), 'redirect');
?>