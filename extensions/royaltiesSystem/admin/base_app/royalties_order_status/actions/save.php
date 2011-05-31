<?php
$royaltiesSystemOrderStatuses = Doctrine_Core::getTable('royaltiesSystemOrderStatuses');
$royaltiesSystemOrderStatusesAll = Doctrine_Query::create()
		->from('royaltiesSystemOrderStatuses')
		->execute();

foreach($royaltiesSystemOrderStatusesAll as $royaltiesSystemOrderStatus){
	$royaltiesSystemOrderStatus->delete();
}

foreach($_POST['royalties_status_id'] as $royalties_status_id)
{
	$royaltiesSystemOrderStatus = $royaltiesSystemOrderStatuses->create();
	$royaltiesSystemOrderStatus->orders_status_id = $royalties_status_id;
	$royaltiesSystemOrderStatus->save();
}
EventManager::attachActionResponse(itw_app_link('appExt=royaltiesSystem', 'royalties_order_status', 'default'), 'redirect');
?>
