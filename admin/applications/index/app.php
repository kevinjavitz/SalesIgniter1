<?php
	$appContent = $App->getAppContentFile();
	if ($App->getAppPage() != 'noAccess'){
		$App->addJavascriptFile('ext/jQuery/external/cookie/jquery.cookie.js');
		$App->addJavascriptFile('admin/applications/index/javascript/sesWidgets.js');
		$App->addStylesheetFile('admin/applications/index/javascript/index.css');
		$App->addStylesheetFile('admin/applications/index/javascript/default.js.css');
	
		require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
		$currencies = new currencies();
	}
?>