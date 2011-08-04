<?php
$pointsRewardsOrderStatuses = Doctrine_Core::getTable('pointsRewardsOrderStatuses');
$pointsRewardsOrderStatusesAll = Doctrine_Query::create()
		->from('pointsRewardsOrderStatuses')
		->execute();

foreach($pointsRewardsOrderStatusesAll as $pointsRewardsOrderStatus){
	$pointsRewardsOrderStatus->delete();
}

foreach($_POST['status_id'] as $royalties_status_id)
{
	$pointsRewardsOrderStatus = $pointsRewardsOrderStatuses->create();
	$pointsRewardsOrderStatus->orders_status_id = $royalties_status_id;
	$pointsRewardsOrderStatus->save();
}
tep_redirect(itw_app_link('appExt=pointsRewards', 'order_status', 'default'));
?>