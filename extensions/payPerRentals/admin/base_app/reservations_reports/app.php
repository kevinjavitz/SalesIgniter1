<?php
	$appContent = $App->getAppContentFile();
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.slider.js');
	$App->addJavascriptFile('ext/jQuery/external/datetimepicker/jquery-ui-timepicker-addon.js');
	$App->addJavascriptFile('ext/jQuery/external/hoverintent/hoverIntent.js');
	//$App->addJavascriptFile('ext/jQuery/external/rfullcalendar/date.js');
	$App->addJavascriptFile('ext/jQuery/external/rfullcalendar/fullcalendar.js');
	$App->addStylesheetFile('ext/jQuery/external/rfullcalendar/fullcalendar.css');
	$App->addJavascriptFile('ext/jQuery/external/transposetable/tabletranspose.js');
	$App->addJavascriptFile('ext/jQuery/external/qTip/jquery.qtip.min.js');
	$App->addStylesheetFile('ext/jQuery/external/qTip/jquery.qtip.min.css');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.autocomplete.js');
	$App->addStylesheetFile('ext/jQuery/themes/smoothness/ui.autocomplete.css');
	//require('../includes/classes/product.php');
?>