<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$appContent = $App->getAppContentFile();

if(sysConfig::get('EXTENSION_PHOTO_GALLERY_DISPLAY_TYPE') == 'Slideshow'){
	$App->addJavascriptFile('ext/jQuery/external/nivoSlider/jquery.nivo.slider.js');
	$App->addStylesheetFile('ext/jQuery/external/nivoSlider/themes/default/default.css');
	$App->addStylesheetFile('ext/jQuery/external/nivoSlider/nivo-slider.css');
}else{
	$App->addJavascriptFile('ext/jQuery/external/jquery.fancybox-2.0.3/jquery.fancybox.js');
	$App->addStylesheetFile('ext/jQuery/external/jquery.fancybox-2.0.3/jquery.fancybox.css');
}
?>