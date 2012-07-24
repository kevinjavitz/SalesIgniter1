<?php
$TagsToProducts = Doctrine_Core::getTable('TagsToProducts')->findOneByTagIdAndProductsIdAndCustomersId($_POST['tag_id'], $_GET['products_id'], $userAccount->getCustomerId());
$TagsToProducts->delete();
EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>