<?php
	$appContent = $App->getAppContentFile();

	if (isset($_GET['fID'])){
		$App->setInfoBoxId($_GET['fID']);
	}
?>