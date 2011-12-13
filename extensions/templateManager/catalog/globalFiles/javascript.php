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
$JavascriptCache = new SystemCache($cacheKey);
if ($JavascriptCache->loadData() === true && !isset($_GET['noCache'])){
	$JavascriptCache->output(false, true);
	exit;
}
else {
	include('includes/application_top.php');

	ob_start();
	if ($env == 'catalog'){
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
		$boxJavascriptsEntered = array();
		$boxJavascriptSourcesEntered = array();
		$infoBoxSrouces = array();
		function parseContainer($Container) {
			global $boxJavascriptsEntered, $boxJavascriptSourcesEntered, $infoBoxSrouces;
			if ($Container->Children->count() > 0){
				foreach($Container->Children as $ChildObj){
					parseContainer($ChildObj);
				}
			}
			else {
				foreach($Container->Columns as $colInfo){
					foreach($colInfo->Widgets as $wInfo){
						foreach($wInfo->Configuration as $config){
							if ($config->configuration_key == 'widget_settings'){
								$WidgetSettings = json_decode($config->configuration_value);
								break;
							}
						}
						$className = 'InfoBox' . ucfirst($wInfo->identifier);
						if (!class_exists($className)){
							$Qbox = Doctrine_Query::create()
								->select('box_path')
								->from('TemplatesInfoboxes')
								->where('box_code = ?', $wInfo->identifier)
								->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

							require($Qbox[0]['box_path'] . 'infobox.php');
						}

						$Box = new $className();
						if (method_exists($className, 'buildJavascript')){
							if ($Box->buildJavascriptMultiple === true || !in_array($className, $boxJavascriptsEntered)){
								if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
									$Box->setBoxId($WidgetSettings->id);
								}
								$Box->setWidgetProperties($WidgetSettings);

								echo $Box->buildJavascript();

								$boxJavascriptsEntered[] = $className;
							}
						}
						if (method_exists($className, 'getJavascriptSources')){
							if (!in_array($className, $boxJavascriptSourcesEntered)){
								if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
									$Box->setBoxId($WidgetSettings->id);
								}
								$Box->setWidgetProperties($WidgetSettings);

								$infoBoxJsFiles = $Box->getJavascriptSources();
								foreach($infoBoxJsFiles as $infoBoxJsFile){
									if (file_exists($infoBoxJsFile)){
										$infoBoxSrouces[] = $infoBoxJsFile;
									}
								}

								$boxJavascriptSourcesEntered[] = $className;
							}
						}
					}
				}
			}
		}

		$Layout = Doctrine_Core::getTable('TemplateManagerLayouts')->find((int)$_GET['layout_id']);
		if ($Layout){
			$Template = $Layout->Template;
			foreach($Layout->Containers as $Container){
				parseContainer($Container);
			}
		}
		?>
	});
	<?php
	 echo file_get_contents(sysConfig::getDirFsCatalog() . 'ext/jQuery/external/reflection/reflection.js');
	}
	$fileContent = ob_get_contents();
	ob_end_clean();

	function src1_fetch() {
		global $fileContent, $env;
		if ($env == 'catalog'){
			return $fileContent;
		}
	}

	define('MINIFY_MIN_DIR', sysConfig::getDirFsCatalog() . 'min');

	/*
		 * This script implements a Minify server for a single set of sources.
		 * If you don't want '.php' in the URL, use mod_rewrite...
		 */

	// setup Minify
	set_include_path(MINIFY_MIN_DIR . '/lib' . PATH_SEPARATOR . get_include_path());
	require 'Minify.php';
	require 'Minify/Cache/File.php';
	Minify::setCache(new Minify_Cache_File()); // guesses a temp directory

	// setup sources
	$sources = array(
		sysConfig::getDirFsCatalog() . 'ext/jQuery/jQuery-min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.core.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.widget.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.mouse.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.position.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.draggable.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.droppable.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.sortable.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/minified/jquery.ui.resizable.min.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.button.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.dialog.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.datepicker.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.accordion.js',
		sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.stars.js'
	);

	if ($env == 'admin'){
		$sources[] = sysConfig::getDirFsAdmin() . 'includes/javascript/main.js';
		$sources[] = sysConfig::getDirFsAdmin() . 'includes/general.js';
	}
	else {
		$sources[] = sysConfig::getDirFsCatalog() . 'includes/javascript/general.js';
		if(count($infoBoxSrouces)){
			$sources = array_merge($sources,$infoBoxSrouces);
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

	$sources[] = new Minify_Source(array(
			'id' => 'source1',
			'getContentFunc' => 'src1_fetch',
			'contentType' => Minify::TYPE_JS,
			'lastModified' => time()
		));

	// handle request
	$serveArr = array(
		'files' => $sources,
		'maxAge' => 86400,
		'debug' => true,
		'quiet' => true
	);

	if (isset($Template) && is_object($Template)){
		switch($Template->Configuration['JAVASCRIPT_COMPRESSION']->configuration_value){
			case 'gzip':
				//ob_start("ob_gzhandler");
				break;
			case 'min':
				$serveArr['debug'] = false;
				break;
			case 'min_gzip':
				//ob_start("ob_gzhandler");
				$serveArr['debug'] = false;
				break;
		}
	}
	$Result = Minify::serve('Files', $serveArr);

	$JavascriptCache->setAddedHeaders($Result['headers']);
	//$JavascriptCache->setContentType('text/javascript');
	$JavascriptCache->setContent($Result['content']);
	$JavascriptCache->setExpires(time() + (60 * 60 * 24 * 2));
	$JavascriptCache->setLastModified($Result['headers']['Last-Modified']);
	$JavascriptCache->store();

	$JavascriptCache->output(false, true);

	include('includes/application_bottom.php');
}
