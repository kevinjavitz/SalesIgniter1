<?php
	$appContent = $App->getAppContentFile();
	if ($App->getAppPage() == 'new'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.autocomplete.js');
		$App->addStylesheetFile('ext/jQuery/themes/smoothness/ui.autocomplete.css');
	}
	
	if (isset($_GET['eID'])){
		$App->setInfoBoxId($_GET['eID']);
	} 
?>