<?php
$selectBox = htmlBase::newElement('selectbox')
	->setName('new_stream_provider_type');

$providers = Doctrine_Query::create()
	->from('ProductsStreamProviders')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
if ($providers){
	foreach ($providers as $providerInfo){
		$selectBox->addOption($providerInfo['provider_id'], ucfirst($providerInfo['provider_name']));
	}
}

EventManager::attachActionResponse($selectBox->draw(), 'html');
?>