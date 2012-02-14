<?php
	$Module = OrderPaymentModules::getModule($_POST['payment_method']);
	$mInfo = $Module->onSelect();

	$onePageCheckout->onePage['info']['payment'] = array(
		'id'    => $Module->getCode(),
		'title' => $Module->getTitle()
	);

	EventManager::attachActionResponse(array(
		'success' => true	
	), 'json');
?>