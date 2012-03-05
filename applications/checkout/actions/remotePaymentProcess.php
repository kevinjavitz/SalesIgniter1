<?php
$foundModule = false;
foreach(OrderPaymentModules::getModules() as $PaymentModule){
	if ($PaymentModule->ownsProcessPage() === true){
		$foundModule = true;
		break;
	}
}
if ($foundModule === true){
	$PaymentModule->processPayment();
	$PaymentModule->afterOrderProcess();
}

EventManager::attachActionResponse(
	itw_app_link((isset($_GET['checkoutType']) ? 'checkoutType=' . $_GET['checkoutType'] : null), 'checkout', 'success', 'SSL'),
	'redirect'
);
?>