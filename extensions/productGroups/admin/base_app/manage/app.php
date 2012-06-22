<?php
	$appContent = $App->getAppContentFile();

	if (isset($_GET['gID'])){
		$App->setInfoBoxId($_GET['gID']);
	} 
?>