<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

Doctrine_Core::loadAllModels();
$appContent = $App->getAppContentFile();

if ($App->getPageName() == 'defines'){
	$App->addJavascriptFile('admin/rental_wysiwyg/ckeditor.js');
}

if (sysConfig::exists('GOOGLE_API_SERVER_KEY') && sysConfig::get('GOOGLE_API_SERVER_KEY') != ''){
	$googleLanguages = sysLanguage::getGoogleLanguages();
}
?>