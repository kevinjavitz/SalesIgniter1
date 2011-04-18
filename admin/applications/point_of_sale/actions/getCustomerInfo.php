<?php
	$cID = $_GET['customers_id'];
	if ($cID == 'new'){
		$html = pointOfSaleHTML::getAddressTable(false, true);
	}else{
		$Qcustomer = dataAccess::setQuery('select * from {customers} where customers_id = {customer_id}');
		$Qcustomer->setTable('{customers}', TABLE_CUSTOMERS);
		$Qcustomer->setValue('{customer_id}', $cID);
		$Qcustomer->runQuery();

		$cID = $Qcustomer->getVal('customers_id');
		$defaultID = $Qcustomer->getVal('customers_default_address_id');

		$addresses = pointOfSaleHTML::getAddressBookEntries(array(
			'customerID'      => $cID,
			'shippingAddress' => $defaultID,
			'billingAddress'  => $defaultID,
			'pickupAddress'   => $defaultID
		));

		$table = htmlBase::newElement('table')
		->setCellSpacing(0)
		->setCellPadding(3)
		->css('width', '98%');
		
		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'text'   => '<b>Name:</b>'
				),
				array(
					'addCls' => 'main',
					'text'   => '<b>Email:</b>'
				),
				array(
					'addCls' => 'main',
					'text'   => '<b>Default Address:</b>'
				)
			)
		));
		
		$table->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'text'   => $Qcustomer->getVal('customers_firstname') . ' ' . $Qcustomer->getVal('customers_lastname')
				),
				array(
					'addCls' => 'main',
					'text'   => $Qcustomer->getVal('customers_email_address')
				),
				array(
					'addCls' => 'main',
					'text'   => tep_address_label($cID, $defaultID, true, '', '<br>')
				)
			)
		));
		
		$addressesTable = htmlBase::newElement('table')
		->setCellSpacing(0)
		->setCellPadding(3)
		->css('width', '98%');
		
		$addressesTable->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'text'   => 'Addresses On File'
				)
			)
		));
		
		$addressesTable->addBodyRow(array(
			'columns' => array(
				array(
					'addCls' => 'main',
					'attr'   => array(
						'id' => 'addressesOnFile'
					),
					'css'    => array(
						'width' => '100%'
					),
					'text'   => $addresses
				)
			)
		));
		
		$html = $table->draw() . '<hr />' . $addressesTable->draw();
	}
	
	EventManager::attachActionResponse($html, 'html');
?>