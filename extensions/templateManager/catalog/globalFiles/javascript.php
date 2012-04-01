<?php
	chdir('../../../../');
if (isset($_GET['layout_id'])){
	$env = 'catalog';
	$layoutId = $_GET['layout_id'];
	$templateDir = isset($_GET['tplDir'])?$_GET['tplDir']:'';
}
else {
	$env = 'admin';
	$layoutId = '9999';
	$templateDir = 'administration';
}
$import = '';
if (isset($_GET['import']) && !empty($_GET['import'])){
	$import = $_GET['import'];
}
$cacheKey = $env . '-javascript-' . $templateDir . '-' . md5($layoutId . '-' . $import);

require('includes/classes/system_cache.php');
$JavascriptCache = new SystemCache($cacheKey, 'cache/' . $env . '/javascript/');
if ($JavascriptCache->loadData() === true && !isset($_GET['noCache'])){
	$JavascriptCache->output(false, true);
	exit;
}
else {
	include('includes/application_top.php');

	if ($env == 'catalog'){
		ob_start();
		?>
	$(document).ready(function (){
	$('.starRating').stars({
	split: 2,
	cancelShow: false,
	disabled: <?php echo ($userAccount->isLoggedIn() === true ? 'false' : 'true'); ?>,
	callback: function (ui, type, value){
	$.ajax({
	cache: false,
	url: js_href_link(thisFile),
	data: 'rType=ajax&action=rateProduct&pID=' + $(ui.element).attr('products_id') + '&rating=' + (value/2),
	dataType: 'json',
	success: function (data){
	if (data.success == false){
	alert('Vote was not placed.');
	}
	}
	});
	}
	});

	$('.checkoutFormButton').button();

	$('.ui-button').each(function (){
	var disable = false;
	if ($(this).hasClass('ui-state-disabled')){
	disable = true;
	}
	$(this).button({
	disabled: disable
	}).click(function (e){
	if ($(this).hasClass('ui-state-disabled')){
	e.preventDefault();
	return false;
	}
	});
	});
	<?php
		$TemplateManager = $appExtension->getExtension('templateManager');
		$TemplateManager->loadWidgets($templateDir);
		$boxJavascriptsEntered = array();
		$boxJavascriptSourcesEntered = array();
		$infoBoxSources = array();
		function parseContainer($Container) {
			global $TemplateManager, $boxJavascriptsEntered, $boxJavascriptSourcesEntered, $infoBoxSources;

			if (isset($Container['widget_id'])){
				$typeId = $Container['widget_id'];
				$type = 'widget';
			}
			elseif (isset($Container['column_id'])) {
				$typeId = $Container['column_id'];
				$type = 'column';
			}
			elseif (isset($Container['container_id'])) {
				$typeId = $Container['container_id'];
				$type = 'container';
			}

			if ($type == 'container' && (($Containers = $TemplateManager->getContainerChildren($typeId)) !== false)){
				foreach($Containers as $ChildObj){
					parseContainer($ChildObj);
				}
			}
			elseif ($type == 'container' && (($Columns = $TemplateManager->getContainerColumns($typeId)) !== false)) {
				foreach($Columns as $ChildObj){
					parseContainer($ChildObj);
				}
			}
			elseif ($type == 'column' && (($Columns = $TemplateManager->getColumnChildren($typeId)) !== false)) {
				foreach($Columns as $ChildObj){
					parseContainer($ChildObj);
				}
			}
			elseif ($type == 'column' && (($Widgets = $TemplateManager->getColumnWidgets($typeId)) !== false)) {
				foreach($Widgets as $wInfo){
					parseContainer($wInfo);
				}
			}
			elseif ($type == 'widget') {
				if (($Configuration = $TemplateManager->getConfigInfo($type, $typeId)) !== false){
					foreach($Configuration as $config){
						if ($config['configuration_key'] == 'widget_settings'){
							$WidgetSettings = json_decode($config['configuration_value']);
							break;
						}
					}

					$WidgetClass = $TemplateManager->getWidget($Container['identifier']);
					if ($WidgetClass !== false){
						if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
							$WidgetClass->setBoxId($WidgetSettings->id);
						}
						$WidgetClass->setWidgetProperties($WidgetSettings);
						if (method_exists($WidgetClass, 'buildJavascript')){
							if ($WidgetClass->buildJavascriptMultiple === true || !in_array($WidgetClass->getBoxCode(), $boxJavascriptsEntered)){
								echo $WidgetClass->buildJavascript();

								$boxJavascriptsEntered[] = $WidgetClass->getBoxCode();
							}
						}
						if (method_exists($WidgetClass, 'getJavascriptSources')){
							if (!in_array($WidgetClass->getBoxCode(), $boxJavascriptSourcesEntered)){
								$infoBoxJsFiles = $WidgetClass->getJavascriptSources();
								foreach($infoBoxJsFiles as $infoBoxJsFile){
									if (file_exists($infoBoxJsFile)){
										$infoBoxSources[] = $infoBoxJsFile;
									}
								}

								$boxJavascriptSourcesEntered[] = $WidgetClass->getBoxCode();
							}
						}
					}
				}
			}
		}

		$Layout = Doctrine_Manager::getInstance()
			->getCurrentConnection()
			->fetchAssoc('select * from template_manager_layouts where layout_id = "' . (int)$_GET['layout_id'] . '"');
		if ($Layout){
			echo '/*' . "\n" .
				' * Layout Manager Generated Javascript' . "\n" .
				' * --BEGIN--' . "\n" .
				' */' . "\n";
			$Containers = Doctrine_Manager::getInstance()
				->getCurrentConnection()
				->fetchAssoc('select * from template_manager_layouts_containers where layout_id = "' . $Layout[0]['layout_id'] . '" and parent_id = 0 order by sort_order');
			if (sizeof($Containers) > 0){
				foreach($Containers as $cInfo){
					if ($cInfo['link_id'] > 0){
						$Link = Doctrine_Manager::getInstance()
							->getCurrentConnection()
							->fetchAssoc('select c.* from template_manager_container_links l left join template_manager_layouts_containers c using(container_id) where l.link_id = "' . $cInfo['link_id'] . '"');
						parseContainer($Link[0]);
					}
					else {
						parseContainer($cInfo);
					}
				}
			}
			echo '/*' . "\n" .
				' * Layout Manager Generated Javascript' . "\n" .
				' * --END--' . "\n" .
				' */' . "\n";
		}
		?>
	});
	<?php
		echo file_get_contents(sysConfig::getDirFsCatalog() . 'ext/jQuery/external/reflection/reflection.js');
		$fileContent = ob_get_contents();
		ob_end_clean();
	}

	function src1_fetch() {
		global $fileContent, $env;
		if ($env == 'catalog'){
			return $fileContent;
		}
		return '';
	}

	// setup sources
	$sources = array(
		sysConfig::getDirFsCatalog() . 'ext/jQuery/jQuery-min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.core.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.widget.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.effects.core.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.mouse.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.position.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.draggable.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.droppable.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.sortable.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.resizable.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.button.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.dialog.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.datepicker.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.accordion.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.stars.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.progressbar.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.newGrid.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.effects.fade.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/external/virtualKeyboard/jquery.keyboard.js'
	);

	if ($env == 'admin'){
		$sources[] = sysConfig::getDirFsAdmin() . 'includes/javascript/main.js';
		$sources[] = sysConfig::getDirFsAdmin() . 'includes/general.js';
	}
	else {
		$sources[] = sysConfig::getDirFsCatalog() . 'includes/javascript/general.js';
		if (count($infoBoxSources)){
			$sources = array_merge($sources, $infoBoxSources);
		}
	}

	if (isset($_GET['import']) && !empty($_GET['import'])){
		foreach(explode(',', $_GET['import']) as $filePath){
			if (substr($filePath, -3) != '.js'){
				continue;
			}

			if (file_exists($filePath)){
				$sources[] = $filePath;
			}
			elseif (file_exists(sysConfig::get('DIR_FS_DOCUMENT_ROOT') . $filePath)) {
				$sources[] = sysConfig::get('DIR_FS_DOCUMENT_ROOT') . $filePath;
			}
			elseif (file_exists(sysConfig::getDirFsCatalog() . $filePath)) {
				$sources[] = sysConfig::getDirFsCatalog() . $filePath;
			}
			elseif (file_exists(sysConfig::getDirFsAdmin() . $filePath)) {
				$sources[] = sysConfig::getDirFsAdmin() . $filePath;
			}
		}
	}

	if (file_exists(sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/i18n/' . Session::get('languages_code') . '.js')){
		$sources[] = sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/i18n/' . Session::get('languages_code') . '.js';
	}

	//include('includes/classes/minifier/JSMinPlus.php');
	include('includes/classes/minifier/JSMin.php');

	$minified = '';

	foreach($sources as $source){
		//$minified .= JSMinPlus::minify($source);
		$minified .= JSMin::minify(file_get_contents($source)).';';

		//$minified .= file_get_contents($source);
	}
	//$minified .= src1_fetch();
	$minified .= JSMin::minify(src1_fetch());

	//$JavascriptCache->setAddedHeaders('Content-Type: application/x-javascript');
	$JavascriptCache->setContentType('text/javascript');
	$JavascriptCache->setContent($minified);
	$JavascriptCache->setExpires(time() + (60 * 60 * 24 * 2));
	$JavascriptCache->setLastModified(gmdate("D, d M Y H:i:s"));
	$JavascriptCache->store();

	$JavascriptCache->output(false, true);

	include('includes/application_bottom.php');
}
