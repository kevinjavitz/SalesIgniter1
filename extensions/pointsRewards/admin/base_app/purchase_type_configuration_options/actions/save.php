<?php
$pointsRewardsPurchaseTypesAll = Doctrine_Query::create()
		->from('pointsRewardsPurchaseTypes')
		->execute();

foreach($pointsRewardsPurchaseTypesAll as $pointsRewardsPurchaseType){
	$pointsRewardsPurchaseType->delete();
}

foreach($_POST['enabled'] as $purchase_type=>$purchase_type_value)
{
	$pointsRewardsPurchaseType = new pointsRewardsPurchaseTypes;
	$pointsRewardsPurchaseType->purchase_type = $purchase_type;
	$pointsRewardsPurchaseType->percentage = $_POST['percentage'][$purchase_type];
	$pointsRewardsPurchaseType->threshold = $_POST['threshold'][$purchase_type];
	$pointsRewardsPurchaseType->conversionRatio = $_POST['conversionratio'][$purchase_type];
	$pointsRewardsPurchaseType->save();
}
tep_redirect(itw_app_link('appExt=pointsRewards', 'purchase_type_configuration_options', 'default'));
?>