<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class osC_onePageCheckout {
	public $onePage;
	
	public function __construct(){
		$this->buildSession((isset($_GET['rType']) === false));
		//$this->setMode('default');
	}

	public function reset(){
		$this->buildSession(true);
	}

	public function &getUserAccount(){
		$userAccount = &Session::getReference('userAccount');
		return $userAccount;
	}

	public function setMode($mode){
		$this->onePage['checkoutMode'] = $mode;
	}

	public function getMode(){
		return $this->onePage['checkoutMode'];
	}

	public function isMembershipCheckout(){
		return ($this->getMode() == 'membership');
	}

	public function isNormalCheckout(){
		return ($this->getMode() == 'default');
	}

	public function buildSession($forceReset = false){
		if (Session::exists('onepage') === false || $forceReset === true){
			Session::set('onepage', array(
				'info'              => array(
					'payment'         => array(), 'shipping'        => array(),
					'comments'        => '',      'telephone'       => '',
					'email_address'   => '',      'password'        => '',
				),
				'customerAddressId' => 'customer',
				'deliveryAddressId' => 'delivery',
				'billingAddressId'  => 'billing',
				'pickupAddressId'   => 'pickup',
				'createAccount'     => 'false',
				'rentalPlan'        => false,
				'shippingEnabled'   => true,
				'pickupEnabled'     => (sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'),
				'checkoutMode'      => ''
			));
			Session::set('payment', false);
			Session::set('shipping', false);
		}

		$this->onePage = &Session::getReference('onepage');
	}

	public function loadOrdersVars(){
		global $order;
		$info =& $this->onePage['info'];
		if (isset($info['payment']['title'])){
			$order->info['payment_module'] = $info['payment']['title'];
		}
		if (isset($info['shipping']['title'])){
			$order->info['shipping_module'] = $info['shipping']['title'];
		}
		if (isset($info['comments'])){
			$order->info['comments'] = $info['comments'];
		}
		
		EventManager::notify('OnepageCheckoutLoadOrdersVars', &$info, &$order);
		
		$userAccount = &$this->getUserAccount();
		$userAccount->setCustomerInfo(array(
			'emailAddress' => $info['email_address'],
			'telephone'    => $info['telephone'],
			'password'     => $info['password']
		));
	}

	public function init(){
		if ($this->isNormalCheckout() === true){
			$this->verifyContents();
		}

		if (!isset($_GET['payment_error'])){
			//$this->reset();
		}
		
		if ($this->isNormalCheckout() === true){
			if (sysConfig::get('STOCK_ALLOW_CHECKOUT') != 'true') {
				$this->checkStock();
			}
		}

		$userAccount = &$this->getUserAccount();
		if ($userAccount->isLoggedIn() === true){
			$this->setupLoggedInCustomer();
		}

		$this->removeCCGV();
	}

	public function setShippingStatus(){
		global $order, $ShoppingCart;

		if ($this->isMembershipCheckout() === true){
			$this->onePage['shippingEnabled'] = false;
			return;
		}

		if ($ShoppingCart->showWeight() <= 0) {
			$this->onePage['shippingEnabled'] = false;
		}

		if (in_array($ShoppingCart->getContentType(), array('virtual', 'virtual_weight'))) {
			$this->onePage['info']['shipping'] = false;
			$this->onePage['shippingEnabled'] = false;
		}

		Session::set('shipping', $this->onePage['info']['shipping']);

		EventManager::notify('CheckoutSetShippingStatus');

	}

	public function fixTaxes(){
		global $order;
		if ($this->isMembershipCheckout() === true){
			$this->loadMembershipPlan();
		}else{
			$order->loadOrderInfo();
		}
	}

	public function setupLoggedInCustomer(){
		$userAccount = &$this->getUserAccount();
		$addressBook =& $userAccount->plugins['addressBook'];

		$this->onePage['createAccount'] = 'false';
		$this->onePage['info']['email_address'] = $userAccount->getEmailAddress();
		$this->onePage['info']['telephone'] = $userAccount->getTelephoneNumber();

		$customerAddress = $addressBook->getAddress($addressBook->getDefaultAddressId());
		if ($addressBook->entryExists('customer') === false){
			$addressBook->addAddressEntry('customer', $customerAddress);
		}
		if ($addressBook->entryExists('delivery') === false){
			$addressBook->addAddressEntry('delivery', $customerAddress);
		}
		if ($addressBook->entryExists('billing') === false){
			$addressBook->addAddressEntry('billing', $customerAddress);
		}
		if ($addressBook->entryExists('pickup') === false){
			$addressBook->addAddressEntry('pickup', $customerAddress);
		}
	}

	public function createCustomerAccount(){
		$userAccount = &$this->getUserAccount();
		if ($this->onePage['createAccount'] == 'true'){
			$addressBook =& $userAccount->plugins['addressBook'];
			$customerAddress = $addressBook->getAddress('billing');
			
			$userAccount->setFirstName($customerAddress['entry_firstname']);
			$userAccount->setLastName($customerAddress['entry_lastname']);
			$userAccount->setEmailAddress($this->onePage['info']['email_address']);
			$userAccount->setFaxNumber((isset($this->onePage['info']['fax']) ? $this->onePage['info']['fax'] : ''));
			$userAccount->setTelephoneNumber($this->onePage['info']['telephone']);
			$userAccount->setPassword($this->onePage['info']['password']);
			$userAccount->setGender($customerAddress['entry_gender']);
			$userAccount->setDateOfBirth((isset($customerAddress['dob']) ? tep_date_raw($customerAddress['dob']) : ''));
			$userAccount->setNewsletter((isset($this->onePage['info']['newsletter']) ? $this->onePage['info']['newsletter'] : '0'));
			$userAccount->setLanguageId(Session::get('languages_id'));
			$customerId = $userAccount->createNewAccount();

			if (isset($this->onePage['info']['order_id'])){
				Doctrine_Query::create()
				->update('Orders')
				->set('customers_id', '?', $customerId)
				->where('orders_id = ?', $this->onePage['info']['order_id'])
				->execute();
				
				//unset($this->onePage['info']['order_id']);
			}
			
			$defaultId = $addressBook->insertAddress($addressBook->getAddress('billing'));
			if (isset($_POST['diffShipping'])){
				$addressBook->insertAddress($addressBook->getAddress('delivery'));
			}

			if (isset($_POST['diffPickup'])){
				$addressBook->insertAddress($addressBook->getAddress('pickup'));
			}

			$addressBook->setDefaultAddress($defaultId, true);

			if ($this->isMembershipCheckout() === true){
				$userAccount->plugins['membership']->setRentalAddress($defaultId);
			}
			
			EventManager::notify('CheckoutAddNewCustomer', $customerId);
		}else{
			if ($userAccount->isLoggedIn() === false){
				/* Confusing, i know. it's for anonymous accounts */
				$userAccount->processLogOut();
				$this->__destruct();
			}
		}
	}
	
	public function drawHiddenFieldsFromArray($arr, $inputName = ''){
		$fields = '';
		foreach($arr as $varName => $val){
			if (empty($inputName)){
				$fieldName = $varName;
			}else{
				$fieldName = $inputName . '[' . $varName . ']';
			}
			
			if (is_array($val)){
				$fields .= $this->drawHiddenFieldsFromArray($val, $fieldName);
			}else{
				//echo $fieldName . '::' . $val . '<br>';
				$fields .= tep_draw_hidden_field($fieldName, $val);
			}
		}
		return $fields;
	}

	public function loadMembershipPlan(){
		global $order;
		
		$userAccount = &$this->getUserAccount();
		$addressBook =& $userAccount->plugins['addressBook'];
		$billingAddress = $addressBook->getAddress('billing');
		if ($billingAddress['entry_country_id'] == ''){
			$countryId = 0;
			$zoneId = 0;
		}else{
			$countryId = $billingAddress['entry_country_id'];
			$zoneId = $billingAddress['entry_zone_id'];
		}

		$order->products = array(
			array(
				'quantity'                => 1,
				'name'                    => $this->onePage['rentalPlan']['name'],
				'model'                   => 'rental_package',
				'tax'                     => tep_get_tax_rate($this->onePage['rentalPlan']['tax_class'], $countryId, $zoneId),
				'tax_description'         => tep_get_tax_description($this->onePage['rentalPlan']['tax_class'], $countryId, $zoneId),
				'price'                   => $this->onePage['rentalPlan']['price'],
				'final_price'             => $this->onePage['rentalPlan']['price'],
				'weight'                  => 0,
				'id'                      => 0,
				'products_ptype'          => 'NR',
				'date_available'          => '',
				'products_recurring_time' => '',
				'products_recurring_days' => '',
				'purchase_type'           => 'membership'
			)
		);

		$shown_price = tep_add_tax($order->products[0]['price'], $order->products[0]['tax']) * $order->products[0]['quantity'];
		$order->info['subtotal'] = $shown_price;

		$products_tax = $order->products[0]['tax'];
		$products_tax_description = $order->products[0]['tax_description'];
		if (sysConfig::get('DISPLAY_PRICE_WITH_TAX') == 'true') {
			$order->info['tax'] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
			if (isset($order->info['tax_groups']["$products_tax_description"])) {
				$order->info['tax_groups']["$products_tax_description"] += $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
			} else {
				$order->info['tax_groups']["$products_tax_description"] = $shown_price - ($shown_price / (($products_tax < 10) ? "1.0" . str_replace('.', '', $products_tax) : "1." . str_replace('.', '', $products_tax)));
			}
		} else {
			$order->info['tax'] += ($products_tax / 100) * $shown_price;
			if (isset($order->info['tax_groups']["$products_tax_description"])) {
				$order->info['tax_groups']["$products_tax_description"] += ($products_tax / 100) * $shown_price;
			} else {
				$order->info['tax_groups']["$products_tax_description"] = ($products_tax / 100) * $shown_price;
			}
		}

		$order->info['free_trial'] = $this->onePage['rentalPlan']['free_trial'];
		$order->info['free_trial_amount'] = $this->onePage['rentalPlan']['free_trial_amount'];

		if (sysConfig::get('DISPLAY_PRICE_WITH_TAX') == 'true') {
			$order->info['total'] = $order->info['subtotal'];
		} else {
			$order->info['total'] = $order->info['subtotal'] + $order->info['tax'];
		}
	}

	public function getAddressFormatted($type){
		global $order;
		switch($type){
			case 'sendto':
				$varName = 'delivery';
				break;
			case 'billto':
				$varName = 'billing';
				break;
			case 'pickup':
				$varName = 'pickup';
				break;
		}
		$userAccount = &$this->getUserAccount();
		return $userAccount->plugins['addressBook']->formatAddress($varName);
	}

	public function verifyContents(){
		global $ShoppingCart;
		// if there is nothing in the customers cart, redirect them to the shopping cart page
		if ($ShoppingCart->countContents() < 1) {
			tep_redirect(itw_app_link(null, 'shoppingCart', 'default'));
		}
	}

	public function checkStock(){
		global $ShoppingCart;
		foreach($ShoppingCart->getProducts() as $cartProduct) {
			if (tep_check_stock($cartProduct->getIdString(), $cartProduct->getQuantity())) {
				//tep_redirect(itw_app_link(null, 'shoppingCart', 'default'));
				break;
			}
		}
	}

	public function removeCCGV(){
		// Start - CREDIT CLASS Gift Voucher Contribution
		if (Session::exists('credit_covers') === true) Session::remove('credit_covers');
		if (Session::exists('cot_gv') === true) Session::remove('cot_gv');
		// End - CREDIT CLASS Gift Voucher Contribution
	}
	
	public function __destruct(){
		unset($this->onePage);
		Session::remove('onepage');
	}
}
?>