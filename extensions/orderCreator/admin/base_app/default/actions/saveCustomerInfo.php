<?php
if(isset($_POST['email']) && !empty($_POST['email']) && !isset($_POST['customers_id'])){
	$Customer = Doctrine_Core::getTable('Customers')->findOneByCustomersEmailAddress($_POST['email']);
}
$customerId = '';
if($Customer){
	$customerId = htmlBase::newElement('input')
			->setType('hidden')
			->setName('customers_id')
			->val($Customer->customers_id)
			->draw();
	$_POST['customers_id'] = $Customer->customers_id;
	$Editor->setCustomerId($Customer->customers_id);
	$Editor->setEmailAddress($Customer->customers_email_address);
	$Editor->setTelephone($Customer->customers_telephone);
}
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
	$Editor->setTelephone($_POST['telephone']);
}
if(isset($_POST['email']) && !empty($_POST['email']) && isset($_POST['customers_id'])){
	$QCustomer = Doctrine_Query::create()
	->update('Customers')
	->set('customers_email_address','?', $_POST['email'])
	->where('customers_id = ?', $_POST['customers_id'])
	->execute();
	$Editor->setEmailAddress($_POST['email']);
}
EventManager::notify('OrderCreatorSaveCustomerInfoResponse');

	EventManager::attachActionResponse($customerId, 'html');
?>