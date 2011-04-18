<?php
/*
	Product Specials Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$appContent = $App->getAppContentFile();
	
	$breadcrumb->add(sysLanguage::get('NAVBAR_TITLE'), itw_app_link('appExt=specials', 'show_specials', 'defualt'));
?>