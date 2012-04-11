<?php
	$appContent = $App->getAppContentFile();
	$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
	if (isset($_GET['tfID'])){
		$App->setInfoBoxId($_GET['tfID']);
	}
?>