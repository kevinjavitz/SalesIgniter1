<?php

	$tag_name = $_POST['tag_name'];
	$tag_status = $_POST['tag_status'];
	//$available = $_POST['rental_status_available'];

	$tag = Doctrine_Core::getTable('CustomTags');
	if (isset($_GET['tID'])){
		$tag = $tag->find((int)$_GET['tID']);
	}


	$tag->tag_name = $tag_name;
	$tag->tag_status = $tag_status;
	$tag->save();

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'tID')) . 'tID=' . $tag->tag_id, null, 'default'), 'redirect');
?>