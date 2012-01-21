<?php
	$infoBoxId = null;
	if (isset($_GET['action']) && ($_GET['action'] == 'new_member' || $_GET['action'] == 'new_group')){
		$infoBoxId = 'new';
	}elseif (isset($_GET['mID'])){
		$infoBoxId = $_GET['mID'];
	}elseif (isset($_GET['gID'])){
		if ($_GET['gID'] == 'groups'){
			$infoBoxId = null;
		}else{
			$infoBoxId = $_GET['gID'];
		}
	}
	
	$App->setInfoBoxId($infoBoxId);
	
	$appContent = $App->getAppContentFile();
?>