<?php
$appContent = $App->getAppContentFile();

if (isset($_GET['gID'])){
	$App->setInfoBoxId($_GET['gID']);
}elseif (isset($_GET['action']) && $_GET['action'] == 'new_group'){
	$App->setInfoBoxId('new');
}

$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');
$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.slide.js');
$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fold.js');
$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fade.js');
