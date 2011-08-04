<?php
$error = false;
$success = false;

if(isset($_POST['apply'])){
	if($_POST['apply'] == 'true')
	{
		if (isset($_POST['points']) && (int)$_POST['points'] > 0) {
			Session::set('pointsRewards_points', (int)$_POST['points']);
		}
	} else {
		Session::remove('pointsRewards_points');
	}
}


if ($onePageCheckout->isMembershipCheckout()) {
	$onePageCheckout->loadMembershipPlan();
}
OrderTotalModules::process();
EventManager::attachActionResponse(array(
										'success' => ($error ? false : true),
										'orderTotalRows' => OrderTotalModules::output()
								   ), 'json');
?>