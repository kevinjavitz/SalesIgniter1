<?php

	if(isset($_POST['idpayment'])){
		Doctrine_Query::create()
		->delete('OrdersPaymentsHistory')
		->where('payment_history_id = ?', $_POST['idpayment'])
		->execute();
	}
	EventManager::attachActionResponse(array(
		'success' => true,
	), 'json');
?>