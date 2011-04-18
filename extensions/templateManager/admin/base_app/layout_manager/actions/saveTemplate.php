<?php
$Template = Doctrine_Core::getTable('TemplateManagerTemplates')->find($_GET['tID']);

$Template->Configuration['NAME']->configuration_value = $_POST['templateName'];
$Template->Configuration['STYLESHEET_COMPRESSION']->configuration_value = $_POST['stylesheet_compression'];
$Template->Configuration['JAVASCRIPT_COMPRESSION']->configuration_value = $_POST['javascript_compression'];

$Template->save();

EventManager::attachActionResponse(array(
	'success' => true
), 'json');
?>