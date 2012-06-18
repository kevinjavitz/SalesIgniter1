<?php
if(isset($_GET['id']))
    $Editor->setData('store_id', $_GET['id']);
elseif(isset($_POST['id']))
    $Editor->setData('store_id', $_POST['id']);

EventManager::attachActionResponse(array(
		'success' => true,
        'store' => $Editor->getData('store_id')
	), 'json');
