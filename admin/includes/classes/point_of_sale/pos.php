<?php
class pointOfSale {
	public function pointOfSale(){
		global $currencies, $currency;
		$shoppingCartBase = &Session::getReference('shoppingCartBase');
		$shoppingCartBase->emptyCart();
		unset($_SESSION['shipping']);
		unset($_SESSION['payment']);
		$this->order = array();
		$this->order_totals = array();
		$this->order['info'] = array(
			'order_status'   => DEFAULT_ORDERS_STATUS_ID,
			'currency'       => $currency,
			'currency_value' => '1.0000',
			'shipping'       => array(
				'id' => ''
			)
		);
		unset($this->orderLoaded);
	}

	public function &getUserAccount(){
		$userAccount = &Session::getReference('userAccount');
		return $userAccount;
	}
	
	public function preProcessOrder(){
		global $order;
		$userAccount = Session::get('userAccount');
		$addressBook = $userAccount->plugins['addressBook'];

		/*
		* @todo: These need to be set somewhere else, during the order build process is preferred -- BEGIN --
		*/
		$this->order['info']['is_rental'] = '0';
		$this->order['info']['bill_attempts'] = '1';
		/*
		* @todo: These need to be set somewhere else, during the order build process is preferred -- END --
		*/

		$order->info = $this->order['info'];
		$order->customer = $addressBook->getAddress('customer');
		$order->billing = $addressBook->getAddress('billing');
		$order->delivery = $addressBook->getAddress('delivery');
		$order->pickup = $addressBook->getAddress('pickup');
		$order->loadProducts();
	}

	public function processPayment(){
		global $order, $paymentModules, $currencies, $orderTotalModules;
		$this->preProcessOrder();
		
		$order->info['comments'] = (!empty($_POST['comment']) ? $_POST['comment'] : false);
		$order->info['order_status'] = $_POST['status'];
		$sendOrderEmail = (isset($_POST['notify']));

		if ($paymentModules->moduleIsEnabled($_POST['payment'])){
			$module = $paymentModules->getModule($_POST['payment']);
			$order->info['payment'] = array(
				'id'    => $module->code,
				'title' => $module->title
			);

			$userAccount = &Session::getReference('userAccount');
			$order->createOrder($userAccount->getCustomerId());

			$module->paymentGatewayProcess(array(
				'orderID'            => $order->newOrder['orderID'],
				'amount'             => $_POST['paymentAmount'],
				'cardType'           => $_POST['cardType'],
				'cardOwner'          => $_POST['cardOwner'],
				'cardNumber'         => $_POST['cardNumber'],
				'cardExpMonth'       => $_POST['cardExpMonth'],
				'cardExpYear'        => $_POST['cardExpYear'],
				'invoiceDescription' => 'Order placed on behalf of customer, using administration POS system.',
				'useRedirect'        => false
			));

			echo '{' . "\n" .
			'success: true,' . "\n" .
			'redirectUrl: "' . tep_href_link('point_of_sale.php') . '",' . "\n" .
			'logDateAdded: "' . date('m/d/Y') . '",' . "\n" .
			'logGatewayMessage: "' . addslashes($module->getResponse('reasonText')) . '",' . "\n" .
			'logPaymentMethod: "' . $module->title . '",' . "\n" .
			'logPaymentAmount: "' . $currencies->format($_POST['paymentAmount']) . '",' . "\n";

			if ($module->paymentSuccessful() === false){
				echo 'errMsg: "' . addslashes($module->getResponse('reasonText')) . '"' . "\n";
			}else{
				echo 'successMsg: "' . addslashes($module->getResponse('reasonText')) . '"' . "\n";
			}
			echo '}' . "\n";

			$order->newOrder['orderTotals'] = $orderTotalModules->process();

			$order->insertOrderTotals();
			$order->insertStatusHistory();

			// initialized for the email confirmation
			$this->newOrder['productsOrdered'] = '';
			for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
				$productClass = $order->products[$i]['productClass'];
				$pID = $productClass->getID();
				$pID_string = $order->products[$i]['id_string'];

				$order->updateProductStock($order->products[$i]);
				$order->updateProductsOrdered($order->products[$i]);
				$order->insertOrderedProduct($order->products[$i]);

				// #################### Added CCGV ######################
				$orderTotalModules->update_credit_account($i);//ICW ADDED FOR CREDIT CLASS SYSTEM
				// #################### End Added CCGV ######################

				//------insert customer choosen option to order--------
				$attributes_exist = '0';
				$productsOrderedAttributes = '';
				if (isset($order->products[$i]['attributes'])) {
					$order->insertOrderedProductAttributes($order->products[$i]);
					$productsOrderedAttributes .= $order->newOrder['currentOrderedProduct']['emailAttributes'];
				}

				if (isset($order->products[$i]['reservation'])){
					$order->insertRentalBooking($order->products[$i]);
					$productsOrderedAttributes .= $order->newOrder['currentOrderedProduct']['emailReservation'];
				}

				$this->newOrder['productsOrdered'] .= $order->products[$i]['quantity'] . ' x ' .
				$productClass->getName() . ' (' . $productClass->getModel() . ') = ' .
				$currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['quantity']) .
				$productsOrderedAttributes . "\n";
			}

			// #################### Added CCGV ######################
			$orderTotalModules->apply_credit();//ICW ADDED FOR CREDIT CLASS SYSTEM
			// #################### End Added CCGV ######################

			if ($sendOrderEmail){
				$this->sendNewOrderEmail();
			}
		}
		return false;
	}

	public function sendNewOrderEmail(){
		global $order;
		$order->newOrder['productsOrdered'] = $this->newOrder['productsOrdered'];
		$order->sendNewOrderEmail();
	}

	public function addProduct($settings){
		global $shoppingCartActions, $cart, $appExtension;

		if ($settings['purchase_type'] == 'reservation'){
			$payPerRentals = $appExtension->getExtension('payPerRentals');
			$payPerRentals->addReservationProductToCart($settings);
		}else{
			$shoppingCartActions->add_product($settings['product_id'], $settings['purchase_type'], $settings['quantity']);
		}

		if (isset($settings['barcode']) && $settings['barcode'] !== false){
			$shoppingCartBase = &Session::getReference('shoppingCartBase');
			if (isset($shoppingCartBase->contents[$pID_string][$settings['purchase_type']])){
				$shoppingCartBase->contents[$pID_string][$settings['purchase_type']]['barcode'] = $settings['barcode'];
			}
		}
	}

	public function updateProduct($settings){
		$this->products[$settings['pID_string']] = $settings;
	}

	public function removeProduct($pID_string, $purchaseType){
		global $shoppingCartActions;
		$shoppingCartActions->removeProduct($pID_string, $purchaseType);
	}

	public function setShippingMethod($method){
		$ship = explode('_', $method);

		$quote = $this->getShippingQuote($ship[0], $ship[1]);
		$this->order['info']['shipping'] = array(
			'id'    => $method,
			'title' => $quote['title'],
			'cost'  => $quote['cost']
		);
	}

	public function setPaymentMethod($method){
		global $paymentModules;
		$this->payment_module = $method;
		$paymentModule = $paymentModules->getModule($method);
		$this->order['info']['payment'] = array(
			'id'    => $method,
			'title' => $paymentModule->title
		);
	}

	public function setOrdersCustomer(){
		$userAccount = &Session::getReference('userAccount');
		$userAccount->__construct($_GET['customers_id']);
		$userAccount->loadPlugins();
		$addressBook =& $userAccount->plugins['addressBook'];

		$this->order['info']['email_address'] = $userAccount->getEmailAddress();
		$this->order['info']['telephone'] = $userAccount->getTelephoneNumber();

		$this->setCustomerAddress($addressBook->getDefaultAddressId());
		$this->setSendTo($_GET['sendTo']);
		$this->setBillTo($_GET['billTo']);
		$this->setPickupFrom($_GET['pickupFrom']);

		return '{
            shippingAddressID: "' . (int)$_GET['sendTo'] . '",
            billingAddressID: "' . (int)$_GET['billTo'] . '",
            pickupAddressID: "' . (int)$_GET['pickupFrom'] . '",
            shippingAddressFormatted: "' . addslashes($addressBook->formatAddress('delivery', true)) . '",
            billingAddressFormatted: "' . addslashes($addressBook->formatAddress('billing', true)) . '",
            pickupAddressFormatted: "' . addslashes($addressBook->formatAddress('pickup', true)) . '"
        }';
	}

	public function setCustomerAddress($addressId){
		$this->customerAddress = $addressId;
		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];

		$addressBook->addAddressEntry('customer', $addressBook->getAddress($addressId));
	}

	public function setSendTo($addressId){
		$this->sendTo = $addressId;
		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];

		$addressBook->addAddressEntry('delivery', $addressBook->getAddress($addressId));
	}

	public function setBillTo($addressId){
		$this->billTo = $addressId;
		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];

		$addressBook->addAddressEntry('billing', $addressBook->getAddress($addressId));
	}

	public function setPickupFrom($addressId){
		$this->pickupFrom = $addressId;
		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];

		$addressBook->addAddressEntry('pickup', $addressBook->getAddress($addressId));
	}

	public function addOrderTotal($settings){
		$this->orderTotals[$settings['class']] = $settings;
	}

	public function updateOrderTotal($settings){
		$this->orderTotals[$settings['class']] = $settings;
	}

	public function addOrdercomments($comments){
		if ($this->comments == ''){
			$this->comments = addslashes($comments);
		}else{
			if (is_array($this->comments)){
				$this->comments[] = addslashes($comments);
			}else{
				$orig = $this->comments;
				$this->comments = array();
				$this->comments[] = $orig;
				$this->comments[] = addslashes($comments);
			}
		}
	}

	public function getShippingQuote($module, $method){
		global $currencies, $cart, $order, $shippingModules;
		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];

		$order->billing = $addressBook->getAddress('billing');
		$order->delivery = $addressBook->getAddress('delivery');

		$mInfo = array($module, $method);
		if ($mInfo[0] == 'free'){
			$quotes[0]['methods'][0] = array(
				'id'    => 'free',
				'title' => 'Free Shipping',
				'cost'  => 0.00,
				'days'  => 0
			);
		}else{
			$quotes = $shippingModules->quote($method, $module);
		}
		return array(
			'id'    => $quotes[0]['methods'][0]['id'],
			'title' => $quotes[0]['methods'][0]['title'],
			'cost'  => $quotes[0]['methods'][0]['cost'],
			'days'  => (isset($quotes[0]['methods'][0]['days']) ? $quotes[0]['methods'][0]['days'] : 0)
		);
	}

	public function saveCustomerAddress(){
		if ($_GET['address_book_id'] != 'false'){
			$addressID = $_GET['address_book_id'];
			$orderAddressKey = $_GET['order_address_key'];
		}

		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];

		$dataArray = array(
			'entry_gender'         => (isset($_GET['gender']) ? $_GET['gender'] : 'm'),
			'entry_company'        => $_GET['company'],
			'entry_firstname'      => $_GET['firstname'],
			'entry_lastname'       => $_GET['lastname'],
			'entry_street_address' => $_GET['street_address'],
			'entry_suburb'         => (isset($_GET['suburb']) ? $_GET['suburb'] : ''),
			'entry_postcode'       => $_GET['postcode'],
			'entry_city'           => $_GET['city'],
			'entry_state'          => $_GET['state'],
			'entry_country_id'     => (int)$_GET['country'],
			'entry_zone_id'        => (int)(isset($_GET['zone_id']) ? $_GET['zone_id'] : $addressBook->getStateZoneId($_GET['country'], $_GET['state'])),
			'inventory_center_id' => (int)(isset($_GET['inventory_center']) ? $_GET['inventory_center'] : 0)
		);

		if (isset($addressID)){
			if (isset($_GET['updateAddressBook'])){
				$addressBook->updateAddress($addressID, $dataArray);

				$updates = array();
				if ($addressID == $this->sendTo && $orderAddressKey != 'delivery'){
					$updates[] = 'delivery';
				}
				if ($addressID == $this->billTo && $orderAddressKey != 'billing'){
					$updates[] = 'billing';
				}
				if ($addressID == $this->pickupFrom && $orderAddressKey != 'pickup'){
					$updates[] = 'pickup';
				}
				if (sizeof($updates) > 0){
					foreach($updates as $key){
						if ($addressBook->entryExists($key) === true){
							$addressBook->updateAddressEntry($key, $dataArray);
						}else{
							$addressBook->addAddressEntry($key, $dataArray);
						}
					}
				}
			}

			if ($addressBook->entryExists($orderAddressKey) === true){
				$addressBook->updateAddressEntry($orderAddressKey, $dataArray);
			}else{
				$addressBook->addAddressEntry($orderAddressKey, $dataArray);
			}
		}elseif (!isset($addressID) && !isset($_GET['updateAddressBook'])){
			$addressID = $addressBook->insertAddress($dataArray);
		}
		return $addressID;
	}

	public function processOrderTotals(){
		global $order, $cart, $orderTotalModules, $total_count;

		$shipping = $this->order['info']['shipping'];

		$this->order_totals = array();
		$order->loadProducts();
		if ($orderTotalModules->modulesAreInstalled() === true) {
			$totalsIndex = 0;
			foreach($orderTotalModules->getModuleClasses() as $moduleName => $moduleClass){
				if ($orderTotalModules->moduleIsEnabled($moduleName) === true){
					$moduleClass->process();
					$moduleOutput = $moduleClass->output;
					for ($i=0, $n=sizeof($moduleOutput); $i<$n; $i++) {
						if (tep_not_null($moduleOutput[$i]['title']) && tep_not_null($moduleOutput[$i]['text'])) {
							$this->order_totals[$totalsIndex] = array(
								'code'       => $moduleClass->code,
								'title'      => $moduleOutput[$i]['title'],
								'text'       => $moduleOutput[$i]['text'],
								'value'      => $moduleOutput[$i]['value'],
								'sort_order' => $totalsIndex
							);
							if ($moduleClass->code == 'ot_subtotal'){
								$subtotalIndex = $totalsIndex;
							}
							if ($moduleClass->code == 'ot_total'){
								$totalIndex = $totalsIndex;
							}
							$totalsIndex++;
						}
					}
				}
			}
		}

		$customTotals = array();
		if (isset($this->orderID) && !isset($_GET['customTotal'])){
			$Qcheck = tep_db_query('select * from ' . TABLE_ORDERS_TOTAL . ' where orders_id = "' . $this->orderID . '" and class = "CustomTotal"');
			while($check = tep_db_fetch_array($Qcheck)){
				$customTotals[$check['sort_order']] = array(
					'code'       => $check['class'],
					'text'       => $check['title'],
					'value'      => $check['value'],
					'sort_order' => $check['sort_order']
				);
			}
		}else{
			$customTotals = (isset($_GET['customTotal']) && is_array($_GET['customTotal']) ? $_GET['customTotal'] : array());
		}

		if (sizeof($customTotals) > 0){
			global $currencies;
			if (!empty($this->order_totals)){
				foreach($customTotals as $index => $array){
					if (isset($subtotalIndex) && $subtotalIndex >= $index){
						$order->info['subtotal'] += $array['value'];
						$this->order_totals[$subtotalIndex]['value'] = $order->info['subtotal'];
						$this->order_totals[$subtotalIndex]['text'] = $currencies->format($order->info['subtotal'], true, $order->info['currency'], $order->info['currency_value']);
					}

					if (isset($totalIndex) && $totalIndex >= $index){
						$order->info['total'] += $array['value'];
						$this->order_totals[$totalIndex]['value'] = $order->info['total'];
						$this->order_totals[$totalIndex]['text'] = '<b>' . $currencies->format($order->info['total'], true, $order->info['currency'], $order->info['currency_value']) . '</b>';
					}
				}

				$newTotals = array();
				$newIndex = 0;
				foreach($this->order_totals as $index => $array){
					if (isset($customTotals[$index])/* && !empty($customTotals[$index]['text']) && !empty($customTotals[$index]['value'])*/){
						$newTotals[$newIndex] = array(
							'code'       => 'CustomTotal',
							'title'      => $customTotals[$index]['text'],
							'text'       => $customTotals[$index]['value'],
							'value'      => $customTotals[$index]['value'],
							'sort_order' => $newIndex
						);
						$newIndex++;
						if ($this->order_totals[$index]['text'] != $customTotals[$index]['text']){
							$this->order_totals[$index]['sort_order'] = $newIndex;
							$newTotals[$newIndex] = $this->order_totals[$index];
							$newIndex++;
						}
						unset($customTotals[$index]);
					}else{
						$newTotals[$newIndex] = $this->order_totals[$index];
						$newIndex++;
					}
				}
				if (!empty($customTotals)){
					foreach($customTotals as $index => $array){
						//if (!empty($customTotals[$index]['text']) && !empty($customTotals[$index]['value'])){
						$newTotals[$newIndex] = array(
							'code'       => 'CustomTotal',
							'title'      => $array['text'],
							'text'       => $array['value'],
							'value'      => $array['value'],
							'sort_order' => $newIndex
						);
						$newIndex++;
						// }
					}
				}

				$this->order_totals = array();
				$tIndex = 0;
				reset($newTotals);
				foreach($newTotals as $tInfo){
					$this->order_totals[$tIndex] = array(
						'code'       => $tInfo['code'],
						'title'      => $tInfo['title'],
						'text'       => $tInfo['text'],
						'value'      => $tInfo['value'],
						'sort_order' => $tIndex
					);
					$tIndex++;
				}
			}
		}
	}

	public function getMethods($methodType, $returnArray = false){
		global $shippingModules, $paymentModules, $order;
		if ($methodType == 'shipping'){
			$moduleClass = $shippingModules;
		}elseif ($methodType == 'payment'){
			$moduleClass = $paymentModules;
		}

		if ($returnArray === true){
			$modulesArray = $moduleClass->getDropMenuArray();
			return $modulesArray;
		}else{
			$userAccount = &Session::getReference('userAccount');
			$addressBook =& $userAccount->plugins['addressBook'];

			$order->billing = $addressBook->getAddress('billing');
			$order->delivery = $addressBook->getAddress('delivery');

			if ($methodType == 'shipping'){
				return pointOfSaleHTML::getShippingQuotesTable();
			}else{
				return pointOfSaleHTML::outputPaymentMethods();
			}
		}
	}

	public function checkServiceAvailability(){
		if (sysConfig::get('EXTENSION_INVENTORY_CENTERS_ENABLED') != 'True') return;

		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];
		if (isset($_POST['street_address'])){
			$centerId = $addressBook->getAddressInventoryCenter(array(
				'entry_state'          => $_POST['state'],
				'entry_zone_id'        => $_POST['state'],
				'entry_country_id'     => $_POST['country'],
				'entry_street_address' => $_POST['street_address'],
				'entry_city'           => $_POST['city'],
				'entry_postcode'       => $_POST['postcode']
			));
		}else{
			$centerId = $addressBook->getAddressInventoryCenter($_POST['pickupFrom']);
		}
		if ($centerId === false){
			return false;
		}else{
			return $centerID;
		}
	}

	public function createCustomerAccount(){
		if (isset($_POST['passAutoGen'])){
			$password = tep_create_random_value(8);
		}else{
			$password = $_POST['customer_password'];
		}
		
		$userAccount = &Session::getReference('userAccount');
		$addressBook =& $userAccount->plugins['addressBook'];
		
		$userAccount->setFirstName($_POST['firstname']);
		$userAccount->setLastName($_POST['lastname']);
		$userAccount->setEmailAddress($_POST['customer_email']);
		$userAccount->setPassword(tep_encrypt_password($password));
		$userAccount->setNewsletter($_POST['newsletter']);
		$userAccount->setFaxNumber($_POST['fax']);
		$userAccount->setTelephoneNumber($_POST['telephone']);
		
		if (ACCOUNT_GENDER == 'true') $userAccount->setGender($_POST['gender']);
		if (ACCOUNT_DOB == 'true') $userAccount->setDateOfBirth(tep_date_raw($_POST['dob']));

		$customerId = $userAccount->createNewAccount();
		
		$dataArray = array(
			'entry_firstname'      => $_POST['firstname'],
			'entry_lastname'       => $_POST['lastname'],
			'entry_street_address' => $_POST['street_address'],
			'entry_postcode'       => $_POST['postcode'],
			'entry_city'           => $_POST['city'],
			'entry_country_id'     => $_POST['country'],
			'entry_zone_id'        => $addressBook->getStateZoneId($_POST['country'], $_POST['state']),
			'entry_state'          => $_POST['state']
		);

		if (ACCOUNT_GENDER == 'true') $dataArray['entry_gender'] = $_POST['gender'];
		if (ACCOUNT_COMPANY == 'true') $dataArray['entry_company'] = $_POST['company'];
		if (ACCOUNT_SUBURB == 'true') $dataArray['entry_suburb'] = $_POST['suburb'];

		$addressId = $addressBook->insertAddress($dataArray, true);
		
		Doctrine_Query::create()
		->update('Customers')
		->set('customers_default_address_id', '?', $addressId)
		->where('customers_id = ?', $customerId)
		->execute();
		
		Doctrine_Query::create()
		->update('CustomersMembership')
		->set('rental_address_id', '?', $addressId)
		->where('customers_id = ?', $customerId)
		->execute();

		return $customerId;
	}
}
?>