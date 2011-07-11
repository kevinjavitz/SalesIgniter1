<?php

	$appContent = $App->getAppContentFile();
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
	$App->addJavascriptFile('ext/jQuery/external/fancybox/jquery.fancybox.js');

	$App->addStylesheetFile('ext/jQuery/external/datepick/css/jquery.datepick.css');
	$App->addStylesheetFile('ext/jQuery/external/uploadify/jquery.uploadify.css');
	$App->addStylesheetFile('ext/jQuery/external/fancybox/jquery.fancybox.css');
?>