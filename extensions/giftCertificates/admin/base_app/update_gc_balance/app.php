<?php
	$appContent = $App->getAppContentFile();

    require(DIR_WS_CLASSES . 'currencies.php');
    $currencies = new currencies();

    $appContent = $App->getAppContentFile();
?>