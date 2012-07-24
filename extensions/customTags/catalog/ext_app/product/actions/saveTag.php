<?php
//$tagArr = ParseTagString($_POST['tags_names']);
$added = false;
//foreach($tagArr as $iTag){
	$iTag = trim($_POST['tags_names']);
	$CustomTag = Doctrine_Core::getTable('CustomTags')->findOneByTagName($iTag);
	if(!$CustomTag){
		$CustomTag = new CustomTags();
		$CustomTag->tag_name = $iTag;
		$CustomTag->tag_status = 0;
		$CustomTag->save();
	}
	$tagId = $CustomTag->tag_id;
	$ProductTags = Doctrine_Core::getTable('TagsToProducts')->findOneByTagIdAndProductsIdAndCustomersId($tagId, $_GET['products_id'], $userAccount->getCustomerId());
	if(!$ProductTags){
		$ProductTags = new TagsToProducts();
		$ProductTags->tag_id = $tagId;
		$ProductTags->products_id = $_GET['products_id'];
		$ProductTags->customers_id = $userAccount->getCustomerId();
		$ProductTags->save();
	}
	$added = true;
//}
if($added){
	$TagsToProducts = Doctrine_Core::getTable('TagsToProducts')->findOneByTagIdAndProductsIdAndCustomersId($_POST['tag_id'], $_GET['products_id'], $userAccount->getCustomerId());
	$TagsToProducts->delete();
}
$json = array(
	'success' => true
);
EventManager::attachActionResponse($json, 'json');
?>