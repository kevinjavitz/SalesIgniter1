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

$toTranslate = array();
if (is_array($_POST['text'])){
	foreach($_POST['text'] as $k => $v){
		$toTranslate[$k] = $v;
	}
}else{
	$toTranslate[] = $_POST['text'];
}

$Translated = sysLanguage::translateText($toTranslate, $_POST['fromLang'], $_POST['toLang']);

EventManager::attachActionResponse(array(
	'success' => true,
	'translated' => $Translated
), 'json');
