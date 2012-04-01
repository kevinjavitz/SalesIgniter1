<?php
	chdir('../../../../');

	if (isset($_GET['env'])){
		$env = $_GET['env'];
		$layoutId = (isset($_GET['layout_id']) ? $_GET['layout_id'] : '9999');
		$templateDir = $_GET['tplDir'];
	}
	elseif (isset($_GET['layout_id'])){
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
	$cacheKey = $env . '-javascript-mobile-' . $templateDir . '-' . md5($layoutId . '-' . $import);

	require('includes/classes/system_cache.php');
	$JavascriptCache = new SystemCache($cacheKey);
if ($JavascriptCache->loadData() === true && !isset($_GET['noCache'])){
	$JavascriptCache->output(false, true);
	exit;
}
else {
	include('includes/application_top.php');

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

	function jqueryMobileConfig(){
		ob_start();
		?>
	$(document).bind("mobileinit", function(){
	$.mobile.ajaxEnabled = false;
	});
		<?php
		$Script = ob_get_contents();
		ob_end_clean();
		return $Script;
	}
	// setup sources
	$sources = array();
	$sources[] = sysConfig::getDirFsCatalog() . 'ext/jQuery/jQuery-min.js';
	$sources[] = new Minify_Source(array(
		'id' => 'jQueryMobileConfig',
		'getContentFunc' => 'jqueryMobileConfig',
		'contentType' => Minify::TYPE_JS,
		'lastModified' => time()
	));
	$sources[] = sysConfig::getDirFsCatalog() . 'ext/jQuery/jQuery-mobile.js';
	$sources[] = sysConfig::getDirFsCatalog() . 'includes/javascript/functions.js';
	//$sources[] = sysConfig::getDirFsCatalog() . 'includes/javascript/general.js';

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

	/*$sources[] = new Minify_Source(array(
		'id' => 'source1',
		'getContentFunc' => 'src1_fetch',
		'contentType' => Minify::TYPE_JS,
		'lastModified' => time()
	));*/

	// handle request
	$serveArr = array(
		'files' => $sources,
		'maxAge' => 86400,
		'debug' => true,
		'quiet' => false
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
