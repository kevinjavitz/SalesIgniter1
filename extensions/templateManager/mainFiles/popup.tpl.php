<script>
$(document).ready(function (){
	var newHeight = $('img', $('.popupWindow')).height() +45;
	var newWidth = $('img', $('.popupWindow')).width() +45;

	if (newHeight > 100){
		$('.popupWindow').dialog('option', 'height', newHeight);
	}

	if (newWidth > 100){
		$('.popupWindow').dialog('option', 'width', newWidth);
	}
});
</script>
<?php
require(sysConfig::getDirFsCatalog() . 'includes/classes/template.php');
$thisApp = $App->getAppName();
$thisAppPage = $App->getAppPage() . '.php';
$thisExtension = (isset($_GET['appExt']) ? $_GET['appExt'] : '');

$layoutPath = sysConfig::getDirFsCatalog() . 'extensions/templateManager/mainFiles';
if (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/popup.tpl')){
	$layoutPath = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir');
}

$Template = new Template('popup.tpl', $layoutPath);

$Template->setVars(array(
	'stylesheets' => $App->getStylesheetFiles(),
	'javascriptFiles' => $App->getJavascriptFiles(),
	'pageStackOutput' => ($messageStack->size('pageStack') > 0 ? $messageStack->output('pageStack') : '')
));

if (isset($_GET['cPath']) && $thisApp == 'index'){
	$thisAppPage = 'index.php';
}

$Qpages = Doctrine_Query::create()
	->from('TemplatePages')
	->where('extension = ?', $thisExtension)
	->andWhere('application = ?', $thisApp)
	->andWhere('page = ?', $thisAppPage)
	->fetchOne();
$layoutArr = explode(',', $Qpages->layout_id);

$QtemplateId = Doctrine_Query::create()
	->select('template_id')
	->from('TemplateManagerTemplatesConfiguration')
	->where('configuration_key = ?', 'DIRECTORY')
	->andWhere('configuration_value = ?', Session::get('tplDir'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$QtemplateLayouts = Doctrine_Query::create()
	->from('TemplateManagerLayouts')
	->where('template_id = ?', $QtemplateId[0]['template_id'])
	->andWhereIn('layout_id', $layoutArr)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$layout_id = $QtemplateLayouts[0]['layout_id'];
$Template->set('templateLayoutId', $layout_id);

function addStyles($El, $Styles) {
	if ($El->hasAttr('id') && $El->attr('id') != ''){
		return;
	}

	$css = array();
	foreach($Styles as $sInfo){
		if (substr($sInfo->definition_value, 0, 1) == '{' || substr($sInfo->definition_value, 0, 1) == '['){
			$css[$sInfo->definition_key] = json_decode($sInfo->definition_value);
		}
		else {
			$css[$sInfo->definition_key] = $sInfo->definition_value;
		}
		$El->css($sInfo->definition_key, $css[$sInfo->definition_key]);
	}
}

function addInputs($El, $Config) {
	foreach($Config as $cInfo){
		if ($cInfo->configuration_key != 'id') {
			continue;
		}

		$El->attr('id', $cInfo->configuration_value);
	}
}

function processContainerChildren($MainObj, &$El) {
	foreach($MainObj->Children as $childObj){
		$NewEl = htmlBase::newElement('div')
			->addClass('container');

		if ($childObj->Configuration->count() > 0){
			addInputs($NewEl, $childObj->Configuration);
		}

		if ($childObj->Styles->count() > 0){
			addStyles($NewEl, $childObj->Styles);
		}

		$El->append($NewEl);
		processContainerColumns($NewEl, $childObj->Columns);
		if ($childObj->Children->count() > 0){
			processContainerChildren($childObj, $NewEl);
		}
	}
}

function processContainerColumns(&$Container, $Columns) {
	if (!$Columns) {
		return;
	}

	foreach($Columns as $col){
		$ColEl = htmlBase::newElement('div')
			->addClass('column');

		if ($col->Configuration->count() > 0){
			addInputs($ColEl, $col->Configuration);
		}

		if ($col->Styles->count() > 0){
			addStyles($ColEl, $col->Styles);
		}

		$WidgetHtml = '';
		if ($col->Widgets->count() > 0){
			foreach($col->Widgets as $wid){
				$WidgetSettings = '';
				if ($wid->Configuration->count() > 0){
					foreach($wid->Configuration as $cInfo){
						if ($cInfo->configuration_key == 'widget_settings'){
							$WidgetSettings = json_decode($cInfo->configuration_value);
						}
					}
				}

				$className = 'InfoBox' . ucfirst($wid->identifier);
				if (!class_exists($className)){
					$QboxPath = Doctrine_Query::create()
						->select('box_path')
						->from('TemplatesInfoboxes')
						->where('box_code = ?', $wid->identifier)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					require($QboxPath[0]['box_path'] . 'infobox.php');
				}
				$Class = new $className;

				if (isset($WidgetSettings->template_file) && !empty($WidgetSettings->template_file)){
					$Class->setBoxTemplateFile($WidgetSettings->template_file);
				}
				if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
					$Class->setBoxId($WidgetSettings->id);
				}
				if (isset($WidgetSettings->widget_title) && !empty($WidgetSettings->widget_title)){
					$Class->setBoxHeading($WidgetSettings->widget_title->{Session::get('languages_id')});
				}

				$Class->setWidgetProperties($WidgetSettings);

				$WidgetHtml .= $Class->show();
			}
		}
		$ColEl->html($WidgetHtml);

		$Container->append($ColEl);
	}
}

$pageContentPath = sysConfig::getDirFsCatalog() . 'extensions/templateManager/widgetTemplates';
if (file_exists(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/popup.tpl')){
	$pageContentPath = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir');
}

$pageContent = new Template('popup.tpl', $pageContentPath);

$checkFiles = array(
	sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/applications/' . $App->getAppName() . '/' . $App->getPageName() . '.php',
	sysConfig::getDirFsCatalog() . 'applications/' . $App->getAppName() . '/pages/' . $App->getPageName() . '.php',
	sysConfig::getDirFsCatalog() . 'applications/' . $appContent,
	(isset($appContent) ? $appContent : false)
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

$Layout = Doctrine_Core::getTable('TemplateManagerLayouts')->find($layout_id);
if ($Layout->Containers->count() > 0){
	foreach($Layout->Containers as $MainObj){
		if ($MainObj->Parent->container_id > 0) {
			continue;
		}

		$MainEl = htmlBase::newElement('div')
			->addClass('container');

		if ($MainObj->Configuration->count() > 0){
			addInputs($MainEl, $MainObj->Configuration);
		}

		if ($MainObj->Styles->count() > 0){
			addStyles($MainEl, $MainObj->Styles);
		}

		processContainerColumns($MainEl, $MainObj->Columns);
		if ($MainObj->Children->count() > 0){
			processContainerChildren($MainObj, $MainEl);
		}
		$Construct->append($MainEl);
	}
}

$Template->set('templateLayoutContent', $Construct->draw());
echo $Template->parse();
?>