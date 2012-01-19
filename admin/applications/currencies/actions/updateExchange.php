<?php
	$server_used = CURRENCY_SERVER_PRIMARY;
	
	$Qcurrencies = Doctrine_Query::create()
	->select('currencies_id, code, title')
	->from('CurrenciesTable')
	->execute();
	foreach($Qcurrencies as $currency){
		$quote_function = 'quote_' . CURRENCY_SERVER_PRIMARY . '_currency';
		$rate = $quote_function($currency->code, sysConfig::get('DEFAULT_CURRENCY'));
		
		if (empty($rate) && (tep_not_null(CURRENCY_SERVER_BACKUP))){
			$messageStack->addSession(
				'pageStack',
				sprintf(sysLanguage::get('WARNING_PRIMARY_SERVER_FAILED'), CURRENCY_SERVER_PRIMARY, $currency->title, $currency->code),
				'warning'
			);
			
			$quote_function = 'quote_' . CURRENCY_SERVER_BACKUP . '_currency';
			$rate = $quote_function($currency->code, sysConfig::get('DEFAULT_CURRENCY'));
			
			$server_used = CURRENCY_SERVER_BACKUP;
		}
		
		if (tep_not_null($rate)){
			$currency->value = $rate;
			$currency->last_updated = date('Y-m-d H:i:s');
			$currency->save();

			$messageStack->addSession(
				'pageStack',
				sprintf(sysLanguage::get('TEXT_INFO_CURRENCY_UPDATED'), $currency->title, $currency->code, $server_used),
				'success'
			);
		}else{
			$messageStack->addSession(
				'pageStack',
				sprintf(sysLanguage::get('ERROR_CURRENCY_INVALID'), $currency->title, $currency->code, $server_used),
				'error'
			);
		}
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action'))), 'redirect');
?>