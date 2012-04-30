<?php
$Configuration = Doctrine_Core::getTable('Configuration')
	->findByConfigurationGroupKey($_GET['extension']);

$ExtConfig = new ExtensionConfigReader($_GET['extension']);
foreach($_POST['configuration'] as $k => $v){
	$Configuration[$k]->configuration_group_key = $ExtConfig->getKey();
	$Configuration[$k]->configuration_key = $k;
	if(isset($v[0]) && $v[0] != 'NoneCheckbox'){
		if (is_array($v)){
			$Config = $ExtConfig->getConfig($k);
			$Configuration[$k]->configuration_value = implode($Config->getGlue(), $v);
		}else{
			$Configuration[$k]->configuration_value = $v;
		}
	}else{
		$Configuration[$k]->configuration_value = '';
	}
}
$Configuration->save();

EventManager::attachActionResponse(array(
	'success' => true
), 'json');
?>