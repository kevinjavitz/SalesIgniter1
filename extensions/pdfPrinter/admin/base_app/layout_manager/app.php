<?php
if (!class_exists('phpQuery')){
	require(sysConfig::getDirFsCatalog() . '/includes/classes/html/dom/phpQuery.php');
}

if (!class_exists('CurlRequest')){
	require(sysConfig::getDirFsCatalog() . '/includes/classes/curl/Request.php');
}

if (!class_exists('CurlResponse')){
	require(sysConfig::getDirFsCatalog() . '/includes/classes/curl/Response.php');
}

if (!class_exists('CurlDownload')){
	require(sysConfig::getDirFsCatalog() . '/includes/classes/curl/Download.php');
}

$appContent = $App->getAppContentFile();
$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');

if ($App->getPageName() == 'editLayout'){
	$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.highlight.js');
	$App->addJavascriptFile('ext/jQuery/external/stickyBar/jquery.stickyBar.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.slider.js');
	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.tabs.js');
	$App->addJavascriptFile('extensions/pdfPrinter/admin/base_app/layout_manager/javascript/construct.js');
	$App->addJavascriptFile('extensions/pdfPrinter/admin/base_app/layout_manager/javascript/backgroundBuilder.js');

	$Imports = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/pdfPrinter/admin/base_app/layout_manager/javascript/background/');
	foreach($Imports as $fInfo){
		if ($fInfo->isDot() === true || $fInfo->isDir() === true) continue;

		$App->addJavascriptFile(str_replace(sysConfig::getDirFsCatalog(), '', $fInfo->getPathName()));

		$dirName = $fInfo->getBasename('.js');
		if (is_dir($fInfo->getPath() . '/' . $dirName)){
			$subImport = new DirectoryIterator($fInfo->getPath() . '/' . $dirName);
			foreach($subImport as $sfInfo){
				if ($sfInfo->isDot() === true || $sfInfo->isDir() === true) continue;

				$App->addJavascriptFile(str_replace(sysConfig::getDirFsCatalog(), '', $sfInfo->getPathName()));
			}
		}
	}

	$Imports = new DirectoryIterator(sysConfig::getDirFsCatalog() . 'extensions/pdfPrinter/admin/base_app/layout_manager/javascript/tabs/');
	foreach($Imports as $fInfo){
		if ($fInfo->isDot() === true || $fInfo->isDir() === true) continue;

		$App->addJavascriptFile(str_replace(sysConfig::getDirFsCatalog(), '', $fInfo->getPathName()));
	}

	$App->addJavascriptFile('extensions/pdfPrinter/admin/base_app/layout_manager/javascript/construct-parser.js');
	$App->addStylesheetFile('extensions/pdfPrinter/admin/base_app/layout_manager/javascript/construct.css');

	$App->addJavascriptFile('ext/jQuery/external/colorPicker/jquery.colorpicker.js');
	$App->addStylesheetFile('ext/jQuery/external/colorPicker/jquery.colorpicker.css');
}
?>