<?php
	$appContent = $App->getAppContentFile();		
	if (isset($_GET['pID'])){
		$App->setInfoBoxId($_GET['pID']);
	}

	if (substr($App->getAppPage(), 0, 3) == 'new'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/adapters/jquery.js');
	}
?>