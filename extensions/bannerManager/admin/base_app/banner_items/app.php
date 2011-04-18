<?php
	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'new_banner'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
        $App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
		$App->addJavascriptFile('admin/rental_wysiwyg/adapters/jquery.js');
	}

?>