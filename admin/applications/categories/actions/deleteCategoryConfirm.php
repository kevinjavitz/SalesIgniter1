<?php
	$success = false;

	$Categories = Doctrine_Core::getTable('Categories')->findOneByCategoriesId((int)$_POST['categories_id']);
	if ($Categories){
		$Categories->delete();
		$success = true;
	}

	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>