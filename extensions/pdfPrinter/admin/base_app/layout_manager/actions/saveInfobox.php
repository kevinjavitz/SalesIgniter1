<?php
$Qwidget = Doctrine_Query::create()
	->from('PDFTemplatesInfoboxes')
	->where('box_code = ?', $_GET['widgetCode'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$WidgetCode = $Qwidget[0]['box_code'];
$WidgetPath = sysConfig::getDirFsCatalog() . $Qwidget[0]['box_path'];
$WidgetProperties = array(
	'id' => $_POST['id'],
	'template_file' => $_POST['template_file'],
	'widget_title' => $_POST['widget_title']
);

$WidgetPreview = false;
if (file_exists($WidgetPath . 'actions/saveInfobox.php')){
	require($WidgetPath . 'actions/saveInfobox.php');

	$className = 'PDFInfoBox' . ucfirst($WidgetCode);
	if (!class_exists($className)){
		require($WidgetPath . 'pdfinfobox.php');
	}

	$Class = new $className;
	if (method_exists($Class, 'showLayoutPreview')){
		$WidgetPreview = $Class->showLayoutPreview(json_decode(json_encode($WidgetProperties)));
	}
}

EventManager::attachActionResponse(array(
	'success' => true,
	'widgetSettings' => $WidgetProperties,
	'widgetPreview' => $WidgetPreview
), 'json');
?>