<?php
$selectBox = htmlBase::newElement('selectbox')
	->setName('new_stream_provider_type');

$Qprovider = Doctrine_Query::create()
	->from('ProductsStreamProviders')
	->where('provider_id = ?', (int)$_GET['pID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
if ($Qprovider){
	$moduleName = $Qprovider[0]['provider_module'];
	require(sysConfig::getDirFsCatalog() . 'extensions/streamProducts/providerModules/' . $moduleName . '/module.php');
	$className = 'StreamProvider' . ucfirst($moduleName);

	$Module = new $className();

	foreach ($Module->getStreamTypes() as $type){
		$selectBox->addOption($type, ucfirst($type));
	}
}

EventManager::attachActionResponse($selectBox->draw(), 'html');
?>