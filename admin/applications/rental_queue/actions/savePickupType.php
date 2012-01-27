<?php
$PickupRequestsTypesTable = Doctrine_Core::getTable('PickupRequestsTypes');
Doctrine_Query::create()
	->delete('PickupRequestsTypes')
	->execute();

if(isset($_POST['pickup'])){
	foreach($_POST['pickup'] as $pickupid => $iPickup){
		$PickupRequestsTypes = $PickupRequestsTypesTable->create();
		$PickupRequestsTypes->type_name = $iPickup['type_name'];
		$PickupRequestsTypes->save();
	}
}
EventManager::attachActionResponse(itw_app_link(null, 'rental_queue', 'pickup_requests_types'), 'redirect');
?>