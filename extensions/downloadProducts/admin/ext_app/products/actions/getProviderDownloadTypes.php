<?php
	$selectBox = htmlBase::newElement('selectbox')
	->setName('new_download_provider_type');
	
	$Qprovider = Doctrine_Query::create()
	->from('ProductsDownloadProviders')
	->where('provider_id = ?', (int) $_GET['pID'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qprovider){
		$moduleName = $Qprovider[0]['provider_module'];
		require(sysConfig::getDirFsCatalog() . 'extensions/downloadProducts/providerModules/' . $moduleName . '/module.php');
		$className = 'DownloadProvider' . ucfirst($moduleName);
		
		$Module = new $className();
		
		foreach($Module->getDownloadTypes() as $type){
			$selectBox->addOption($type, ucfirst($type));
		}
	}
	
	EventManager::attachActionResponse($selectBox->draw(), 'html');
?>