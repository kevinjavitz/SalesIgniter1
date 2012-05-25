<?php
	$Module = OrderPaymentModules::getModule($_POST['payment_method'], true);
	$mInfo = $Module->onSelect();

	$onePageCheckout->onePage['info']['payment'] = array(
		'id'    => $Module->getCode(),
		'title' => $Module->getTitle()
	);
	OrderPaymentModules::setSelected($Module->getCode());
	EventManager::attachActionResponse(array(
		'success' => true	
	), 'json');
?>