<?php


$Groups = Doctrine_Core::getTable('ProductsGroups');
if (isset($_GET['gID'])){
	$Group = $Groups->find((int) $_GET['gID']);
}else{
	$Group = $Groups->getRecord();
}

$Group->product_group_name = $_POST['product_group_name'];
//$Group->product_group_limit = $_POST['product_group_limit'];
$Group->products = implode(',', $_POST['product_groups']);
$Group->save();

EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'gID')) . 'gID=' . $Group->product_group_id, null, 'default'), 'redirect');
?>