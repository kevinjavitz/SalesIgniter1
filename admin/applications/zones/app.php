<?php
	$appContent = $App->getAppContentFile();

	if ($App->getAppPage() == 'new'){
		$App->addJavascriptFile('http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=' . sysConfig::get('GOOGLE_MAPS_API_KEY'));
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
		$App->addJavascriptFile('rental_wysiwyg/ckeditor.js');

	}
	if (isset($_GET['zID'])){
		$App->setInfoBoxId($_GET['zID']);
	} 
?>