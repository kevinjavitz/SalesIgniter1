<?php
	$appContent = $App->getAppContentFile();
	if ($App->getAppPage() == 'new'){
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');

	}
	if (isset($_GET['cID'])){
		$App->setInfoBoxId($_GET['cID']);
	} 
?>