<?php

	//$success = $Editor->PaymentManager->refundPayment($_POST['payment_module']);

	$QpaymentMethods = Doctrine_Query::create()
	->from('Modules')
	->where('modules_type = ?', 'order_payment')
	->andWhere('modules_status = ?', '1')
	->orderBy('modules_code')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$PaymentMethodDrop = htmlBase::newElement('selectbox')
	->setName('payment_method')
	->css('width', '300px');
	$PaymentMethodDrop->selectOptionByValue($_POST['payment_method']);

	foreach($QpaymentMethods as $mInfo){
		$Module = OrderPaymentModules::getModule($mInfo['modules_code']);
		if ($Module !== false && $Module->hasFormUrl() === false){
			$PaymentMethodDrop->addOption($Module->getCode(), $Module->getTitle());
		}
	}

	$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none">'.date('m/d/Y').'</td>';
	$headerPaymentCols[] = '<td class="ui-widget-content ui-state-hover" align="left" style="border-top:none;border-left:none;">'.$PaymentMethodDrop->draw().'</td>';

	if (isset($_POST['payment_method']) && !empty($_POST['payment_method'])){
		$Module = OrderPaymentModules::getModule($_POST['payment_method']);
		if (method_exists($Module, 'getCreatorRow')){
			$Module->getCreatorRow($Editor, &$headerPaymentCols);
		}
	}

	$html = '<tr>';
	foreach($headerPaymentCols as $column){
		$html .= $column;
	}
	$html.='</tr>';

	EventManager::attachActionResponse(array(
		'success' => true,
		'tableRow' => $html
	), 'json');
?>