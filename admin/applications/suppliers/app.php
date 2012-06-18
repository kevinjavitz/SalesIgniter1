<?php
	$appContent = $App->getAppContentFile();

		if ($App->getAppPage() == 'new_supplier'){
			$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
			$App->addJavascriptFile('ext/jQuery/external/datepick/jquery.datepick.js');
			$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
			$App->addJavascriptFile('ext/jQuery/external/autocomplete/jquery.autocomplete.js');
			$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
			$App->addJavascriptFile('ext/jQuery/external/uploadify/swfobject.js');
			$App->addJavascriptFile('ext/jQuery/external/uploadify/jquery.uploadify.js');
			$App->addJavascriptFile('ext/jQuery/external/fancybox/jquery.fancybox.js');
		
			$App->addStylesheetFile('ext/jQuery/external/datepick/css/jquery.datepick.css');
			$App->addStylesheetFile('ext/jQuery/external/uploadify/jquery.uploadify.css');
			$App->addStylesheetFile('ext/jQuery/external/fancybox/jquery.fancybox.css');
		}
	
?>