<?php
if (isset($_GET['env'])){
	if ($_GET['env'] == 'admin'){
		chdir('../../../../');
		chdir('admin');
	}else{
		chdir('../../../../');
	}
}else{
	chdir('../../../../');
}
include('includes/application_top.php');

header('Content-Type: text/css');

$sources = array(
	sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/blueprint/screen.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.core.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.theme.css'
);

ob_start();

foreach($sources as $filePath){
	if (file_exists($filePath)){
		echo '/*' . "\n" .
			' * Required File' . "\n" .
			' * Path: ' . $filePath . "\n" .
			' * --BEGIN--' . "\n" .
			' */' . "\n";
		require($filePath);
		echo '/*' . "\n" .
			' * Required File' . "\n" .
			' * Path: ' . $filePath . "\n" .
			' * --END--' . "\n" .
			' */' . "\n";
	}
}

/* Overwrites for the core css framework --BEGIN-- */
?>
body, div, td { font-family: Verdana, Arial, sans-serif;font-size: 12px; }
div {  }
a { text-decoration: none;color: #626262; }
a:hover { text-decoration: underline;color: #626262; }
form { display: inline; }
textarea { width: 100%;font-size: 11px; }

h1 { font-size: 22px;color: #c30;margin: 10px;font-weight: normal; }
h2 { font-size: 18px;color: #c00 }
h3 { font-size: 15px; }
h4 { font-size: 13px;color: #c30;margin: 10px;font-weight: bold; }

.inputRequirement { color: red;}
.main, .main { font-size: .9em;line-height: 1.5em; }
.smallText { font-family: Arial, sans-serif;font-size: .9em; }
.column { margin:0; }
<?php
 /* Overwrites for the core css framework --END-- */

$sources = array(
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.accordion.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.datepicker.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.dialog.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.progressbar.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.resizable.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.slider.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.tabs.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.tooltip.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.autocomplete.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.button.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.stars.css',
	sysConfig::getDirFsCatalog() . 'ext/jQuery/themes/smoothness/ui.menu.css'
);

foreach($sources as $filePath){
	if (file_exists($filePath)){
		echo '/*' . "\n" .
			' * Required File' . "\n" .
			' * Path: ' . $filePath . "\n" .
			' * --BEGIN--' . "\n" .
			' */' . "\n";
		require($filePath);
		echo '/*' . "\n" .
			' * Required File' . "\n" .
			' * Path: ' . $filePath . "\n" .
			' * --END--' . "\n" .
			' */' . "\n";
	}
}
/* Overwrites for jQuery UI Settings --BEGIN-- */
?>
/*
* Border Radius
* top-left top-right bottom-right bottom-left
*/
.ui-corner-all { <?php echo buildBorderRadius('4px', '4px', '4px', '4px'); ?> }
.ui-corner-all-big { <?php echo buildBorderRadius('10px', '10px', '10px', '10px'); ?> }
.ui-corner-all-small { <?php echo buildBorderRadius('2px', '2px', '2px', '2px'); ?> }
.ui-corner-all-medium { <?php echo buildBorderRadius('6px', '6px', '6px', '6px'); ?> }
.ui-corner-all-large { <?php echo buildBorderRadius('10px', '10px', '10px', '10px'); ?> }
.ui-corner-all-xlarge { <?php echo buildBorderRadius('14px', '14px', '14px', '14px'); ?> }
.ui-corner-tl { <?php echo buildBorderRadius('4px', '0px', '0px', '0px'); ?> }
.ui-corner-tr { <?php echo buildBorderRadius('0px', '4px', '0px', '0px'); ?> }
.ui-corner-br { <?php echo buildBorderRadius('0px', '0px', '4px', '0px'); ?> }
.ui-corner-bl { <?php echo buildBorderRadius('0px', '0px', '0px', '4px'); ?> }
.ui-corner-top { <?php echo buildBorderRadius('4px', '4px', '0px', '0px'); ?> }
.ui-corner-bottom { <?php echo buildBorderRadius('0px', '0px', '4px', '4px'); ?> }
.ui-corner-right { <?php echo buildBorderRadius('0px', '4px', '0px', '4px'); ?> }
.ui-corner-left { <?php echo buildBorderRadius('4px', '0px', '4px', '0px'); ?> }

.ui-button {  }
.ui-button.ui-state-default { border-color: #f8ef24; }
.ui-button.ui-state-hover { border-color: #ffffff; }
.ui-button.ui-state-active { border-color: #ffffff; }
.ui-button .ui-button-icon-primary, .ui-button-text-icon .ui-button-icon-primary, .ui-button-text-icons .ui-button-icon-primary, .ui-button-icons-only .ui-button-icon-primary{ left:0.3em; }

.ui-widget-header { border-color: #cccccc;color:#222222;line-height: 1.35em;vertical-align: top; }
.ui-widget-content { background: #ffffff; color: #222222; }
.ui-widget-content a { color: #333333; }
.ui-widget-header .ui-icon { background-image: url(<?php echo jqueryIconsPath('ffffff'); ?>); }
.ui-widget-footer-box { margin-top:.5em; }
.ui-widget-footer-box .ui-button { margin:.5em; }

.ui-icon.ui-icon-closethick { background-image: url(<?php echo jqueryIconsPath('cd0a0a'); ?>); }
<?php
 /* Overwrites for jQuery UI Settinge --END-- */

/* Our core managed css --BEGIN-- */
?>
.errorReport { margin:.5em;padding: 0.7em;border:none;border: 1px solid #000000; }
.errorReport .ui-icon { float: left; margin-right: 0.3em; }
.errorReport .ui-state-error { border-color:#fdcfcf;<?php echo buildSimpleGradient('#fea4a4', '#fc7373'); ?> }
.errorReport .ui-state-warning { border-color:#fceede;<?php echo buildSimpleGradient('#fedfbd', '#fbb86f'); ?> }
.errorReport .ui-state-notice { border-color:#cfddf7;<?php echo buildSimpleGradient('#a4c4fe', '#6499fa'); ?> }

.pageStackContainer { font-family: Verdana, Arial, sans-serif; font-size: .9em; }
.pageStackContainer .ui-widget { margin-bottom: 1em; }

.ui-infobox { background: #dcdcdc;position: relative;margin-bottom: 10px; }
.ui-infobox-header { color: #ffffff;font-weight: bold;font-size: 1em;position:relative;margin:0; padding:0;line-height:1em;}
.ui-infobox-header-text { font-weight:normal;margin:0;margin-left:.5em;padding:0;color:#ffffff;;vertical-align:middle }
.ui-infobox-header-link { float:right;vertical-align:middle;margin-right:.5em; }
.ui-infobox-header-link a { vertical-align:middle; }
.ui-infobox-content { margin: .5em; }
.ui-infobox-header .ui-icon { position:relative; display:inline-block; }
.ui-infobox-content .ui-icon-triangle-1-e { display: inline-block; }

.ui-infobox-header .ui-icon { position:relative; display:inline-block;background-image: url(<?php echo $jqueryThemeIcons; ?>/ui-icons_ffffff_256x240.png); }
.ui-infobox-content .ui-icon-triangle-1-e { display: inline-block; }

.ui-ajax-loader { display: block;text-indent: -99999px;overflow: hidden;background-repeat: no-repeat; }
.ui-ajax-loader-xsmall { width: 10px;height: 10px;background-image: url(<?php echo $jqueryThemeIcons; ?>/ajax_loader_xsmall.gif); }
.ui-ajax-loader-icon { width: 16px;height: 16px;background-image: url(<?php echo $jqueryThemeIcons; ?>/ajax_loader_icon.gif); }
.ui-ajax-loader-small { opacity: 2; width: 20px; height: 20px; background-image: url(<?php echo $jqueryThemeIcons; ?>/ajax_loader_small.gif); }
.ui-ajax-loader-normal { width: 40px; height: 40px; background-image: url(<?php echo $jqueryThemeIcons; ?>/ajax_loader_normal.gif); }
.ui-ajax-loader-large { width: 60px; height: 60px; background-image: url(<?php echo $jqueryThemeIcons; ?>/ajax_loader_large.gif); }
.ui-ajax-loader-xlarge { width: 80px; height: 80px; background-image: url(<?php echo $jqueryThemeIcons; ?>/ajax_loader_xlarge.gif); }

.ui-ajax-loader-back{ background-image: url(<?php echo $templateDir; ?>images/bg_ajax.png);width:375px;height:200px; }
.ui-ajax-loader-dialog{ margin-left:150px; margin-top:60px; }

.moduleRow.ui-state-default, .moduleRow.ui-state-hover, .moduleRow.ui-state-active { color:#ffffff;border-color:#eca09a; }
.moduleRow.ui-state-default { <?php echo buildSimpleGradient('#e4e4e4', '#cccccc'); ?> }
.moduleRow.ui-state-hover { <?php echo buildSimpleGradient('#c4c2c2', '#a7a7a7'); ?> }
.moduleRow.ui-state-active { border-color:#d71a14;background-color:#ffffff; }

.ui-contentbox { width: 100%;position: relative;margin-bottom: 0px; }
.ui-contentbox-header { font-weight: bold;font-size: 1em; }
.ui-contentbox-header-text { font-family:Arial; font-size:18px; color:#000000;background-image:url(<?php echo $templateDir; ?>images/icon_widget2.png);background-repeat:no-repeat;padding-right:15px;margin-left:10px;height:39px; line-height:33px; background-position:right center;}
.ui-contentbox-content { position:relative;background-color:#fcf9f9; border: 1px solid #e2e0de; padding-left:10px; }
.ui-contentbox-content h1{ margin:0;margin-bottom:40px;color:#ffffff; }
.ui-contentbox-content h3{ margin:0; }
.ui-contentbox .ui-widget-content { border:none; }
.ui-contentbox .moreInfo { margin-left:.5em; }

.productListing-heading { height: 34px;font-weight: normal;font-family: Arial;font-size: 14px;color: #ffffff;background: url(<?php echo $templateDir; ?>images/infobox_header.png) repeat-x top left; }
a.productListing-heading, a.productListing-heading:hover { color: #ffffff;background: none;height: 15px; }
.productListingColBoxContainer{ width:110px;float:left;margin-left:15px;margin-top:15px;background-color:#f2f2f2;border:1px solid #cccccc;padding-bottom:4px; }

.productListingColBoxContainer { text-align:center;margin:.5em; }
.productListingColBoxContent_image{ padding-top:4px; }

.productListingRowContainer { padding: .5em; }
.productListingRowContents { }
.productListingRow-even { background: #e8e8e8; }
.productListingRow-odd { background: transparent; }
.productListingRowPager { color:#ffffff;background: #313131;font-size: .8em;position:relative; }
.productListingRowPager a { color:#ffffff; }
.productListingRowPagerLink { font-size:1em;padding: .4em .6em;background-color:#cccccc; }
a.productListingRowPagerLink:hover { text-decoration:none; }
.productListingRowPagerLinkActive { font-size:1em;padding: .4em .6em;font-weight: bold; }

.pageHeaderContainer { line-height: 2em;vertical-align:middle;margin:.2em; }
.pageHeaderContainer .pageHeaderText { vertical-align:middle; }
.pageHeaderContainer .ui-icon { margin-left:.3em;display:inline-block;vertical-align:middle; }
.pageContent { margin:.2em; }
.pageButtonBar { text-align:right; padding:.3em; margin-top:.5em; }

<?php
 /*
	 * @TODO: Move to pay per rentals infobox buildStylesheet function
	 */
if ($appExtension->isInstalled('payPerRentals')){
	?>
.ui-datepicker-reserved { background: #FF0000; }
.ui-datepicker-reserved span.ui-state-default { background: #FF0000; }

<?php

}

/*
	 * @TODO: Move to blog infobox buildStylesheet function
	 */
if ($appExtension->isInstalled('blog')){
	?>
#blogcategoriesModuleMenu h3{ margin:0;padding:0; }
#blogcategoriesModuleMenu ul{ list-style-type:none; }
#blogcategoriesModuleMenu ul li{ margin:0;padding:0;background:transparent; }
#blogarchivesModuleMenu ul{ list-style-type:none; margin:0; padding:0; }
#blogarchivesModuleMenu ul li{ margin:0;padding:0;background:transparent; }
.blogInfoboxLink{ border-color:transparent; }
.blogInfoboxLink:hover{ text-decoration:none !important; }
.comf, .captcha_img{ display:block;margin-bottom:10px; }
#cke_comment_text{ width:80%; }
<?php

}
/* Our core managed css --END-- */

if (isset($_GET['import']) && !empty($_GET['import'])){
	foreach(explode(',', $_GET['import']) as $filePath){
		if (substr($filePath, -4) != '.css') {
			continue;
		}

		$requireFile = false;
		if (file_exists($filePath)){
			$requireFile = $filePath;
		}
		elseif (file_exists(sysConfig::get('DIR_FS_DOCUMENT_ROOT') . $filePath)) {
			$requireFile = sysConfig::get('DIR_FS_DOCUMENT_ROOT') . $filePath;
		}
		elseif (file_exists(sysConfig::getDirFsCatalog() . $filePath)) {
			$requireFile = sysConfig::getDirFsCatalog() . $filePath;
		}
		elseif (file_exists(sysConfig::getDirFsAdmin() . $filePath)) {
			$requireFile = sysConfig::getDirFsAdmin() . $filePath;
		}

		if ($requireFile !== false){
			echo '/*' . "\n" .
				' * Imported File' . "\n" .
				' * Path: ' . $requireFile . "\n" .
				' * --BEGIN--' . "\n" .
				' */' . "\n";
			require($requireFile);
			echo '/*' . "\n" .
				' * Imported File' . "\n" .
				' * Path: ' . $requireFile . "\n" .
				' * --END--' . "\n" .
				' */' . "\n";
		}
	}
}

if (isset($_GET['layout_id'])){
	$boxStylesEntered = array();
	$addCss = '';
	function parseContainer($Container) {
		global $boxStylesEntered, $addCss;

		if ($Container->Configuration['id'] && $Container->Configuration['id']->configuration_value != ''){
			$Style = new StyleBuilder();
			$Style->setSelector('#' . $Container->Configuration['id']->configuration_value);
			foreach($Container->Styles as $sInfo){
				$Style->addRule($sInfo->definition_key, $sInfo->definition_value);
			}
			$addCss .= $Style->outputCss();
		}

		if ($Container->Children->count() > 0){
			foreach($Container->Children as $ChildObj){
				parseContainer($ChildObj);
			}
		}
		else {
			foreach($Container->Columns as $colInfo){
				if ($colInfo->Configuration['id'] && $colInfo->Configuration['id']->configuration_value != ''){
					$Style = new StyleBuilder();
					$Style->setSelector('#' . $colInfo->Configuration['id']->configuration_value);
					foreach($colInfo->Styles as $sInfo){
						$Style->addRule($sInfo->definition_key, $sInfo->definition_value);
					}
					$addCss .= $Style->outputCss();
				}

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
					if (method_exists($className, 'buildStylesheet')){
						if ($Box->buildStylesheetMultiple === true || !in_array($className, $boxStylesEntered)){
							if (isset($WidgetSettings->id) && !empty($WidgetSettings->id)){
								$Box->setBoxId($WidgetSettings->id);
							}
							$Box->setWidgetProperties($WidgetSettings);

							echo $Box->buildStylesheet();

							$boxStylesEntered[] = $className;
						}
					}
				}
			}
		}
	}

	$Layout = Doctrine_Core::getTable('TemplateManagerLayouts')->find((int)$_GET['layout_id']);
	if ($Layout){
		echo '/*' . "\n" .
			' * Layout Manager Generated Styles' . "\n" .
			' * --BEGIN--' . "\n" .
			' */' . "\n";
		$Template = $Layout->Template;

		if ($Layout->Styles->count() > 0){
			$StyleBuilder = new StyleBuilder();
			$StyleBuilder->setSelector('body');
			$rules = array();
			foreach($Layout->Styles as $sInfo){
				$StyleBuilder->addRule($sInfo->definition_key, $sInfo->definition_value);
			}
			echo $StyleBuilder->outputCss();
		}
		
		foreach($Layout->Containers as $Container){
			parseContainer($Container);
		}

		echo $addCss;
		echo '/*' . "\n" .
			' * Layout Manager Generated Styles' . "\n" .
			' * --END--' . "\n" .
			' */' . "\n";
	}

	echo '/*' . "\n" .
		' * Template Stylesheet' . "\n" .
		' * Path: ' . sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/stylesheet.css' . "\n" .
		' * --BEGIN--' . "\n" .
		' */' . "\n";
	require(sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/stylesheet.css');
	echo '/*' . "\n" .
		' * Template Stylesheet' . "\n" .
		' * Path: ' . sysConfig::getDirFsCatalog() . 'templates/' . Session::get('tplDir') . '/stylesheet.css' . "\n" .
		' * --END--' . "\n" .
		' */' . "\n";
}
else {
	echo '/*' . "\n" .
		' * Template Stylesheet' . "\n" .
		' * Path: ' . sysConfig::getDirFsAdmin() . 'template/fallback/stylesheet.css' . "\n" .
		' * --BEGIN--' . "\n" .
		' */' . "\n";
	require(sysConfig::getDirFsAdmin() . 'template/fallback/stylesheet.css');
	echo '/*' . "\n" .
		' * Template Stylesheet' . "\n" .
		' * Path: ' . sysConfig::getDirFsAdmin() . 'template/fallback/stylesheet.css' . "\n" .
		' * --END--' . "\n" .
		' */' . "\n";
}

$fileContent = ob_get_contents();
ob_end_clean();

function src1_fetch() {
	global $fileContent;
	return $fileContent;
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
$sources = new Minify_Source(array(
	'id' => 'source1',
	'getContentFunc' => 'src1_fetch',
	'contentType' => Minify::TYPE_CSS,
	'lastModified' => ($_SERVER['REQUEST_TIME'] - $_SERVER['REQUEST_TIME'] % 86400)
));

// handle request
$serveArr = array(
	'files' => $sources,
	'maxAge' => 86400,
	//'quiet' => true,
	'debug' => true
);

if (isset($Template) && is_object($Template)){
	switch($Template->Configuration['STYLESHEET_COMPRESSION']->configuration_value){
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
/*
$templateDir = Session::get('tplDir');
$layoutId = $_GET['layout_id'];
$import = '';
if (isset($_GET['import']) && !empty($_GET['import'])){
	$import = $_GET['import'];
}
$cacheKey = 'stylesheet_' . $templateDir . '_' . $layoutId . '_' . $import;

$Result = Minify::serve('Files', $serveArr);
FileCache::save($cacheKey, $Result['content']);
FileCache::serve($cacheKey);
*/
Minify::serve('Files', $serveArr);
include('includes/application_bottom.php');
?>