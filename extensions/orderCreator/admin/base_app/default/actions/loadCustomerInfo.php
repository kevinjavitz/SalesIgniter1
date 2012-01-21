<?php
	$Qcustomer = Doctrine_Query::create()
	->from('Customers c')
	->leftJoin('c.AddressBook ab')
	->leftJoin('ab.Countries co')
	->leftJoin('co.AddressFormat af')
	->leftJoin('ab.Zones z')
	->where('c.customers_id = ?', (int) $_GET['cID'])
	->andWhere('ab.address_book_id = c.customers_default_address_id')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$customerId = htmlBase::newElement('input')
	->setType('hidden')
	->setName('customers_id')
	->val((int) $_GET['cID']);
	
	$addressArray = $Qcustomer[0]['AddressBook'][0];
	$addressArray['id'] = $Qcustomer[0]['AddressBook'][0]['address_book_id'];
	$addressArray['entry_name'] = $Qcustomer[0]['AddressBook'][0]['entry_firstname'] . ' ' . $Qcustomer[0]['AddressBook'][0]['entry_lastname'];

	$addressArray['address_type'] = 'customer';
	$OrderCustomerAddress = new OrderCreatorAddress($addressArray);

	$addressArray['address_type'] = 'billing';
	$OrderBillingAddress = new OrderCreatorAddress($addressArray);

	$addressArray['address_type'] = 'delivery';
	$OrderDeliveryAddress = new OrderCreatorAddress($addressArray);

	$addressArray['address_type'] = 'pickup';
	$OrderPickupAddress = new OrderCreatorAddress($addressArray);

	$Editor->setCustomerId($Qcustomer[0]['customers_id']);
	$Editor->setEmailAddress($Qcustomer[0]['customers_email_address']);
	$Editor->setTelephone($Qcustomer[0]['customers_telephone']);
	$Editor->AddressManager->addAddressObj($OrderCustomerAddress);
	$Editor->AddressManager->addAddressObj($OrderBillingAddress);
	$Editor->AddressManager->addAddressObj($OrderDeliveryAddress);
	$Editor->AddressManager->addAddressObj($OrderPickupAddress);

	EventManager::attachActionResponse(array(
		'success' => true,
		'email_address' => $Editor->getEmailAddress(),
		'telephone' => $Editor->getTelephone(),
		'customer' => $Editor->AddressManager->editAddress('customer') . $customerId->draw(),
		'billing' => $Editor->AddressManager->editAddress('billing'),
		'delivery' => $Editor->AddressManager->editAddress('delivery'),
		'pickup' => $Editor->AddressManager->editAddress('pickup')
	), 'json');
?>