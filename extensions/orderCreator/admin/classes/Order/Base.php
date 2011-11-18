<?php
require(dirname(__FILE__) . '/AddressManager/Base.php');
require(dirname(__FILE__) . '/ProductManager/Base.php');
require(dirname(__FILE__) . '/TotalManager/Base.php');
require(dirname(__FILE__) . '/PaymentManager/Base.php');

class OrderCreator extends Order implements Serializable {
	
	public function __construct($orderId = null){
		if (is_null($orderId) === false){
			$this->mode = 'edit';
			$this->setOrderId($orderId);

			$Qorder = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersAddresses oa')
			->leftJoin('oa.Zones z')
			->leftJoin('oa.Countries c')
			->leftJoin('c.AddressFormat af')
			->leftJoin('o.OrdersTotal ot')
			->leftJoin('o.OrdersPaymentsHistory oph')
			->leftJoin('o.OrdersStatusHistory osh')
			->leftJoin('osh.OrdersStatus s')
			->leftJoin('s.OrdersStatusDescription sd')
			->leftJoin('o.OrdersProducts op')
			->where('o.orders_id = ?', $orderId)
			->andWhere('sd.language_id = ?', Session::get('languages_id'))
			->orderBy('ot.sort_order, osh.date_added DESC');

			EventManager::notify('OrderQueryBeforeExecute', &$Qorder);

			$Order = $Qorder->execute()->toArray();
			$this->Order = $Order[0];
			$this->customerId = $this->Order['customers_id'];

			$this->AddressManager = new OrderCreatorAddressManager($this->Order['OrdersAddresses']);
			$this->AddressManager->setOrderId($this->Order['orders_id']);

			$this->ProductManager = new OrderCreatorProductManager($this->Order['OrdersProducts']);
			$this->ProductManager->setOrderId($this->Order['orders_id']);

			$this->TotalManager = new OrderCreatorTotalManager($this->Order['OrdersTotal']);
			$this->TotalManager->setOrderId($this->Order['orders_id']);

			$this->PaymentManager = new OrderCreatorPaymentManager($this->Order['OrdersPaymentsHistory']);
			$this->PaymentManager->setOrderId($this->Order['orders_id']);
		}else{
			$this->mode = 'new';
			$this->Order = array(
				'orders_status' => 1,
				'currency' => Session::get('currency'),
				'currency_value' => Session::get('currency_value'),
				'OrdersStatusHistory' => array(),
				'OrdersPaymentsHistory' => array()
			);
			$this->AddressManager = new OrderCreatorAddressManager;
			$this->ProductManager = new OrderCreatorProductManager;
			$this->TotalManager = new OrderCreatorTotalManager(array(
				array(
					'module_type' => 'subtotal',
					'title' => 'Sub-Total:',
					'value' => 0.00,
					'sort_order' => 1
				),
				array(
					'module_type' => 'tax',
					'title' => 'Tax:',
					'value' => 0.00,
					'sort_order' => 2
				),
				array(
					'module_type' => 'total',
					'title' => 'Total:',
					'value' => 0.00,
					'sort_order' => 3
				)
			));
			$this->PaymentManager = new OrderCreatorPaymentManager;
		}
			
		$this->errorMessages = array();
	}

	public function init(){
		$this->ProductManager->init();
		/*$this->AddressManager->init();
		$this->TotalManager->init();
		$this->PaymentManager->init();*/
	}

	public function serialize(){
		$data = array(
			'orderId' => $this->getOrderId(),
			'customerId' => $this->getCustomerId(),
			'mode' => $this->mode,
			'Order' => $this->Order,
			'ProductManager' => $this->ProductManager,
			'AddressManager' => $this->AddressManager,
			'TotalManager' => $this->TotalManager,
			'PaymentManager' => $this->PaymentManager,
			'errorMessages' => $this->errorMessages
		);
		return serialize($data);
	}

	public function unserialize($data){
		$data = unserialize($data);
		foreach($data as $key => $dInfo){
			$this->$key = $dInfo;
		}
	}
	
	public function addErrorMessage($val){
		$this->errorMessages[] = $val;
	}
	
	public function hasErrors(){
		return (sizeof($this->errorMessages) > 0);
	}
	
	public function getErrors(){
		$return = $this->errorMessages;
		$this->errorMessages = array();
		return $return;
	}

	public function hasDebt(){
		return ($this->TotalManager->getTotalValue('total') > $this->PaymentManager->getPaymentsTotal());
	}

	public function hasCredit(){
		return ($this->TotalManager->getTotalValue('total') < $this->PaymentManager->getPaymentsTotal());
	}
	
	public function hasPendingPayments(){
		return ($this->PaymentManager->getPendingPaymentsTotal() > 0);
	}

	public function getBalance($type){
		global $currencies;
		switch($type){
			case 'debt':
				$Balance = ($this->TotalManager->getTotalValue('total') - $this->PaymentManager->getPaymentsTotal());
				break;
			case 'credit':
				$Balance = ($this->PaymentManager->getPaymentsTotal() - $this->TotalManager->getTotalValue('total'));
				break;
			case 'pending':
				$Balance = $this->PaymentManager->getPendingPaymentsTotal();
				break;
		}
		return $currencies->format($Balance);
	}
	
	public function editPaymentHistory(){
		return $this->PaymentManager->edit();
	}

	public function editTotals(){
		return $this->TotalManager->edit();
	}

	public function editAddresses(){
		return $this->AddressManager->editAll();
	}

	public function editAddress($type){
		return $this->AddressManager->editAddress($type);
	}
	
	public function editProducts(){
		return $this->ProductManager->editProducts();
	}
	
	public function setShippingModule($val){
		$this->Order['shipping_module'] = $val;
	}

	public function setPaymentModule($val){
		$this->Order['payment_module'] = $val;
	}

	public function getShippingModule(){
		return $this->Order['shipping_module'];
	}
	
	public function getPaymentModule(){
		return $this->Order['payment_module'];
	}
	
	public function getPassword(){
		if (isset($_POST['account_password'])){
			return $_POST['account_password'];
		}
	}

	public function setCustomerId($val){
		$this->customerId = $val;
	}
	
	public function setTelephone($val){
		$this->Order['customers_telephone'] = $val;
	}

	public function setEmailAddress($val){
		$this->Order['customers_email_address'] = $val;
	}

	public function editTelephone(){
		$input = htmlBase::newElement('input')
		->setName('telephone')
		->val($this->getTelephone());

		return $input->draw();
	}

	public function editEmailAddress(){
		$input = htmlBase::newElement('input')
		->setName('email')
		->val($this->getEmailAddress());

		return $input->draw();
	}
	
	public function createCustomerAccount($CollectionObj){
		$CustomerAddress = $this->AddressManager->getAddress('customer');
		
		$CollectionObj->language_id = Session::get('languages_id');
		$CollectionObj->customers_firstname = $CustomerAddress->getFirstName();
		$CollectionObj->customers_lastname = $CustomerAddress->getLastName();
		$CollectionObj->customers_email_address = $this->getEmailAddress();
		$CollectionObj->customers_telephone = $this->getTelephone();
		
		$password = '';
		for ($i=0; $i<10; $i++){
			$password .= tep_rand();
		}
		$salt = substr(md5($password), 0, 2);
		$password = md5($salt . $this->getPassword()) . ':' . $salt;

		$CollectionObj->customers_password = $password;
		//$CollectionObj->customers_gender = $this->getGender();
		//$CollectionObj->customers_dob = $this->getDateOfBirth();
		//$CollectionObj->customers_default_address_id = $this->getAddressId();
		//$CollectionObj->customers_fax = $this->getFax();
		//$CollectionObj->customers_newsletter = 1;
		
		EventManager::notify('OrderCreatorCreateCustomerAccount', $CollectionObj);
		
		$AddressBook = new AddressBook();
		$AddressBook->entry_gender = $CustomerAddress->getGender();
		$AddressBook->entry_company = $CustomerAddress->getCompany();
		$AddressBook->entry_firstname = $CustomerAddress->getFirstName();
		$AddressBook->entry_lastname = $CustomerAddress->getLastName();
		$AddressBook->entry_street_address = $CustomerAddress->getStreetAddress();
		$AddressBook->entry_suburb = $CustomerAddress->getSuburb();
		$AddressBook->entry_postcode = $CustomerAddress->getPostcode();
		$AddressBook->entry_city = $CustomerAddress->getCity();
		$AddressBook->entry_state = $CustomerAddress->getState();
		$AddressBook->entry_country_id = $CustomerAddress->getCountryId();
		$AddressBook->entry_zone_id = $CustomerAddress->getZoneId();
		
		EventManager::notify('OrderCreatorCreateCustomerAddress', $AddressBook);
		
		$CollectionObj->AddressBook->add($AddressBook);
		
		$CollectionObj->CustomersInfo->customers_info_number_of_logons = 0;
		
		$firstName = $CustomerAddress->getFirstName();
		$lastName = $CustomerAddress->getLastName();
		$emailAddress = $this->getEmailAddress();
		$fullName = $firstName . ' ' . $lastName;

		$emailEvent = new emailEvent('create_account');

		$emailEvent->setVars(array(
			'email_address' => $emailAddress,
			'password'      => $this->getPassword(),
			'firstname'     => $firstName,
			'lastname'      => $lastName,
			'full_name'     => $fullName
		));

		if (isset($this->newCustomerEmailVars)){
			foreach($this->newCustomerEmailVars as $var => $val){
				$emailEvent->setVar($var, $val);
			}
		}

		$emailEvent->sendEmail(array(
			'email' => $emailAddress,
			'name'  => $fullName
		));
	}
	
	public function sendNewOrderEmail($CollectionObj){
		global $appExtension, $currencies;
		$DeliveryAddress = $this->AddressManager->getAddress('delivery');
		$BillingAddress = $this->AddressManager->getAddress('billing');
		
		$sendToFormatted = $this->AddressManager->showAddress($DeliveryAddress, false);
		$billToFormatted = $this->AddressManager->showAddress($BillingAddress, false);

		$products_ordered = '';
		foreach($CollectionObj->OrdersProducts as $opInfo){
			$products_ordered .= sprintf("%s x %s (%s) = %s\n", 
				$opInfo->products_quantity,
				$opInfo->products_name,
				$opInfo->products_model,
				$currencies->display_price(
				 	$opInfo->products_price,
				 	$opInfo->products_tax,
				 	$opInfo->products_quantity
				)
			);
			
			EventManager::notify('OrderCreatorAddProductToEmail', $opInfo, &$products_ordered);
		}
		
		$emailEvent = new emailEvent('order_success', Session::get('languages_id'));
		$emailEvent->setVar('order_id', $CollectionObj->orders_id);
		$emailEvent->setVar('invoice_link', itw_catalog_app_link('order_id=' . $CollectionObj->orders_id, 'account', 'history_info', 'SSL', false));
		$emailEvent->setVar('date_ordered', strftime(sysLanguage::getDateFormat('long')));
		$emailEvent->setVar('ordered_products', $products_ordered);
		$emailEvent->setVar('billing_address', $billToFormatted);
		$emailEvent->setVar('shipping_address', $sendToFormatted);
		if (sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'){
			$PickupAddress = $this->AddressManager->getAddress('pickup');
			$pickUpFormatted = $this->AddressManager->showAddress($PickupAddress, false);
			$emailEvent->setVar('pickup_address', $pickUpFormatted);
		}
		if ($appExtension->isInstalled('goRentalsDepot') && $appExtension->isEnabled('goRentalsDepot')){
			//$emailEvent->setVar('rental_city', $this->info['delivery_depot_postcode']);
			//$emailEvent->setVar('delivery_depot', $this->info['delivery_depot']);
		}
		$emailEvent->setVar('order_comments', $CollectionObj->OrdersStatusHistory[0]->comments);

		$orderTotals = '';
		foreach($CollectionObj->OrdersTotal as $tInfo){
			$orderTotals .= strip_tags($tInfo['title']) . ' ' . strip_tags($tInfo['text']) . "\n";
		}
		$emailEvent->setVar('orderTotals', $orderTotals);

		/*
		 * @TODO: Why is ['payment_module'] == payment method title, it should be ['payment_method'] == payment method title
		 */
		if (!empty($CollectionObj->payment_module)){
			$Module = OrderPaymentModules::getModule($CollectionObj->payment_module);
			$emailEvent->setVar('paymentTitle', $Module->getTitle());
			if ($CollectionObj->payment_module == 'po'){
				$emailEvent->setVar('po_number', 'P.O. Number: ' . $CollectionObj->po_number);
			}
		}
		$sendVariables = array();
		EventManager::notify('OrderCreatorBeforeSendNewEmail', $CollectionObj, $emailEvent, &$products_ordered, &$sendVariables);
		$sendVariables['email'] = $CollectionObj->customers_email_address;
		$sendVariables['name'] = $BillingAddress->getName();

		$emailEvent->sendEmail($sendVariables);

		// send emails to other people
		if (sysConfig::get('SEND_EXTRA_ORDER_EMAILS_TO') != '') {
			$emailEvent->sendEmail(array(
				'email' => sysConfig::get('SEND_EXTRA_ORDER_EMAILS_TO'),
				'name'  => ''
			));
		}
	}

	public function sendNewEstimateEmail($CollectionObj, $emailAddress = ''){
		global $appExtension, $currencies;
		$DeliveryAddress = $this->AddressManager->getAddress('delivery');
		$BillingAddress = $this->AddressManager->getAddress('billing');

		$sendToFormatted = $this->AddressManager->showAddress($DeliveryAddress, false);
		$billToFormatted = $this->AddressManager->showAddress($BillingAddress, false);

		$products_ordered = '';
		foreach($CollectionObj->OrdersProducts as $opInfo){
			$products_ordered .= sprintf("%s x %s (%s) = %s\n",
				$opInfo->products_quantity,
				$opInfo->products_name,
				$opInfo->products_model,
				$currencies->display_price(
				 	$opInfo->products_price,
				 	$opInfo->products_tax,
				 	$opInfo->products_quantity
				)
			);

			EventManager::notify('OrderCreatorAddProductToEmail', $opInfo, &$products_ordered);
		}

		$emailEvent = new emailEvent('estimate_success', Session::get('languages_id'));
		$emailEvent->setVar('order_id', $CollectionObj->orders_id);
		$emailEvent->setVar('invoice_link', itw_catalog_app_link('order_id=' . $CollectionObj->orders_id, 'account', 'history_info', 'SSL', false));
		$emailEvent->setVar('date_ordered', strftime(sysLanguage::getDateFormat('long')));
		$emailEvent->setVar('ordered_products', $products_ordered);
		$emailEvent->setVar('billing_address', $billToFormatted);
		$emailEvent->setVar('shipping_address', $sendToFormatted);
		if (sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'){
			$PickupAddress = $this->AddressManager->getAddress('pickup');
			$pickUpFormatted = $this->AddressManager->showAddress($PickupAddress, false);
			$emailEvent->setVar('pickup_address', $pickUpFormatted);
		}
		if ($appExtension->isInstalled('goRentalsDepot') && $appExtension->isEnabled('goRentalsDepot')){
			//$emailEvent->setVar('rental_city', $this->info['delivery_depot_postcode']);
			//$emailEvent->setVar('delivery_depot', $this->info['delivery_depot']);
		}
		$emailEvent->setVar('order_comments', $CollectionObj->OrdersStatusHistory[0]->comments);

		$orderTotals = '';
		foreach($CollectionObj->OrdersTotal as $tInfo){
			$orderTotals .= strip_tags($tInfo['title']) . ' ' . strip_tags($tInfo['text']) . "\n";
		}
		$emailEvent->setVar('orderTotals', $orderTotals);
		$sendVariables = array();
		EventManager::notify('OrderCreatorBeforeSendNewEmail', $CollectionObj, $emailEvent, &$products_ordered, &$sendVariables);
		if($emailAddress == ''){
			$email = $CollectionObj->customers_email_address;
		}else{
			$email = $emailAddress;
		}
		$sendVariables['email'] = $email;
		$sendVariables['name'] = $BillingAddress->getName();

		$emailEvent->sendEmail($sendVariables);

		// send emails to other people
		if (sysConfig::get('SEND_EXTRA_ORDER_EMAILS_TO') != '') {
			$emailEvent->sendEmail(array(
				'email' => sysConfig::get('SEND_EXTRA_ORDER_EMAILS_TO'),
				'name'  => ''
			));
		}
	}
}
?>