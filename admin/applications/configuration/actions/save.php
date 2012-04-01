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

$Configuration = Doctrine_Core::getTable('Configuration')->findAll();
$ConfigGroup = new MainConfigReader($_GET['key']);
foreach($_POST['configuration'] as $k => $v){
	$Configuration[$k]->configuration_group_key = $ConfigGroup->getKey();
	$Configuration[$k]->configuration_key = $k;
	if (is_array($v)){
		$Config = $ConfigGroup->getConfig($k);
		$Configuration[$k]->configuration_value = implode($Config->getGlue(), $v);
	}else{
		$Configuration[$k]->configuration_value = $v;
	}
}
$Configuration->save();
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>