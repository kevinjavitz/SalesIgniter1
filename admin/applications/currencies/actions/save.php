<?php
	$CurrenciesTable = Doctrine_Core::getTable('CurrenciesTable');
	if (isset($_GET['cID'])){
		$newCurrency = $CurrenciesTable->findOneByCurrenciesId($_GET['cID']);
	}else{
		$newCurrency = $CurrenciesTable->create();
	}
	
	$newCurrency->title = $_POST['title'];
	$newCurrency->code = $_POST['code'];
	$newCurrency->symbol_left = $_POST['symbol_left'];
	$newCurrency->symbol_right = $_POST['symbol_right'];
	$newCurrency->decimal_point = $_POST['decimal_point'];
	$newCurrency->thousands_point = $_POST['thousands_point'];
	$newCurrency->decimal_places = $_POST['decimal_places'];
	$newCurrency->value = $_POST['value'];
	$newCurrency->save();
	
	if (isset($_POST['default'])) {
		Doctrine_Query::create()
		->update('Configuration')
		->set('configuration_value', '?', $newCurrency->code)
		->where('configuration_key = ?', 'DEFAULT_CURRENCY')
		->execute();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')) . 'cID=' . $newCurrency->currencies_id), 'redirect');
?>