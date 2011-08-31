<?php
	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'new'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('rental_wysiwyg/ckeditor.js');

	}
	if (isset($_GET['zID'])){
		$App->setInfoBoxId($_GET['zID']);
	} 
?>