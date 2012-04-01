<?php
	$CurrenciesTable = Doctrine_Core::getTable('CurrenciesTable')->find((int)$_GET['cID']);
	if ($CurrenciesTable){
		if ($CurrenciesTable->code == sysConfig::get('DEFAULT_CURRENCY')){
			Doctrine_Query::create()
			->update('Configuration')
			->set('configuration_value', '?', '')
			->where('configuration_key = ?', 'DEFAULT_CURRENCY')
			->execute();
		}
		$CurrenciesTable->delete();
	}

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>