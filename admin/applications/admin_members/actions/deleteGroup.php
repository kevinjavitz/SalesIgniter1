<?php
$success = false;
if ((int)$_GET['gID'] > 1){
	$AdminGroups = Doctrine_Core::getTable('AdminGroups')->find((int)$_GET['gID']);
	if ($AdminGroups){
		$AdminGroups->delete();
		$success = true;
	}
}

	EventManager::attachActionResponse(array(
		'success' => $success
	), 'json');
?>