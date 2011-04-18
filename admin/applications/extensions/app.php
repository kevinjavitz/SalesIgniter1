<?php
	$appContent = $App->getAppContentFile();
	
	$infoBoxId = null;
	if (isset($_GET['ext'])){
		$infoBoxId = $_GET['ext'];
	}
	
	$App->setInfoBoxId($infoBoxId);
?>