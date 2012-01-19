<?php
$Customer = Doctrine_Core::getTable('Customers')->find((int) $_GET['cID']);

$Address = false;
foreach($Customer->AddressBook as $aInfo){
	if ($aInfo->address_book_id == $Customer->customers_default_address_id){
		$Address = $aInfo;
		break;
	}
}

$Qcustomer = Doctrine_Query::create()
	->from('Customers c')
	->leftJoin('c.AddressBook ab')
	->leftJoin('ab.Countries co')
	->leftJoin('co.AddressFormat af')
	->leftJoin('ab.Zones z')
	->where('c.customers_id = ?', (int)$_GET['cID'])
	->andWhere('ab.address_book_id = c.customers_default_address_id')
	->execute();

$customerId = htmlBase::newElement('input')
	->setType('hidden')
	->setName('customers_id')
	->val($Customer->customers_id);

$addressArray = $Address->toArray();
$addressArray['id'] = $Address->address_book_id;
$addressArray['entry_name'] = $Address->entry_firstname . ' ' . $Address->entry_lastname;

$addressArray['address_type'] = 'customer';
$OrderCustomerAddress = new OrderCreatorAddress($addressArray);

$addressArray['address_type'] = 'billing';
$OrderBillingAddress = new OrderCreatorAddress($addressArray);

$addressArray['address_type'] = 'delivery';
$OrderDeliveryAddress = new OrderCreatorAddress($addressArray);

$addressArray['address_type'] = 'pickup';
$OrderPickupAddress = new OrderCreatorAddress($addressArray);

$Editor->setCustomerId($Customer->customers_id);
$Editor->setEmailAddress($Customer->customers_email_address);
$Editor->setTelephone($Customer->customers_telephone);
$Editor->AddressManager->addAddressObj($OrderCustomerAddress);
$Editor->AddressManager->addAddressObj($OrderBillingAddress);
$Editor->AddressManager->addAddressObj($OrderDeliveryAddress);
$Editor->AddressManager->addAddressObj($OrderPickupAddress);

$response = array(
	'success' => true,
	'customer' => $Editor->AddressManager->editAddress('customer') . $customerId->draw(),
	'billing' => $Editor->AddressManager->editAddress('billing'),
	'delivery' => $Editor->AddressManager->editAddress('delivery'),
	'pickup' => $Editor->AddressManager->editAddress('pickup'),
	'field_values' => array(
		'email' => $Editor->getEmailAddress(),
		'member_number' => $Editor->getMemberNumber(),
		'telephone' => $Editor->getTelephone(),
	)
);
EventManager::notify('OrderCreatorLoadCustomerInfoResponse', &$response, $Customer);

EventManager::attachActionResponse($response, 'json');
?>