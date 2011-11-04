<?php
	//Doctrine_Core::loadAllModels();
	$appContent = $App->getAppContentFile();

	if ($App->getPageName() == 'defines'){
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
	}
?>