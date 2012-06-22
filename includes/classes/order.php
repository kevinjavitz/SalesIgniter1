<?php
/*
$Id: order.php,v 1.33 2003/06/09 22:25:35 hpdl Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2003 osCommerce

Released under the GNU General Public License
*/

class OrderProcessor {
	public $info,
	$totals,
	$products,
	$content_type;

	public function __construct($order_id = '') {
		if ($order_id !== false){
			$this->info = array();
			$this->totals = array();
			$this->products = array();
			$this->products_ordered = '';
			if ($order_id != '') {
				$this->orderId = $order_id;
				$this->query($this->orderId);
				$this->posMode = 'edit';
			} else {
				$this->cart();
				$this->posMode = 'new';
			}
		}
	}

	public function __call($function, $args){
		return EventManager::notifyWithReturn('orderClassFunction_' . $function, $args);
	}

	private function &getUserAccount(){
		global $userAccount;
		if (Session::exists('userAccount') === true){
			$userAccount = &Session::getReference('userAccount');
		}
		return $userAccount;
	}

	public function query($order_id) {
		global $currencies;
		$Qorder = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.OrdersAddresses oa')
			->leftJoin('oa.Zones z')
			->leftJoin('oa.Countries c')
			->leftJoin('o.OrdersTotal ot')
			->leftJoin('o.OrdersPaymentsHistory oph')
			->leftJoin('o.OrdersStatusHistory osh')
			->leftJoin('osh.OrdersStatus s')
			->leftJoin('s.OrdersStatusDescription sd')
			->leftJoin('o.OrdersProducts op')
			->where('o.orders_id = ?', $order_id)
			->andWhere('sd.language_id = ?', Session::get('languages_id'));

		EventManager::notify('OrderQueryBeforeExecute', &$Qorder);

		$Orders = $Qorder->execute()->toArray();
		if(isset($Orders[0])){
			$Order = $Orders[0];
		}else{
			return false;
		}

		//echo '<pre>';print_r($Order);
		$userAccount = &$this->getUserAccount();
		$addressBook =& $userAccount->plugins['addressBook'];

		$OrdersAddresses = $Order['OrdersAddresses'];
		foreach($OrdersAddresses as $address){
			$address['entry_zone_id'] = isset($address['Zones']['zone_id'])?$address['Zones']['zone_id']:0;
			$address['entry_country_id'] = isset($address['Countries']['countries_id'])?$address['Countries']['countries_id']:0;

			$addressBook->addAddressEntry($address['address_type'], $address);
		}

		$OrdersTotal = $Order['OrdersTotal'];
		foreach($OrdersTotal as $tInfo){
			$this->totals[] = array(
				'title' => $tInfo['title'],
				'text'  => $tInfo['text']
			);
			/*
			 * @TODO: Change to only look for "total" after a while, when client upgrades will no longer be affected
			 */
			if ($tInfo['module_type'] == 'ot_total' || $tInfo['module_type'] == 'total'){
				$orderTotal = strip_tags($tInfo['text']);
			}elseif ($tInfo['module_type'] == 'ot_shipping' || $tInfo['module_type'] == 'shipping'){
				$shippingMethod = strip_tags($tInfo['title']);
			}
		}

		$this->info = array(
			'customers_id'     => $Order['customers_id'],
			'email_address'    => $Order['customers_email_address'],
			'telephone'        => $Order['customers_telephone'],
			'currency'         => $Order['currency'],
			'currency_value'   => $Order['currency_value'],
			'payment_method'   => $Order['payment_module'],
			'date_purchased'   => $Order['date_purchased'],
			'orders_status'    => $Order['OrdersStatusHistory'][sizeof($Order['OrdersStatusHistory'])-1]['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'],
			'orders_status_id' => $Order['OrdersStatusHistory'][sizeof($Order['OrdersStatusHistory'])-1]['OrdersStatus']['orders_status_id'],
			'last_modified'    => $Order['last_modified'],
			'comments'		   =>  $Order['OrdersStatusHistory'][sizeof($Order['OrdersStatusHistory'])-1]['comments'],
			'total'            => $orderTotal,
			'bill_attempts'    => $Order['bill_attempts'],
			//Package Tracking Plus BEGIN
			'usps_track_num'   => $Order['usps_track_num'],
			'usps_track_num2'  => $Order['usps_track_num2'],
			'ups_track_num'    => $Order['ups_track_num'],
			'ups_track_num2'   => $Order['ups_track_num2'],
			'fedex_track_num'  => $Order['fedex_track_num'],
			'fedex_track_num2' => $Order['fedex_track_num2'],
			'dhl_track_num'    => $Order['dhl_track_num'],
			'dhl_track_num2'   => $Order['dhl_track_num2'],
			//Package Tracking Plus END
			'shipping_method'  => ''
		);

		if (isset($shippingMethod)){
			if (substr($shippingMethod, -1) == ':'){
				$this->info['shipping_method'] = substr($shippingMethod, 0, -1);
			}else{
				$this->info['shipping_method'] = $shippingMethod;
			}
		}

		$index = 0;
		$OrdersProducts = $Order['OrdersProducts'];
		foreach($OrdersProducts as $pInfo){
			$productClass = new product($pInfo['products_id'], $pInfo['purchase_type']);
			$this->products[$index] = array(
				'productClass'   => $productClass,
				'opID'           => $pInfo['orders_products_id'],
				'quantity'       => $pInfo['products_quantity'],
				'id'             => $pInfo['products_id'],
				'name'           => $pInfo['products_name'],
				'model'          => $pInfo['products_model'],
				'tax'            => $pInfo['products_tax'],
				'date_available' => $pInfo['products_date_available'],
				'price'          => $pInfo['products_price'],
				'final_price'    => $pInfo['final_price'],
				'purchase_type'  => $pInfo['purchase_type']
			);

			if(isset($pInfo['OrdersProductsDownload'])){
				$OrdersProductsDownload = $pInfo['OrdersProductsDownload'];
				if (sizeof($OrdersProductsDownload) > 0){
					$this->products[$index]['download_type'] = 'download';
				}
			}
			if(isset($pInfo['OrdersProductsStream'])){
				$OrdersProductsStream = $pInfo['OrdersProductsStream'];
				if (sizeof($OrdersProductsStream) > 0){
					$this->products[$index]['download_type'] = 'stream';
				}
			}

			EventManager::notify('OrderClassQueryFillProductArray', &$pInfo, &$this->products[$index]);

			$this->info['tax_groups']["{$this->products[$index]['tax']}"] = '';

			$index++;
		}
		if(!class_exists('Order')){
			require(sysConfig::getDirFsCatalog() . 'includes/classes/Order/Base.php');
		}
		$MyOrder = new Order($order_id);

		$orderedProducts = '<table>';
		foreach($MyOrder->getProducts() as $OrderProduct){
			$orderedProducts .= '<tr>' . "\n" .
				'<td class="main" align="right" valign="top" width="30">' . $OrderProduct->getQuantity() . '&nbsp;x</td>' . "\n" .
				'<td class="main" valign="top">' . $OrderProduct->getNameHtml();

			$orderedProducts .= '</td>' . "\n";

			if ($MyOrder->hasTaxes() === true) {
				$orderedProducts .= '<td class="main" valign="top" align="right">' . tep_display_tax_value($OrderProduct->getTaxRate()) . '%</td>' . "\n";
			}

			$orderedProducts .= '<td class="main" align="right" valign="top">' . $currencies->format($OrderProduct->getFinalPrice(true, true)) . '</td>' . "\n" .
				'</tr>' . "\n";
		}
		$orderedProducts .= '</table>';
		$this->products_ordered = $orderedProducts;

		$OrdersStatusHistory = $Order['OrdersStatusHistory'];
		$this->statusHistory = array();
		foreach($OrdersStatusHistory as $shInfo){
			$this->statusHistory[] = array(
				'customer_notified'  => $shInfo['customer_notified'],
				'orders_status_name' => $shInfo['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'],
				'date_added'         => $shInfo['date_added'],
				'comments'           => $shInfo['comments']
			);
		}

		$OrdersPaymentHistory = $Order['OrdersPaymentsHistory'];
		$this->paymentHistory = array();
		foreach($OrdersPaymentHistory as $phInfo){
			$this->paymentHistory[] = array(
				'card_details'    => $phInfo['card_details'],
				'date_added'      => $phInfo['date_added'],
				'payment_method'  => $phInfo['payment_method'],
				'gateway_message' => $phInfo['gateway_message'],
				'payment_amount'  => $phInfo['payment_amount']
			);
		}

		EventManager::notify('OrderSingleLoad', &$this, &$Order);
	}

	public function getOrdersAddress($aID){
		$userAccount = &$this->getUserAccount();
		$addressBook =& $userAccount->plugins['addressBook'];

		return $addressBook->getAddress($aID);
	}

	public function getProductsOrdered(){
		return $this->products_ordered;
	}

	public function getPaymentMethodHistory(){
		$methods = array();
		foreach($this->paymentHistory as $payment){
			$methods[] = $payment['payment_method'];
		}
		return implode(', ', $methods);
	}

	public function cart() {
		global $ShoppingCart, $currencies;

		$this->content_type = $ShoppingCart->getContentType();

		$this->taxAddress = ($this->content_type == 'virtual' ? 'billing' : 'delivery');

		$this->info = array(
			'order_status'    => sysConfig::get('DEFAULT_ORDERS_STATUS_ID'),
			'currency'        => Session::get('currency'),
			'currency_value'  => $currencies->currencies[Session::get('currency')]['value'],
			'shipping_method' => '',
			'shipping_cost'   => 0,
			'payment'         => array('id' => '', 'text' => ''),
			'payment_method'  => '',
			'payment_module'  => '',
			'subtotal'        => 0,
			'tax'             => 0,
			'tax_groups'      => array(),
			'comments'        => (Session::exists('comments') === true ? Session::get('comments') : ''),
			'bill_attempts'   => 0
		);

		$this->loadShippingInfo();
		$this->loadPaymentInfo();
		$this->loadOrderInfo();
	}

	public function loadShippingInfo(){
		global $shippingModules, $onePageCheckout;
		if (isset($shippingModules) && isset($onePageCheckout) && array_key_exists('module', $onePageCheckout->onePage['info']['shipping'])){
			$this->info['shipping'] = $onePageCheckout->onePage['info']['shipping'];
			if ($shippingModules->moduleIsLoaded($this->info['shipping']['module'])){
				$this->info['shipping_module'] = $this->info['shipping']['id'];
				$this->info['shipping_method'] = $this->info['shipping']['title'];
				$this->info['shipping_cost'] = $this->info['shipping']['cost'];
			}
		}
	}

	public function loadPaymentInfo(){
		global $paymentModules;
		if (isset($paymentModules) && Session::exists('payment') === true){
			$this->info['payment'] = Session::get('payment');
			if ($paymentModules->moduleIsLoaded($this->info['payment']['id'])) {
				$this->info['payment_module'] = $this->info['payment']['id'];
				$this->info['payment_method'] = $this->info['payment']['title'];

				$paymentModule = $paymentModules->getModule($this->info['payment']['id']);
				if (isset($paymentModule->order_status) && is_numeric($paymentModule->order_status) && $paymentModule->order_status > 0){
					$this->info['order_status'] = $paymentModule->order_status;
				}
			}
		}
	}

	public function loadOrderInfo(){
		global $ShoppingCart;
		$this->info['subtotal'] = 0;
		$this->info['tax'] = 0;
		$this->info['tax_groups'] = array();

		foreach($ShoppingCart->getProducts() as $cartProduct) {
			$shownPrice = $cartProduct->getFinalPrice(true) * $cartProduct->getQuantity();
			$this->info['subtotal'] += $shownPrice;

			$tax = $cartProduct->getTaxRate();
			$taxDesc = $cartProduct->getTaxDescription();
			if (sysConfig::get('DISPLAY_PRICE_WITH_TAX') == 'true'){
				$priceNoTax = ($shownPrice / (($tax < 10) ? "1.0" . str_replace('.', '', $tax) : "1." . str_replace('.', '', $tax)));
				$this->info['tax'] += $shownPrice - $priceNoTax;
				if (isset($this->info['tax_groups'][$taxDesc])) {
					$this->info['tax_groups'][$taxDesc] += $shownPrice - $priceNoTax;
				}else{
					$this->info['tax_groups'][$taxDesc] = $shownPrice - $priceNoTax;
				}
			}else{
				$this->info['tax'] += ($tax / 100) * $shownPrice;
				if (isset($this->info['tax_groups'][$taxDesc])) {
					$this->info['tax_groups'][$taxDesc] += ($tax / 100) * $shownPrice;
				}else{
					$this->info['tax_groups'][$taxDesc] = ($tax / 100) * $shownPrice;
				}
			}
		}
		//print_r($this->info);
		if (sysConfig::get('DISPLAY_PRICE_WITH_TAX') == 'true'){
			$this->info['total'] = $this->info['subtotal'];
		}else{
			$this->info['total'] = $this->info['subtotal'] + $this->info['tax'];
		}
	}

	public function getBillingAttempts(){
		if (!isset($this->info['bill_attempts'])){
			$this->info['bill_attempts'] = 0;
		}
		return $this->info['bill_attempts'];
	}

	public function updateBillingAttempts(){
		$this->info['bill_attempts'] += 1;
		$Qupdate = Doctrine_Query::create()
			->update('Orders')
			->set('bill_attempts', '?', $this->info['bill_attempts'])
			->where('orders_id = ?', $this->orderId)
			->execute();
	}

	public function createOrder($cID = false){
		global $onePageCheckout;

		EventManager::notify('InsertOrderPreStart');

		$userAccount = &$this->getUserAccount();
		$customersId = ($cID !== false ? $cID : $userAccount->getCustomerId());
		$newOrder = new Orders();
		$newOrder->customers_id = $customersId;
		$newOrder->customers_telephone = $userAccount->getTelephoneNumber();
		$newOrder->customers_email_address = $userAccount->getEmailAddress();
		$newOrder->shipping_module = (isset($this->info['shipping_module']) ? $this->info['shipping_module'] : '');
		$newOrder->payment_module = $this->info['payment_module'];
		$newOrder->orders_status = (int)sysConfig::get('DEFAULT_ORDERS_STATUS_ID');
		$newOrder->currency = $this->info['currency'];
		$newOrder->currency_value = (float)$this->info['currency_value'];
		$newOrder->bill_attempts = (isset($this->info['bill_attempts']) ? $this->info['bill_attempts'] : 1);
		$newOrder->ip_address = $_SERVER['REMOTE_ADDR'];
		EventManager::notify('NewOrderBeforeSave', &$this, &$newOrder);

		$newOrder->save();

		$this->newOrder['orderID'] = $newOrder->orders_id;

		if (isset($onePageCheckout) && is_object($onePageCheckout)){
			$onePageCheckout->onePage['info']['order_id'] = $this->newOrder['orderID'];
		}

		$addressBook = $userAccount->plugins['addressBook'];
		//print_r($addressBook);
		$customerAddress = $addressBook->getAddress('customer');
		if (empty($customerAddress) || empty($customerAddress['entry_firstname'])){
			$customerAddress = $addressBook->getAddress('billing');
		}

		$billingAddress = $addressBook->getAddress('billing');
		$deliveryAddress = $addressBook->getAddress('delivery');
		$pickupAddress = $addressBook->getAddress('pickup');

		if (empty($deliveryAddress)){
			$deliveryAddress = $billingAddress;
		}

		if (empty($pickupAddress)){
			if (empty($deliveryAddress)){
				$pickupAddress = $billingAddress;
			}else{
				$pickupAddress = $deliveryAddress;
			}
		}
		$this->insertOrdersAddress($customerAddress, 'customer');
		$this->insertOrdersAddress($deliveryAddress, 'delivery');
		$this->insertOrdersAddress($billingAddress, 'billing');
		$this->insertOrdersAddress($pickupAddress, 'pickup');
	}

	public function insertOrdersAddress($address, $type){
		$userAccount = &$this->getUserAccount();
		$countryInfo = $userAccount->plugins['addressBook']->getCountryInfo($address['entry_country_id']);
		$newOrderAddress = new OrdersAddresses();
		$newOrderAddress->orders_id = $this->newOrder['orderID'];
		if (isset($address['entry_name'])){
			$newOrderAddress->entry_name = $address['entry_name'];
		}else{
			$newOrderAddress->entry_name = $address['entry_firstname'] . ' ' . $address['entry_lastname'];
		}
		$newOrderAddress->entry_company = $address['entry_company'];
		$newOrderAddress->entry_street_address = $address['entry_street_address'];
		$newOrderAddress->entry_suburb = $address['entry_suburb'];
		$newOrderAddress->entry_city = $address['entry_city'];
		$newOrderAddress->entry_postcode = $address['entry_postcode'];
		$newOrderAddress->entry_state = $address['entry_state'];
		if(isset($address['entry_vat'])){
			$newOrderAddress->entry_vat = $address['entry_vat'];
		}
		if(isset($address['entry_cif'])){
			$newOrderAddress->entry_cif = $address['entry_cif'];
		}
		if(isset($address['entry_city_birth'])){
			$newOrderAddress->entry_city_birth = $address['entry_city_birth'];
		}
		$newOrderAddress->entry_country = $countryInfo['countries_name'];
		$newOrderAddress->entry_format_id = $countryInfo['AddressFormat']['address_format_id'];
		$newOrderAddress->address_type = $type;
		$newOrderAddress->save();
	}

	public function insertOrderTotals(){
		$orderTotals = $this->newOrder['orderTotals'];
		for ($i=0, $n=sizeof($orderTotals); $i<$n; $i++) {
			$newOrdersTotal = new OrdersTotal();
			$newOrdersTotal->orders_id = $this->newOrder['orderID'];
			$newOrdersTotal->title = $orderTotals[$i]['title'];
			$newOrdersTotal->text = $orderTotals[$i]['text'];
			$newOrdersTotal->value = $orderTotals[$i]['value'];
			$newOrdersTotal->module_type = $orderTotals[$i]['code'];
			$newOrdersTotal->module = $orderTotals[$i]['module'];
			$newOrdersTotal->method = $orderTotals[$i]['method'];
			$newOrdersTotal->sort_order = $orderTotals[$i]['sort_order'];
			$newOrdersTotal->save();
		}
	}

	public function insertStatusHistory($historyArray = false){
		$orders_id = $this->newOrder['orderID'];
		$customer_notification = (sysConfig::get('SEND_EMAILS') == 'true') ? '1' : '0';
		$status = $this->info['order_status'];
		$comments = $this->info['comments'];

		if ($historyArray !== false){
			if (isset($historyArray['orders_id'])){
				$orders_id = $historyArray['orders_id'];
			}

			if (isset($historyArray['orders_status_id'])){
				$status = $historyArray['orders_status_id'];
			}

			if (isset($historyArray['customer_notified'])){
				$customer_notification = $historyArray['customer_notified'];
			}

			if (isset($historyArray['comments'])){
				$comments = $historyArray['comments'];
			}
		}

		$newOrdersStatusHistory = new OrdersStatusHistory();
		$newOrdersStatusHistory->orders_id = $orders_id;
		$newOrdersStatusHistory->orders_status_id = $status;
		$newOrdersStatusHistory->customer_notified = $customer_notification;
		$newOrdersStatusHistory->comments = $comments;
		$newOrdersStatusHistory->save();
	}

	public function insertOrderedProduct($cartProduct, &$products_ordered){
		global $currencies;
		$this->newOrder['currentOrderedProduct'] = array();

		$products_ordered .= sprintf("%s x %s (%s) = %s\n",
			$cartProduct->getQuantity(),
			$cartProduct->getName(),
			$cartProduct->getModel(),
			$currencies->display_price(
				$cartProduct->getFinalPrice(),
				$cartProduct->getTaxRate(),
				$cartProduct->getQuantity()
			)
		);

		$newOrdersProduct = new OrdersProducts();
		$newOrdersProduct->orders_id = $this->newOrder['orderID'];
		$newOrdersProduct->products_id = (int)$cartProduct->getIdString();
		$newOrdersProduct->products_model = $cartProduct->getModel();
		$newOrdersProduct->products_name = $cartProduct->getName();
		$newOrdersProduct->products_price = $cartProduct->getPrice();
		$newOrdersProduct->final_price = $cartProduct->getFinalPrice();
		$newOrdersProduct->products_tax = $cartProduct->getTaxRate();
		$newOrdersProduct->products_quantity = $cartProduct->getQuantity();
		$newOrdersProduct->purchase_type = $cartProduct->getPurchaseType();

		EventManager::notify('InsertOrderedProductBeforeSave', &$newOrdersProduct, &$cartProduct);

		$newOrdersProduct->save();

		EventManager::notify('InsertOrderedProductAfterSave', &$newOrdersProduct, &$cartProduct);

		$cartProduct->onInsertOrderedProduct($this->newOrder['orderID'], &$newOrdersProduct, &$products_ordered);

		$this->newOrder['currentOrderedProduct']['id'] = $newOrdersProduct->orders_products_id;
		$this->updateProductsOrdered($cartProduct);
		$this->updateProductStock(&$cartProduct);
	}

	public function insertMembershipProduct($pInfo, &$products_ordered){
		global $currencies;
		$products_ordered .= sprintf("%s x %s (%s) = %s\n",
			$pInfo['quantity'],
			$pInfo['name'],
			$pInfo['model'],
			$currencies->display_price(
				$pInfo['final_price'],
				$pInfo['tax'],
				$pInfo['quantity']
			)
		);

		$newOrdersProduct = new OrdersProducts();
		$newOrdersProduct->orders_id = $this->newOrder['orderID'];
		$newOrdersProduct->products_id = $pInfo['id'];
		$newOrdersProduct->products_model = $pInfo['model'];
		$newOrdersProduct->products_name = $pInfo['name'];
		$newOrdersProduct->products_price = $pInfo['price'];
		$newOrdersProduct->final_price = $pInfo['final_price'];
		$newOrdersProduct->products_tax = $pInfo['tax'];
		$newOrdersProduct->products_quantity = $pInfo['quantity'];
		$newOrdersProduct->purchase_type = $pInfo['purchase_type'];
		$newOrdersProduct->save();
	}

	public function updateProductsOrdered($cartProduct){
		Doctrine_Query::create()
			->update('Products')
			->set('products_ordered', 'products_ordered + ' . sprintf('%d', $cartProduct->getQuantity()))
			->where('products_id = ?', ($cartProduct->getModel() == 'rental_package' ? 0 : (int)$cartProduct->getIdString()))
			->execute();
	}

	public function updateProductStock(&$cartProduct){
		global $ShoppingCart;
		$purchaseTypeCls = $cartProduct->purchaseTypeClass;

		$purchaseTypeCls->updateStock(
			$this->newOrder['orderID'],
			(int)$this->newOrder['currentOrderedProduct']['id'],
			&$cartProduct
		);

		/* @TODO: Get into the package products extension */
		/*if ($cartProduct->productClass->hasPackageProducts() === true){
			$products = $cartProduct->productClass->getPackageProducts();
			foreach($products as $pInfo){
				$productClass = $pInfo['productClass'];
				$purchaseTypeCls = $productClass->getPurchaseType($pInfo['purchase_type']);
				$trackMethod = $purchaseTypeCls->getTrackMethod();
				$totalQty = ($pInfo['packageQuantity'] * $product['quantity']);
				if ($trackMethod == 'barcode'){
					$stockID = $purchaseTypeCls->updateStock(array(
						'purchase_type' => 'reservation',
						'quantity' => $totalQty
					));
				}elseif ($trackMethod == 'quantity'){
					$purchaseTypeCls->updateStock(array(
						'purchase_type' => 'reservation',
						'quantity' => $totalQty
					));
				}
			}
		}*/
	}

	public function sendNewOrderEmail(){
		global $appExtension, $paymentModules, $products_ordered, $order_has_streaming_or_download;
		$userAccount = &$this->getUserAccount();
		$addressBook =& $userAccount->plugins['addressBook'];
		$sendToFormatted = $addressBook->formatAddress('delivery', true);
		$billToFormatted = $addressBook->formatAddress('billing', true);

		$emailEvent = new emailEvent('order_success', $userAccount->getLanguageId());
		$emailEvent->setVar('order_id', (isset($this->newOrder['orderID'])?$this->newOrder['orderID']:$this->orderId));
		$emailEvent->setVar('invoice_link', itw_app_link('order_id=' . (isset($this->newOrder['orderID'])?$this->newOrder['orderID']:$this->orderId), 'account', 'history_info', 'SSL', false));
		$emailEvent->setVar('date_ordered', strftime(sysLanguage::getDateFormat('long')));
		$emailEvent->setVar('full_name', $userAccount->getFullName());
		$emailEvent->setVar('ordered_products', (isset($this->newOrder['productsOrdered']) ? $this->newOrder['productsOrdered'] : ((isset($products_ordered)&&(!empty($products_ordered)))?$products_ordered:$this->products_ordered) ));
		$emailEvent->setVar('billing_address', $billToFormatted);
		if(sysConfig::get('ONEPAGE_CHECKOUT_SHIPPING_ADDRESS') == 'true'){
			$emailEvent->setVar('shipping_address', $sendToFormatted);
		}
		if($order_has_streaming_or_download){
			$emailEvent->setVar('order_has_streaming_or_download', sysLanguage::get('TEXT_ORDER_SUCCESS_EMAIL_STREAM_OR_DOWNLOAD'));
		}
		if (sysConfig::get('ONEPAGE_CHECKOUT_PICKUP_ADDRESS') == 'true'){
			$pickUpFormatted = $addressBook->formatAddress('pickup');
			$emailEvent->setVar('pickup_address', $pickUpFormatted);
		}
		$emailEvent->setVar('order_comments', $this->info['comments']);

		$orderTotalsTitle = '';
		$orderTotalsValue = '';
		$totalVal = '';
		if (isset($this->newOrder['orderTotals'])){
			for ($i=0, $n=sizeof($this->newOrder['orderTotals']); $i<$n; $i++) {
				if(strpos(strtolower($this->newOrder['orderTotals'][$i]['title']),'total') === false && strpos(strtolower($this->newOrder['orderTotals'][$i]['title']),'sub-total') === false){
					$orderTotalsTitle .= strip_tags($this->newOrder['orderTotals'][$i]['title'])  . "<br/>";
				}
			}
			for ($i=0, $n=sizeof($this->newOrder['orderTotals']); $i<$n; $i++) {
				if(strpos(strtolower($this->newOrder['orderTotals'][$i]['title']),'total') === false && strpos(strtolower($this->newOrder['orderTotals'][$i]['title']),'sub-total') === false){
					$orderTotalsValue .= strip_tags($this->newOrder['orderTotals'][$i]['text'])  . "<br/>";
				}
				if(strpos(strtolower($this->newOrder['orderTotals'][$i]['title']),'total') !== false){
					$totalVal = strip_tags($this->newOrder['orderTotals'][$i]['title']) .': '.strip_tags($this->newOrder['orderTotals'][$i]['text']).'<br/>';
				}
			}
		}else{
			for ($i=0, $n=sizeof($this->totals); $i<$n; $i++) {
				if(strpos(strtolower($this->totals[$i]['title']),'total') === false && strpos(strtolower($this->totals[$i]['title']),'sub-total') === false){
					$orderTotalsTitle .= strip_tags($this->totals[$i]['title']) . "<br/>";
				}
			}
			for ($i=0, $n=sizeof($this->totals); $i<$n; $i++) {
				if(strpos(strtolower($this->totals[$i]['title']),'total') === false && strpos(strtolower($this->totals[$i]['title']),'sub-total') === false){
					$orderTotalsValue .= strip_tags($this->totals[$i]['text'])  . "<br/>";
				}
				if(strpos(strtolower($this->totals[$i]['title']),'total') !== false){
					$totalVal = strip_tags($this->totals[$i]['title']) .': '.strip_tags($this->totals[$i]['text']).'<br/>';
				}
			}

		}
		$emailEvent->setVar('totalNames', $orderTotalsTitle);
		$emailEvent->setVar('totalValues', $orderTotalsValue);
		$emailEvent->setVar('totalVal', $totalVal);

		if (!empty($this->info['payment_module'])){
			$emailEvent->setVar('paymentTitle', $this->info['payment_module']);
		}

		/*to debug

		$res = print_r($userAccount, true);
		$myFile = sysConfig::getDirFsCatalog(). 'file2.txt';
		$fh = fopen($myFile, 'a') or die("can't open file");
		fwrite($fh, 'new email:'.$res.'\n');
		fwrite($fh, 'new email lang:'.$userAccount->getLanguageId().'\n');
		fwrite($fh, 'new email orderid:'.(isset($this->newOrder['orderID'])?$this->newOrder['orderID']:$this->orderId).'\n');
		fwrite($fh, 'new email address:'.$userAccount->getEmailAddress().'\n');
		fwrite($fh, 'new email name:'.$userAccount->getFullName().'\n');
		fclose($fh);

		end debug*/

		if(isset($this->newOrder)){
			$currentOrder =& $this->newOrder;
		}else{
			$currentOrder['orderID'] = (isset($this->newOrder['orderID'])?$this->newOrder['orderID']:$this->orderId);
			$currentOrder['productsOrdered'] = (isset($this->newOrder['productsOrdered']) ? $this->newOrder['productsOrdered'] : ((isset($products_ordered)&&(!empty($products_ordered)))?$products_ordered:$this->products_ordered) );
		}

		$sendVariables = array();

		EventManager::notify('OrderBeforeSendEmail', &$currentOrder, &$emailEvent, &$products_ordered, &$sendVariables);

		$sendVariables['email'] = strtolower($userAccount->getEmailAddress());
		$sendVariables['name'] = $userAccount->getFullName();
		$emailEvent->sendEmail($sendVariables);
		if(isset($sendVariables['emails']) && is_array($sendVariables['emails'])){
			foreach($sendVariables['emails'] as $email){

					$emailEvent->sendEmail(array(
							'email' => strtolower($email['email']),
							'name'  => $email['name']
						));
			}
		}

		if (sysConfig::get('SEND_EXTRA_ORDER_EMAILS_TO') != '') {
			$extraEmails = sysConfig::get('SEND_EXTRA_ORDER_EMAILS_TO');
			foreach(explode(',', $extraEmails) as $email){
				$email = trim($email);
				$matches = array();
				if (strstr($email, '<')){
					preg_match('/([a-zA-Z 0-9]+)\<(.*)\>/', $email, &$matches);
				}elseif (strstr($email, '@')){
					$matches = array(
						$userAccount->getFullName(),
						$email
					);
				}

				if (!empty($matches)){
					$emailEvent->sendEmail(array(
						'email' => strtolower($matches[2]),
						'name'  => $matches[1]
					));
				}
			}
		}
	}
}
?>