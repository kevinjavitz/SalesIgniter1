<?php
	$appContent = $App->getAppContentFile();
	if (isset($_GET['rID'])){
		$App->setInfoBoxId($_GET['rID']);
	}
?>