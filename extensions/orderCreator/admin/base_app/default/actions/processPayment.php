<?php
	$success = $Editor->PaymentManager->processPayment($_POST['payment_method']);
	
	$html = '';
	if ($success === true){
		$Qhistory = Doctrine_Query::create()
		->from('OrdersPaymentsHistory')
		->where('orders_id = ?', $Editor->getOrderId())
		->orderBy('payment_history_id DESC')
		->limit(1)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$paymentHistory = $Qhistory[0];
		
		if (array_key_exists('card_details', $paymentHistory) && is_null($paymentHistory['card_details']) === false){
			$cardInfo = unserialize(cc_decrypt($paymentHistory['card_details']));
			if (empty($cardInfo['cardNumber'])){
				unset($cardInfo);
			}
		}
		
		if ($paymentHistory['success'] == 0){
			$iconClass = 'ui-icon-closethick';
		}elseif ($paymentHistory['success'] == 1){
			$iconClass = 'ui-icon-check';
		}elseif ($paymentHistory['success'] == 2){
			$iconClass = 'ui-icon-alert';
		}
			
		$html = '<tr>' . 
			'<td class="ui-widget-content" style="border-top:none;">' . 
				tep_date_short($paymentHistory['date_added']) . 
			'</td>' . 
			'<td class="ui-widget-content" style="border-top:none;border-left:none;">' . 
				$paymentHistory['payment_method'] . 
			'</td>' . 
			'<td class="ui-widget-content" style="border-top:none;border-left:none;">' . 
				stripslashes($paymentHistory['gateway_message']) . 
			'</td>' . 
			'<td class="ui-widget-content" style="border-top:none;border-left:none;" align="center">' . 
				'<span class="ui-icon ' . $iconClass . '">' . 
			'</td>' . 
			'<td class="ui-widget-content" style="border-top:none;border-left:none;">' . 
				$currencies->format($paymentHistory['payment_amount']) . 
			'</td>' . 
			'<td class="ui-widget-content" style="border-top:none;border-left:none;">' . 
				(isset($cardInfo) && is_array($cardInfo) ? $cardInfo['cardNumber'] : '') . 
			'</td>' . 
			'<td class="ui-widget-content" style="border-top:none;border-left:none;">' . 
				(isset($cardInfo) && is_array($cardInfo) ? $cardInfo['cardExpMonth'] . ' / ' . $cardInfo['cardExpYear'] : '') . 
			'</td>' . 
			'<td class="ui-widget-content" style="border-top:none;border-left:none;">' . 
				(isset($cardInfo) && is_array($cardInfo) && isset($cardInfo['cardCvvNumber']) ? $cardInfo['cardCvvNumber'] : 'N/A') . 
			'</td>' . 
		'</tr>';
	}
	
	EventManager::attachActionResponse(array(
		'success' => $success,
		'tableRow' => $html
	), 'json');
?>