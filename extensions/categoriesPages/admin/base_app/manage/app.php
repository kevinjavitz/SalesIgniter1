<?php
	$appContent = $App->getAppContentFile();

	if (isset($_GET['cID'])){
		$App->setInfoBoxId($_GET['cID']);
	}
?>