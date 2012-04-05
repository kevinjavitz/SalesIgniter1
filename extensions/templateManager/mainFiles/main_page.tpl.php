<?php

if(isset($_GET['genTemplate'])){
	Session::set('tplDir', $_GET['genTemplate']);
}
require(sysConfig::getDirFsCatalog() . 'includes/classes/template.php');

$thisApp = $App->getAppName();
if(!isset($_GET['actualPage'])){
	$thisAppPage = $App->getAppPage() . '.php';
}else{
	$thisAppPage = $_GET['actualPage'];
}

$thisExtension = (isset($_GET['appExt']) ? $_GET['appExt'] : '');
$thisTemplate = Session::get('tplDir');

$layoutPath = sysConfig::getDirFsCatalog() . 'extensions/templateManager/mainFiles';
if(!isset($_GET['tplDir']) || $_GET['tplDir'] != 'codeGeneration'){
	if (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/layout.tpl')){
		$layoutPath = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir');
	}
	$Template = new Template('layout.tpl', $layoutPath);
}else{
	$Template = new Template('layoutCodeGeneration.tpl', $layoutPath);
}

$Template->setVars(array(
	'stylesheets' => $App->getStylesheetFiles(),
	'javascriptFiles' => $App->getJavascriptFiles(),
	'pageStackOutput' => ($messageStack->size('pageStack') > 0 ? $messageStack->output('pageStack') : '')
));

$Qpages = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select layout_id from template_pages where extension = "' . $thisExtension . '" and application = "' . $thisApp . '" and page = "' . $thisAppPage . '"');
$Page = $Qpages[0];
$pageLayouts = $Page['layout_id'];

$QtemplateId = Doctrine_Manager::getInstance()
	->getCurrentConnection()
	->fetchAssoc('select template_id from template_manager_templates_configuration where configuration_key = "DIRECTORY" and configuration_value = "' . $thisTemplate . '"');
$TemplateId = $QtemplateId[0];

$Page['layout_id'] = implode(',',array_filter(explode(',',$Page['layout_id'])));

if(isset($Page['layout_id']) && !empty($Page['layout_id'])){
	$QpageLayout = Doctrine_Manager::getInstance()
		->getCurrentConnection()
		->fetchAssoc('select layout_id from template_manager_layouts where template_id = "' . $TemplateId['template_id'] . '" and layout_id IN(' . $Page['layout_id'] . ')');
	if(isset($QpageLayout[0])){
		$PageLayoutId = $QpageLayout[0];
	}
}

if(!isset($_GET['lID'])){
	if(!isset($QpageLayout[0])){
		$QpageLayout = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select layout_id from template_manager_layouts where template_id = "' . $TemplateId['template_id'] . '" ');
		$tLayouts = array();
		foreach($QpageLayout as $PageLayoutId){
			$tLayouts[] = $PageLayoutId['layout_id'];
		}
		$maxLayout = -1;
		$maxCount = -1;
		foreach($tLayouts as $iLayout){
			$Qpages = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select count(*) from template_pages where FIND_IN_SET("'.$iLayout.'",layout_id)');
			$PageCount = $Qpages[0];
			if($maxCount < $PageCount){
				$maxCount = $PageCount;
				$maxLayout = $iLayout;
			}
		}
		$PageLayoutId['layout_id'] = $maxLayout;
		$pageLayouts .= ','.$maxLayout;
		Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->exec('update template_pages set layout_id = "'.$pageLayouts.'" where extension = "' . $thisExtension . '" and application = "' . $thisApp . '" and page = "' . $thisAppPage . '"');
	}

	$layout_id = $PageLayoutId['layout_id'];
}else{
	$layout_id = $_GET['lID'];
}

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
	if (file_exists($filePath) && is_file($filePath)){
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