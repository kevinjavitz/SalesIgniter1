<?php
$addressArray['address_type'] = 'customer';
$OrderCustomerAddress = new OrderCreatorAddress($addressArray);

$addressArray['address_type'] = 'billing';
$OrderBillingAddress = new OrderCreatorAddress($addressArray);

$addressArray['address_type'] = 'delivery';
$OrderDeliveryAddress = new OrderCreatorAddress($addressArray);

$addressArray['address_type'] = 'pickup';
$OrderPickupAddress = new OrderCreatorAddress($addressArray);

$Editor->AddressManager->addAddressObj($OrderCustomerAddress);
$Editor->AddressManager->addAddressObj($OrderBillingAddress);
$Editor->AddressManager->addAddressObj($OrderDeliveryAddress);
$Editor->AddressManager->addAddressObj($OrderPickupAddress);

$Editor->AddressManager->updateFromPost();

if(isset($_POST['telephone']) && !empty($_POST['telephone']) && isset($_POST['customers_id'])){
	$QCustomer = Doctrine_Query::create()
	->update('Customers')
	->set('customers_telephone','?', $_POST['telephone'])
	->where('customers_id = ?', $_POST['customers_id'])
	->execute();
}
if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['customers_id'])){
	$QCustomer = Doctrine_Query::create()
	->update('Customers')
	->set('customers_email_address','?', $_POST['email'])
	->where('customers_id = ?', $_POST['customers_id'])
	->execute();
}
EventManager::notify('OrderCreatorSaveCustomerInfoResponse');

	EventManager::attachActionResponse('', 'html');
?>