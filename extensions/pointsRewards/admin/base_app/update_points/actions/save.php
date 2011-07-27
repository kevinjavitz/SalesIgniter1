<?php
switch($_POST['actionAddRemove']){
	case 'add':
		$pointsRewardsEarned = new pointsRewardsPointsEarned;
		$pointsRewardsEarned->customers_id = $_POST['customers_id'];
		$pointsRewardsEarned->points = $_POST['points'];
		$pointsRewardsEarned->date = date('Y-m-d');
		$pointsRewardsEarned->purchase_type = $_POST['purchaseType'];
		$pointsRewardsEarned->save();
		break;

	case 'deduct':
		$PointsDeducted = new pointsRewardsPointsDeducted;
		$PointsDeducted->customers_id = $_POST['customers_id'];
		$PointsDeducted->points = $_POST['points'];
		$PointsDeducted->date = date('Y-m-d');
		$PointsDeducted->purchase_type = $_POST['purchaseType'];
		$PointsDeducted->save();
		break;
}
$json = array(
	'success'   => true,
	'msgStack'  => $messageStack->parseTemplate('pageStack', $_POST['points'] . ' points ' . $_POST['actionAddRemove'] . 'ed for customer.', 'success')
);
if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
	EventManager::attachActionResponse($json, 'json');
}else{
	$messageStack->addSession('pageStack', 'Page not accesible', 'error');
	EventManager::attachActionResponse(itw_app_link('appExt=pointsRewards', 'update_points', 'default'), 'redirect');
}
?>