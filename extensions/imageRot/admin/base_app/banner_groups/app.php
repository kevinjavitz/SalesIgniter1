<?php
	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'new_group'){
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
	}
?>