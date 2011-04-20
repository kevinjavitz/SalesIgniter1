<?php
	require(DIR_WS_CLASSES . 'template.php');
$thisTemplate = Session::get('tplDir');
$thisApp = $App->getAppName();
$thisAppPage = $App->getAppPage() . '.php';
$thisDir = sysConfig::getDirFsCatalog() . 'templates/' . $thisTemplate;
$thisFile = basename($_SERVER['PHP_SELF']);
$thisExtension = (isset($_GET['appExt']) ? $_GET['appExt'] : '');

$Template = new Template('layout.tpl', $thisDir);

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
	->andWhere('configuration_value = ?', $thisTemplate)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$QtemplateLayouts = Doctrine_Query::create()
	->from('TemplateManagerLayouts')
	->where('template_id = ?', $QtemplateId[0]['template_id'])
	->andWhereIn('layout_id', $layoutArr)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$layout_id = $QtemplateLayouts[0]['layout_id'];
$Template->set('templateLayoutId', $layout_id);

function addStyles($El, $Styles) {
	$css = array();
	foreach($Styles as $sInfo){
		if ($sInfo->definition_value == '{}' || $sInfo->definition_value == '[]') continue;

		$El->css($sInfo->definition_key, $sInfo->definition_value);
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

		if ($childObj->Styles->count() > 0){
			$addStyle = true;
			if ($childObj->Configuration['id'] && $childObj->Configuration['id']->configuration_value != ''){
				$addStyle = false;
			}

			if ($addStyle === true){
				addStyles($NewEl, $childObj->Styles);
			}
		}

		if ($childObj->Configuration->count() > 0){
			addInputs($NewEl, $childObj->Configuration);
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

		if ($col->Styles->count() > 0){
			$addStyle = true;
			if ($col->Configuration['id'] && $col->Configuration['id']->configuration_value != ''){
				$addStyle = false;
			}

			if ($addStyle === true){
				addStyles($ColEl, $col->Styles);
			}
		}

		if ($col->Configuration->count() > 0){
			addInputs($ColEl, $col->Configuration);
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


$templateDir = sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir');

$pageContent = new Template('pageContent.tpl', sysConfig::getDirFsCatalog() . 'extensions/templateManager/widgetTemplates/');

$checkFiles = array(
	$templateDir . '/applications/' . $App->getAppName() . '/' . $App->getPageName() . '.php',
	sysConfig::getDirFsCatalog() . '/applications/' . $App->getAppName() . '/pages/' . $App->getPageName() . '.php',
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



$Construct = htmlBase::newElement('div');

$Layout = Doctrine_Core::getTable('TemplateManagerLayouts')->find($layout_id);
if ($Layout->Containers->count() > 0){
	foreach($Layout->Containers as $MainObj){
		if ($MainObj->Parent->container_id > 0) {
			continue;
		}

		$MainEl = htmlBase::newElement('div')
			->addClass('container');

		if ($MainObj->Styles->count() > 0){
			$addStyle = true;
			if ($MainObj->Configuration['id'] && $MainObj->Configuration['id']->configuration_value != ''){
				$addStyle = false;
			}

			if ($addStyle === true){
				addStyles($MainEl, $MainObj->Styles);
			}
		}

		if ($MainObj->Configuration->count() > 0){
			addInputs($MainEl, $MainObj->Configuration);
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