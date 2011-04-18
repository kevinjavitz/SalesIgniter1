<?php
	$CurrenciesTable = Doctrine_Core::getTable('CurrenciesTable')->findOneByCurrenciesId((int)$_GET['cID']);
	if ($CurrenciesTable){
		if ($CurrenciesTable->code == DEFAULT_CURRENCY){
			Doctrine_Query::create()
			->update('Configuration')
			->set('configuration_value', '?', '')
			->where('configuration_key = ?', 'DEFAULT_CURRENCY')
			->execute();
		}
		$CurrenciesTable->delete();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>