<?php
	require(DIR_WS_CLASSES . 'currencies.php');
	$currencies = new currencies();
	
	$appContent = $App->getAppContentFile();

	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.slide.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fold.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fade.js');
?>