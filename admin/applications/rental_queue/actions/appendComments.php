<?php
	$comments = isset($_POST['comments'])?$_POST['comments']:'';
	$queueId = $_POST['queue_id'];
    $RentedQueue = Doctrine_Core::getTable('RentedQueue')->find($queueId);
	$RentedQueue->comments = $comments;
	$RentedQueue->save();

	EventManager::attachActionResponse(array(
		'success' => true,
	), 'json');
?>