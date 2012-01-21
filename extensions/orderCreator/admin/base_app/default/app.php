<?php
	$appContent = $App->getAppContentFile();

	//require(sysConfig::getDirFsCatalog() . 'includes/classes/product.php');
	require(sysConfig::getDirFsCatalog() . 'includes/functions/crypt.php');
	require(sysConfig::getDirFsCatalog() . 'includes/classes/http_client.php');

	require(sysConfig::getDirFsCatalog() . 'includes/classes/currencies.php');
	$currencies = new currencies();
	
	if ($App->getAppPage() == 'new'){
		if (Session::exists('OrderCreator') === false){
			if (isset($_GET['oID'])){
				$Editor = new OrderCreator((int) $_GET['oID']);
			}else{
				$Editor = new OrderCreator;
			}
			Session::set('OrderCreator', $Editor);
		}else{
			$Editor = Session::get('OrderCreator');
			if (!isset($_GET['action']) && isset($_GET['oID']) && $Editor->getOrderId() != $_GET['oID']){
				$Editor = new OrderCreator((int) $_GET['oID']);
				Session::set('OrderCreator', $Editor);
			}else{
				//$Editor = new Order((int) $_GET['oID']);
				//Session::set('OrderEditor', $Editor);
				$Editor->init();
			}
		}
    	$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.datepicker.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.ui.autocomplete.js');
		$App->addStylesheetFile('ext/jQuery/themes/smoothness/ui.autocomplete.css');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.core.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.slide.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fold.js');
		$App->addJavascriptFile('ext/jQuery/ui/jquery.effects.fade.js');
	}
	
	$orders_statuses = array();
	$orders_status_array = array();
	$Qstatus = Doctrine_Query::create()
	->select('s.orders_status_id, sd.orders_status_name')
	->from('OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('sd.language_id = ?', (int)Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($Qstatus as $status){
		$orders_statuses[] = array(
			'id' => $status['orders_status_id'],
			'text' => $status['OrdersStatusDescription'][0]['orders_status_name']
		);
		$orders_status_array[$status['orders_status_id']] = $status['OrdersStatusDescription'][0]['orders_status_name'];
	}
	
	if ($Editor->hasErrors()){
		foreach($Editor->getErrors() as $msg){
			$messageStack->add('pageStack', $msg, 'error');
		}
	}
?>