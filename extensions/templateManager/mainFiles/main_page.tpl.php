<?php
require(sysConfig::getDirFsCatalog() . 'includes/classes/template.php');
$thisApp = $App->getAppName();
$thisAppPage = $App->getAppPage() . '.php';
$thisExtension = (isset($_GET['appExt']) ? $_GET['appExt'] : '');
$thisTemplate = Session::get('tplDir');

$layoutPath = sysConfig::getDirFsCatalog() . 'extensions/templateManager/mainFiles';
if (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/layout.tpl')){
	$layoutPath = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir');
}

$Template = new Template('layout.tpl', $layoutPath);

$Template->setVars(array(
	'stylesheets' => $App->getStylesheetFiles(),
	'javascriptFiles' => $App->getJavascriptFiles(),
	'pageStackOutput' => ($messageStack->size('pageStack') > 0 ? $messageStack->output('pageStack') : '')
));

if (isset($_GET['cPath']) && $thisApp == 'index'){
	$thisAppPage = 'index.php';
}

$Qpages = mysql_query('select layout_id from template_pages where extension = "' . $thisExtension . '" and application = "' . $thisApp . '" and page = "' . $thisAppPage . '"');
$Page = mysql_fetch_assoc($Qpages);

$QtemplateId = mysql_query('select template_id from template_manager_templates_configuration where configuration_key = "DIRECTORY" and configuration_value = "' . $thisTemplate . '"');
$TemplateId = mysql_fetch_assoc($QtemplateId);

$QpageLayout = mysql_query('select layout_id from template_manager_layouts where template_id = "' . $TemplateId['template_id'] . '" and layout_id IN(' . $Page['layout_id'] . ')');
$PageLayoutId = mysql_fetch_assoc($QpageLayout);

$layout_id = $PageLayoutId['layout_id'];
$Template->set('templateLayoutId', $layout_id);

$templateDir = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir');

$pageContentPath = sysConfig::getDirFsCatalog() . 'extensions/templateManager/widgetTemplates';
if (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/pageContent.tpl')){
	$pageContentPath = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir');
}

$pageContent = new Template('pageContent.tpl', $pageContentPath);

$checkFiles = array(
	(isset($appContent) ? $appContent : false),
	sysConfig::getDirFsCatalog() . 'applications/' . $appContent,
	sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/applications/' . $App->getAppName() . '/' . $App->getPageName() . '.php',
	sysConfig::getDirFsCatalog() . 'applications/' . $App->getAppName() . '/pages/' . $App->getPageName() . '.php'
);

$requireFile = false;
foreach($checkFiles as $filePath){
	if (file_exists($filePath)){
		$requireFile = $filePath;
		break;
	}
}

if ($requireFile !== false){
	require($requireFile);
}
$Template->set('pageContent', $pageContent);

$Construct = htmlBase::newElement('div')->attr('id', 'bodyContainer');
$ExtTemplateManager = $appExtension->getExtension('templateManager');
$ExtTemplateManager->buildLayout($Construct, $layout_id);
$Template->set('templateLayoutContent', $Construct->draw());

echo $Template->parse();
?>