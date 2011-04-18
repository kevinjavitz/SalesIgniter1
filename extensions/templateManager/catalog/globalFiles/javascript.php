<?php
	chdir('../../../../');
include('includes/application_top.php');

header('Content-Type: text/javascript');

ob_start();
if (isset($_GET['layout_id'])){
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
	function parseContainer($Container) {
		global $boxJavascriptsEntered;
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
	global $fileContent;
	if (isset($_GET['layout_id'])){
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
	sysConfig::getDirFsCatalog() . 'ext/jQuery/jQuery.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.core.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.widget.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.mouse.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.position.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.draggable.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.droppable.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.sortable.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.resizable.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.button.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.dialog.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.accordion.js',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/ui/jquery.ui.stars.js'
);

if (!isset($_GET['layout_id'])){
	$sources[] = sysConfig::getDirFsAdmin() . 'includes/javascript/main.js';
	$sources[] = sysConfig::getDirFsAdmin() . 'includes/general.js';
}
else {
	$sources[] = sysConfig::getDirFsCatalog() . 'includes/javascript/general.js';
}

if (isset($_GET['import']) && !empty($_GET['import'])){
	foreach(explode(',', $_GET['import']) as $filePath){
		if (substr($filePath, -3) != '.js') {
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
	'contentType' => Minify::TYPE_CSS,
	'lastModified' => ($_SERVER['REQUEST_TIME'] - $_SERVER['REQUEST_TIME'] % 86400)
));

// handle request
$serveArr = array(
	'files' => $sources,
	'maxAge' => 86400,
	'debug' => true
);

if (isset($Template) && is_object($Template)){
	switch ($Template->Configuration['JAVASCRIPT_COMPRESSION']->configuration_value){
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
/*if (isset($_GET['layout_id'])){
	$templateDir = Session::get('tplDir');
	$layoutId = $_GET['layout_id'];
	$import = '';
	if (isset($_GET['import']) && !empty($_GET['import'])){
		$import = $_GET['import'];
	}
	$cacheKey = 'javascript_' . $templateDir . '_' . $layoutId . '_' . $import;

	$serveArr['quiet'] = true;
	$Result = Minify::serve('Files', $serveArr);
	FileCache::save($cacheKey, $Result['content']);
	FileCache::serve($cacheKey);
}else{*/
	Minify::serve('Files', $serveArr);
//}

include('includes/application_bottom.php');
?>