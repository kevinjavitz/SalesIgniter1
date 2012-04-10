<?php
	$appContent = $App->getAppContentFile();

	if (isset($_GET['tfID'])){
		$App->setInfoBoxId($_GET['tfID']);
	}
?>