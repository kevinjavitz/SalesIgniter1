<?php
$Configuration = Doctrine_Core::getTable('Configuration')
	->findByConfigurationGroupKey($_GET['extension']);

$info = simplexml_load_file(sysConfig::getDirFsCatalog() . 'extensions/' . $_GET['extension'] . '/'. 'data/info.xml', 'SimpleXMLElement', LIBXML_NOCDATA);

$ExtConfig = new ExtensionConfigReader($_GET['extension']);

$Configuration[(string)$info->installed_key]->configuration_group_key = $ExtConfig->getKey();
$Configuration[(string)$info->installed_key]->configuration_key = (string)$info->installed_key;
$Configuration[(string)$info->installed_key]->configuration_value = 'False';

$Configuration[(string)$info->status_key]->configuration_group_key = $ExtConfig->getKey();
$Configuration[(string)$info->status_key]->configuration_key = (string)$info->status_key;
$Configuration[(string)$info->status_key]->configuration_value = 'False';

$Configuration->save();

EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>