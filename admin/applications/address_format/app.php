<?php
	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'new'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('rental_wysiwyg/ckeditor.js');

	}
	if (isset($_GET['fID'])){
		$App->setInfoBoxId($_GET['fID']);
	} 
?>