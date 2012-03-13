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
}else{
	$ShoppingCart->emptyCart(true);
	Session::remove('sendto');
	Session::remove('billto');
	Session::remove('shipping');
	Session::remove('payment');
	Session::remove('comments');

}
EventManager::attachActionResponse(
	itw_app_link((isset($_GET['checkoutType']) ? 'checkoutType=' . $_GET['checkoutType'] : null), 'checkout', 'success', 'SSL'),
	'redirect'
);


?>