 <?php
class OrderPaymentGateway2checkout extends StandardPaymentModule
{

	private $parameters = array();
	

	private $apiResponseMessage = null;

	const RESPONSE_OK = 'OK';
	
	const URL_MARK_SHIPPED = 'https://www.2checkout.com/api/sales/mark_shipped';
	
	const URL_LIST_SALES = 'https://www.2checkout.com/api/sales/list_sales';
	
	const URL_DEATIL_PENDING_PAYMENT = 'https://www.2checkout.com/api/acct/detail_pending_payment';
	
	const URL_DETAIL_COMPANY_INFO = 'https://www.2checkout.com/api/acct/detail_company_info';
	
	const URL_LIST_PAYMENTS = 'https://www.2checkout.com/api/acct/list_payments';
	
	const URL_DETAIL_SALE = 'https://www.2checkout.com/api/sales/detail_sale';
	
	const URL_REFUND_INVOICE = 'https://www.2checkout.com/api/sales/refund_invoice';
	
	const URL_CREATE_COMMENT = 'https://www.2checkout.com/api/sales/create_comment';
	
	const URL_STOP_LINEITEM_RECURRING = 'https://www.2checkout.com/api/sales/stop_lineitem_recurring';

	const URL_REFUND_ORDER_ITEM = 'https://www.2checkout.com/api/sales/refund_lineitem';

	public function __construct() {
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Credit/Debit Card (via 2Checkout)');
		$this->setDescription('Credit/Debit Card (via 2Checkout)');

	
		$this->init(
			'gateway2checkout',
			false,
			__DIR__ . DIRECTORY_SEPARATOR
		);
		
		if (strncmp($this->getConfigData('PURCHASE_ROUTINE'), 'Standard', 8) == 0)
		{
		$this->setFormUrl('https://www.2checkout.com/checkout/purchase');
		}
		else
			$this->setFormUrl('https://www.2checkout.com/checkout/spurchase');
		

	}

	/**
	 * @param string $sendMethod
	 * @return CurlRequest
	 */
	private function createApiRequest($sendMethod){
		$CurlRequest = new CurlRequest();
		$CurlRequest->setSendMethod($sendMethod);
		$CurlRequest->setHttpHeader('Accept', 'application/json');
		$CurlRequest->setLoginInfo($this->getConfigData('USERNAME'), $this->getConfigData('PASSWORD'));
		return $CurlRequest;
	}
	
	/**
	 * @param string $url
	 * @param array $data
	 * @param string $sendMethod
	 * @return stdClass
	 */
	private function callApi($url, $data = array(), $sendMethod = 'get'){
		$CurlRequest = $this->createApiRequest($sendMethod);
		$CurlRequest->setUrl($url);
		
		$data['vender_id'] = $this->getConfigData('VENDOR_ID');

		$CurlRequest->setData($data);

		$CurlResponse = $CurlRequest->execute();

		
		return json_decode($CurlResponse->getResponse());
	}

	private function reportApiResponse($message, $type){
		global $messageStack;

		$this->apiResponseMessage = $message;
		$messageStack->add('pageStack', $message, $type);
	}

	public function getApiResponse(){
		return $this->apiResponseMessage;
	}

	private function setParameter($k, $v) {
		$this->parameters[$k] = $v;
	}

	function beforeProcessPayment() {
		global $order, $orderTotalModules, $onePageCheckout, $currencies, $ShoppingCart;
		$return = false;
		if (isset($order->newOrder['orderID'])){
			if (Session::exists('cartID')){
				$return = true;
				Session::set('cart_Gateway2Checkout_INS_ID', Session::get('cartID') . '-' . $order->newOrder['orderID']);
			}
		}

		return $return;
	}

	public function ownsProcessPage(){
		if (isset($_GET['payment_module']) && $_GET['payment_module'] == $this->getCode()){
			return true;
		}elseif (isset($_POST['payment_module']) && $_POST['payment_module'] == $this->getCode()){
			return true;
		}
		return false;
	}

	function getHiddenFields() {

		global $order, $ShoppingCart, $currencies, $userAccount, $onePageCheckout;

		$AddressBook =& $userAccount->plugins['addressBook'];

		if ($this->getConfigData('DEMO_MODE')){
			$this->setParameter('demo', 'Y');
		}
		$this->setParameter('sid', $this->getConfigData('VENDOR_ID'));
		$this->setParameter('mode', '2CO');
		$this->setParameter('pay_method', $this->getConfigData('DEFAULT_PAYMENT'));
		$this->setParameter('skip_landing', $this->getConfigData('SKIP_LANDING'));
		//$this->setParameter('return_url', itw_app_link(null, 'shoppingCart', 'default'));
		$this->setParameter('merchant_order_id', $order->newOrder['orderID']);
		//$this->setParameter('x_receipt_link_url', itw_app_link('action=remotePaymentProcess', 'checkout', 'success'));
		$this->setParameter('x_receipt_link_url', itw_app_link(null, 'checkout', 'success'));

     	//	$this->setParameter('action', 'remotePaymentProcess');  //causes problem with form creating a blank page on return
		$this->setParameter('payment_module', $this->getCode());
		if ($onePageCheckout->isMembershipCheckout() === true){
			$this->setParameter('is_membership', 'Y');
		}

		/*Session::set('Gateway2Checkout_Ins_Hash', strtoupper(md5(
			 $_POST['sale_id'] .
				$this->getConfigData('VENDOR_ID') .
				$_POST['invoice_id'] .
				$this->getConfigData('INS_SECRET')
		)));*/


		if ($this->getConfigData('PREPOPULATE_BILLING_INFO') == 'True'){
			$billingAddress = $AddressBook->getAddress('billing');
			$billingCountryInfo = $AddressBook->getCountryInfo($billingAddress['entry_country_id']);

			$this->setParameter('card_holder_name', $billingAddress['entry_firstname'] . ' ' . $billingAddress['entry_lastname']);
			$this->setParameter('street_address', $billingAddress['entry_street_address']);
			$this->setParameter('street_address2', '');
			$this->setParameter('city', $billingAddress['entry_city']);
			$this->setParameter('state', $billingAddress['entry_state']);
			$this->setParameter('zip', $billingAddress['entry_postcode']);
			$this->setParameter('country', $billingCountryInfo['countries_name']);
			$this->setParameter('email', $userAccount->getEmailAddress());
			$this->setParameter('phone', $userAccount->getTelephoneNumber());
			$this->setParameter('phone_extension', '');
		}

		if ($this->getConfigData('PREPOPULATE_SHIPPING_INFO') == 'True'){
			$deliveryAddress = $AddressBook->getAddress('delivery');
			$deliveryCountryInfo = $AddressBook->getCountryInfo($deliveryAddress['entry_country_id']);

			$this->setParameter('ship_name', $deliveryAddress['entry_firstname'] . ' ' . $deliveryAddress['entry_lastname']);
			$this->setParameter('ship_street_address', $deliveryAddress['entry_street_address']);
			$this->setParameter('ship_street_address2', '');
			$this->setParameter('ship_city', $deliveryAddress['entry_city']);
			$this->setParameter('ship_state', $deliveryAddress['entry_state']);
			$this->setParameter('ship_zip', $deliveryAddress['entry_postcode']);
			$this->setParameter('ship_country', $deliveryCountryInfo['countries_name']);
		}


		if ($this->getConfigData('PRODUCT_INFORMATION') == 'SalesIgniter'){
			$i = 1;
			if ($onePageCheckout->isMembershipCheckout() === true){
				$this->setParameter('checkoutType', 'rental');
				$rentalPlan = $onePageCheckout->onePage['rentalPlan'];
				foreach($order->products as $pInfo){
					$membershipMonths = $rentalPlan['months'];
					$membershipDays = $rentalPlan['days'];

					$this->setParameter('li_' . $i . '_type', 'product');
					$this->setParameter('li_' . $i . '_name', addslashes($pInfo['name']));
					$this->setParameter('li_' . $i . '_quantity', $pInfo['quantity']);
					$this->setParameter('li_' . $i . '_price', round($pInfo['final_price'],2));
					$this->setParameter('li_' . $i . '_product_id', $pInfo['id']);
					if ($membershipMonths > 0){
						$this->setParameter('li_' . $i . '_recurrence', $membershipMonths . ' Month');
					}elseif ($membershipDays > 0){
						$this->setParameter('li_' . $i . '_recurrence', $membershipDays . ' Day');
					}
					$this->setParameter('li_' . $i . '_duration', 'forever');
					//$this->setParameter('li_' . $i . '_startup_fee', 'Deposit Amount??');
					$this->setParameter('fixed', 'Y');

					$i++;
				}
			}else{

				$CartProducts = $ShoppingCart->getProducts();
                            //was $CartProducts=$ShoppingCart->getProducts()->getIterator();
				$CartProducts->rewind();

				while($CartProducts->valid() === true){

					$CartProduct = $CartProducts->current();
					$this->setParameter('li_' . $i . '_type', 'product');
					$this->setParameter('li_' . $i . '_name', addslashes($CartProduct->getName()));
					$this->setParameter('li_' . $i . '_quantity', $CartProduct->getQuantity());
					$this->setParameter('li_' . $i . '_price', round($CartProduct->getFinalPrice(), 2));
					//$this->setParameter('li_' . $i . '_product_id', $CartProduct->getProductClass()->getId());
					$this->setParameter('li_' . $i . '_product_id' , $CartProduct->getIdString());
					//$this->setParameter('li_' . $i . '_product_description', addslashes(htmlspecialchars($CartProduct->getNameHtml())));
					//$this->setParameter('li_' . $i . '_tangible', 'Y');
					$CartProducts->next();
					$i++;
				}
			}


			foreach(OrderTotalModules::process() as $OrderTotal){
				if (in_array($OrderTotal['code'], array('shipping', 'tax', 'coupon'))){
					if ($OrderTotal['code'] == 'shipping'){
						$Type = 'SHIPPING';
					}
					elseif ($OrderTotal['code'] == 'tax'){
						$Type = 'TAX';
					}
					elseif ($OrderTotal['code'] == 'coupon'){
						$Type = 'COUPON';
					}
					$this->setParameter('li_' . $i . '_type', $Type);
					$this->setParameter('li_' . $i . '_name', $OrderTotal['title']);
					$this->setParameter('li_' . $i . '_price', $OrderTotal['value']);

					$i++;
				}
			}

		}

		$process_button_string = '';
		foreach($this->parameters as $k => $v){
			$process_button_string .= htmlBase::newElement('input')
				->setType('hidden')
				->setName($k)
				->val($v)
				->draw() . "\n";
		}

	
		return $process_button_string;
	}

	
	  function afterOrderProcess($success) {

		global $userAccount;
		$PassbackData = (!empty($_POST) ? $_POST : $_GET);

/*$this->logPayment(array(
				'orderID' => $this->PassbackData['merchant_order_id'],
				'amount' => $this->PassbackData['total'],  //change to grand total sum
				'message' => '2checkout' , //transactionid
				'success' => 1));
*/

		$passbackHash = strtoupper(md5(
			$this->getConfigData('SECRET_WORD') .
			$this->getConfigData('VENDOR_ID') .
			$PassbackData['order_number'] .
			$PassbackData['total']
		));
		if ($passbackHash == $PassbackData['key'] || $PassbackData['demo'] == 'Y'){
			$Order = Doctrine_Core::getTable('Orders')
				->find((int) $PassbackData['merchant_order_id']);
			if ($Order){
				$Order->orders_status = $this->getConfigData('ORDER_STATUS_PROCESSING_ID');

				$NewPayment = new OrdersPaymentsHistory;
				$NewPayment->payment_module = $this->getCode();
				$NewPayment->payment_method = $this->getTitle();
				$NewPayment->gateway_message = 'Successful payment';
				$NewPayment->payment_amount = $PassbackData['total'];
				$Order->OrdersPaymentsHistory[] = $NewPayment;
				$NewStatus = new OrdersStatusHistory;
				$NewStatus->orders_status_id = $this->getConfigData('ORDER_STATUS_PROCESSING_ID');
				$NewStatus->customer_notified = '0';
				$NewStatus->comments = '2Checkout Payment Processing';
				$Order->OrdersStatusHistory[] = $NewStatus;

				$Order->save();

				if (isset($PassbackData['is_membership']) && $PassbackData['is_membership'] == 'Y'){
					$membership = $userAccount->plugins['membership'];
					$membership->updateActivationStatus('Y');
					$_GET['checkoutType'] = 'rental';
				}
			//header("Location: ". $PassbackData['x_receipt_link_url']);
			//echo "<meta http-equiv='Refresh' content='50;URL=" . $PassbackData['x_receipt_link_url'] . "'>";
			
			

			}else{
				die('Unable To Find Order');
			}
		}else{
			die('Invalid Data');
		}
	}
/*
	function processPaymentCron($orderID) {
		global $order;
		$order_status_id = OrderPaymentModules::getModule('paypalipn')
			->getConfigData('MODULE_PAYMENT_PAYPALIPN_COMP_ORDER_STATUS_ID');
		$newStatus = new OrdersPaymentsHistory();
		$newStatus->orders_id = $orderID;
		$newStatus->payment_module = 'paypal_ipn';
		$newStatus->payment_method = 'Paypal';
		$newStatus->gateway_message = 'Successfull payment';
		$newStatus->payment_amount = $order->info['total'];
		$newStatus->card_details = 'NULL';
		$newStatus->save();

		Doctrine_Query::create()
			->update('Orders')
			->set('orders_status', '?', $order_status_id)
			->set('last_modified', '?', date('Y-m-d H:i:s'))
			->where('orders_id = ?', $orderID)
			->execute();

		$newOrdersStatus = new OrdersStatusHistory();
		$newOrdersStatus->orders_id = $orderID;
		$newOrdersStatus->orders_status_id = $order_status_id;
		$newOrdersStatus->date_added = date('Y-m-d H:i:s');
		$newOrdersStatus->customer_notified = '0';
		$newOrdersStatus->comments = 'PayPal IPN (Not Verified Transaction)';
		$newOrdersStatus->save();
		$order->info['payment_method'] = $this->getTitle();

		return true;
	}
*/

	/**
	 * @param StdClass $Customer
	 * @param bool $html
	 * @return string
	 */
	public function formatAddress(StdClass $Customer, $html = true){
		$address = array();
		$address[] = $Customer->address_1;
		if (!empty($Customer->address_2)){
			$address[] = $Customer->address_2;
		}

		$address[] = $Customer->city . ', ' . $Customer->state . ' ' . $Customer->postal_code;
		if (empty($Customer->country_name)){
			$address[] = $Customer->country_code;
		}else{
			$address[] = $Customer->country_name;
		}
		return implode(($html === true ? '<br>' : "\n"), $address);
	}

	/**
	 * The list_sales call is used to retrieve a summary of all sales or only those matching a variety of sale attributes.
	 * @link https://www.2checkout.com/documentation/api/sales-list_sales/
	 *
	 * @param array $options
	 * @return bool|stdClass
	 */
	public function getOrders($options = array()){
		$defaults = array(
			'sale_id'             => null, // Search for sale with this Sale ID. Optional.
			'invoice_id'          => null, // Search for a Sale with this Invoice ID. Optional.
			'customer_name'       => null, // Search for sales with this cardholder name. Must be at least 3 chars and can be substring of cardholder name. Case Insensitive. Optional.
			'customer_email'      => null, // Search for sales with this customer email. Can be substring of the email. Case insensitive. Optional.
			'customer_phone'      => null, // Search for sales with this phone number. Can be an incomplete number but must match from the beginning. Optional.
			'vendor_product_id'   => null, // Search for sales with this product id. Can be substring of the id. Optional.
			'ccard_first6'        => null, // Search for sales with these First 6 numbers of the credit card number. Optional.
			'ccard_last2'         => null, // Search for sales with these Last 2 numbers of the credit card number. Optional.
			'sale_date_begin'     => null, // Search for sales from this date to current date (or sale_date_end). Optional.
			'sale_date_end'       => null, // Search for sales from beginning of time (or sale_date_begin) to this date. Optional.
			'declined_recurrings' => null, // Search for declined recurring sales. Optional.
			'active_recurrings'   => null, // Search for active recurring sales. Optional.
			'refunded'            => null, // Search for sales that have been refunded in full or partially. Optional.
			'cur_page'            => null, // The page number to retrieve. First page = 1. Optional.
			'pagesize'            => null, // Total rows per page. Possible values are 1-100. If pagesize not specified, default of 20 items per page will be assigned internally. Optional.
			'sort_col'            => null, // The name of the column to sort on. Possible values are sale_id, date_placed, customer_name, recurring, recurring_declined and usd_total. (case insensitive) Optional.
			'sort_dir'            => null  // The direction of the sort process. ('ASC' or 'DESC') (case insensitive) Optional.
		);

		$o = array_merge($defaults, $options);

		$ResponseErrorCodes = array(
			'PARAMETER_MISSING' => 'Required parameter missing: <parameter name>',
			'PARAMETER_INVALID' => 'Invalid value for parameter: <parameter name>',
			'RECORD_NOT_FOUND'  => 'Unable to find record.'
		);

		$error = false;

		$ResponseJSON = $this->callApi(self::URL_LIST_SALES, $o);
		if ($ResponseJSON->response_code == self::RESPONSE_OK){
			$this->reportApiResponse($ResponseJSON->response_message, 'success');
		}else{
			$error = true;
			$this->reportApiResponse($ResponseJSON->response_message, 'error');
		}
		return ($error === false ? $ResponseJSON : false);
	}

	/**
	 * The detail_pending_payment call is used to get a detailed estimate of the current pending payment.
	 * @link https://www.2checkout.com/documentation/api/acct-detail_pending_payment/
	 *
	 * @return bool
	 */
	public function getPendingPayment(){
		$error = false;

		$ResponseJSON = $this->callApi(self::URL_DEATIL_PENDING_PAYMENT);
		if ($ResponseJSON->response_code == self::RESPONSE_OK){
			$this->reportApiResponse($ResponseJSON->response_message, 'success');

			$CompanyInfo = $this->getCompanyInfo();
			if ($CompanyInfo !== false){
				$ResponseJSON->payment->currency = $CompanyInfo->currency_code;
			}
		}else{
			$error = true;
			$this->reportApiResponse($ResponseJSON->response_message, 'error');
		}
		return ($error === false ? $ResponseJSON->payment : false);
	}

	/**
	 * The detail_company_info call is used to retrieve your account’s company information details from the Site Management page.
	 * @link https://www.2checkout.com/documentation/api/acct-detail_company_info/
	 *
	 * @return bool
	 */
	public function getCompanyInfo(){
		$error = false;

		$ResponseJSON = $this->callApi(self::URL_DETAIL_COMPANY_INFO);
		if ($ResponseJSON->response_code == self::RESPONSE_OK){
			$this->reportApiResponse($ResponseJSON->response_message, 'success');
		}else{
			$error = true;
			$this->reportApiResponse($ResponseJSON->response_message, 'error');
		}
		return ($error === false ? $ResponseJSON->vendor_company_info : false);
	}

	/**
	 * The list_payments call is used to get a list of past vendor payments.
	 * @link https://www.2checkout.com/documentation/api/acct-list_payments/
	 *
	 * @return StdClass|bool
	 */
	public function getPastPayments(){
		$error = false;

		$ResponseJSON = $this->callApi(self::URL_LIST_PAYMENTS);
		if ($ResponseJSON->response_code == self::RESPONSE_OK){
			$this->reportApiResponse($ResponseJSON->response_message, 'success');
		}else{
			$error = true;
			$this->reportApiResponse($ResponseJSON->response_message, 'error');
		}
		return ($error === false ? $ResponseJSON->payments : false);
	}

	/**
	 * The detail_sale call is used to retrieve information about a specific sale or invoice.
	 * @link https://www.2checkout.com/documentation/api/sales-detail_sale/
	 *
	 * @param array $options
	 * @return StdClass|bool
	 */
	public function getOrderDetails($options = array()){
		$defaults = array(
			'sale_id'    => null, // The order number of the requested sale. Optional if invoice_id is specified.
			'invoice_id' => null  // The invoice number of the requested invoice (specify to include only the requested invoice. Omit and use sale_id to include all invoices). Optional if sale_id is specified.
		);

		$o = array_merge($defaults, $options);

		$error = false;
		if (is_null($o['sale_id']) === true && is_null($o['invoice_id']) === true){
			$error = true;
		}

		if ($error === false){
			$ResponseErrorCodes = array(
				'PARAMETER_MISSING' => 'Required parameter missing: <parameter name>',
				'PARAMETER_INVALID' => 'Invalid value for parameter: <parameter name>',
				'RECORD_NOT_FOUND'  => 'Unable to find record.',
				'FORBIDDEN'         => 'Access denied to sale.'
			);

			$ResponseJSON = $this->callApi(self::URL_DETAIL_SALE, $o);
			if ($ResponseJSON->response_code == self::RESPONSE_OK){
				$this->reportApiResponse($ResponseJSON->response_message, 'success');
			}else{
				$error = true;
				$this->reportApiResponse($ResponseJSON->response_message, 'error');
			}
		}
		return ($error === false ? $ResponseJSON->sale : false);
	}

	/**
	 * The refund_invoice call is used to attempt to issue a full or partial refund on an invoice. 
	 * This call will send the REFUND_ISSUED INS message.
	 * @link https://www.2checkout.com/documentation/api/sales-refund_invoice/
	 * 
	 * @param array $options
	 * @return bool
	 */
	public function refundOrder($options = array()){
		$defaults = array(
			'sale_id'    => null, // Order number/sale ID to issue a refund on. Optional when invoice_id is specified, otherwise required.
			'invoice_id' => null, // Invoice ID to issue a refund on. Optional when sale_id is specified and sale only has 1 invoice.
			'amount'     => null, // The amount to refund. Only needed when issuing a partial refund. If an amount is not specified, the remaining amount for the invoice is assumed.
			'currency'   => 'customer', // Currency type of refund amount. Can be 'usd', 'vendor' or 'customer'. Only required if amount is used.
			'category'   => null, // ID representing the reason the refund was issued. Required.
			'comment'    => null  // Message explaining why the refund was issued. Required.
		);

		$o = array_merge($defaults, $options);

		$allowedCategories = array(
			1,  // Did not receive order
			2,  // Did not like item
			3,  // Item(s) not as described
			4,  // Fraud
			5,  // Other
			6,  // Item not available
			8,  // No response
			9,  // Recurring last installment
			10, // Cancellation
			11, // Billed in error
			12, // Prohibited product
			13, // Service refunded at sellers request
			14, // Nondelivery
			15, // Not as described
			16, // Out of stock
			17  // Duplicate
		);

		$error = false;
		if (
			(is_null($o['invoice_id']) === true && is_null($o['sale_id']) === true) ||
			(is_null($o['category']) === true || !in_array($o['category'], $allowedCategories)) ||
			(is_null($o['comment']) === true || strlen(strip_tags($o['comment'])) > 5000)
		){
			$error = true;
		}

		if ($error === false){
			$o['comment'] = strip_tags($o['comment']);

			$ResponseErrorCodes = array(
				'PARAMETER_MISSING' => 'Required parameter missing: <parameter name>',
				'PARAMETER_INVALID' => 'Invalid value for parameter: <parameter name>',
				'RECORD_NOT_FOUND'  => 'Unable to find record.',
				'FORBIDDEN'         => 'Access denied to sale.',
				'FORBIDDEN'         => 'Permission denied to set refund category to 7.',
				'FORBIDDEN'         => 'Access denied to invoice.',
				'AMBIGUOUS'         => 'Ambiguous request. Multiple invoices on sale. invoice_id parameter required.',
				'TOO_LOW'           => 'Amount must be at least 0.01.',
				'NOTHING_TO_DO'     => 'Invoice was already refunded.',
				'TOO_HIGH'          => 'Amount greater than remaining balance on invoice.',
				'TOO_LATE'          => 'Invoice too old to refund. (Will occur if sale is over 180 days)'
			);

			$ResponseJSON = $this->callApi(self::URL_REFUND_INVOICE, $o, 'post');
			if ($ResponseJSON->response_code == self::RESPONSE_OK){
				$this->reportApiResponse($ResponseJSON->response_message, 'success');
			}else{
				$error = true;
				$this->reportApiResponse($ResponseJSON->response_message, 'error');
			}
		}
		return ($error === false);
	}

	/**
	 * The refund_lineitem call is used to attempt to issue a full or partial refund on an invoice.
	 * This call will send the REFUND_ISSUED INS message.
	 * @link https://www.2checkout.com/documentation/api/sales-refund_lineitem/
	 * 
	 * @param array $options
	 * @return bool
	 */
	public function refundOrderItem($options = array()){
		$defaults = array(
			'lineitem_id' => null, // Line Item ID to stop recurring on. Required.
			'category'    => null, // ID representing the reason the refund was issued. Required.
			'comment'     => null  // Message explaining why the refund was issued. Required.
		);

		$o = array_merge($defaults, $options);

		$allowedCategories = array(
			1,  // Did not receive order
			2,  // Did not like item
			3,  // Item(s) not as described
			4,  // Fraud
			5,  // Other
			6,  // Item not available
			8,  // No response from seller
			9,  // Recurring last installment
			10, // Cancellation
			11, // Billed in error
			12, // Prohibited product
			13, // Service refunded at sellers request
			14, // Nondelivery
			15, // Not as described
			16, // Out of stock
			17  // Duplicate
		);

		$error = false;
		if (
			is_null($o['lineitem_id']) === true ||
			(is_null($o['category']) === true || !in_array($o['category'], $allowedCategories)) || 
			(is_null($o['comment']) === true || strlen(strip_tags($o['comment'])) > 5000)
		){
			$error = true;
		}

		if ($error === false){
			$o['comment'] = strip_tags($o['comment']);

			$ResponseErrorCodes = array(
				'PARAMETER_MISSING' => 'Required parameter missing: <parameter name>',
				'PARAMETER_INVALID' => 'Invalid value for parameter: <parameter name>',
				'RECORD_NOT_FOUND'  => 'Unable to find record.',
				'FORBIDDEN'         => 'Access denied to sale.',
				'INVALID_PARAMETER' => 'This lineitem cannot be refunded.',
				'NOTHING_TO_DO'     => 'Lineitem was already refunded.',
				'TOO_LATE'          => 'Invoice too old to refund lineitem. (Will occur if sale is over 180 days)',
				'TOO_HIGH'          => 'Lineitem amount greater than remaining balance on invoice.',
				'TOO_LOW'           => 'Lineitem amount must be at least 0.01.'
			);

			$ResponseJSON = $this->callApi(self::URL_REFUND_ORDER_ITEM, $o, 'post');
			if ($ResponseJSON->response_code == self::RESPONSE_OK){
				$this->reportApiResponse($ResponseJSON->response_message, 'success');
			}else{
				$error = true;
				$this->reportApiResponse($ResponseJSON->response_message, 'error');
			}
		}
		return ($error === false);
	}

	/**
	 * The stop_lineitem_recurring call is used to attempt to stop a recurring line item for a specified sale.
	 * This call will send the RECURRING_STOPPED INS message.
	 * @link https://www.2checkout.com/documentation/api/sales-stop_lineitem_recurring/
	 * 
	 * @param array $options
	 * @return bool
	 */
	public function stopRecurringItem($options = array()){
		$defaults = array(
			'lineitem_id' => null // Line Item ID to stop recurring on. Required.
		);

		$o = array_merge($defaults, $options);

		$error = false;
		if (is_null($o['lineitem_id']) === true){
			$error = true;
		}

		if ($error === false){
			$ResponseErrorCodes = array(
				'PARAMETER_MISSING' => 'Required parameter missing: <parameter name>',
				'PARAMETER_INVALID' => 'Invalid value for parameter: <parameter name>',
				'RECORD_NOT_FOUND'  => 'Unable to find record.',
				'FORBIDDEN'         => 'Access denied to sale.'
			);

			$ResponseJSON = $this->callApi(self::URL_STOP_LINEITEM_RECURRING, $o, 'post');
			if ($ResponseJSON->response_code == self::RESPONSE_OK){
				$this->reportApiResponse($ResponseJSON->response_message, 'success');
			}else{
				$error = true;
				$this->reportApiResponse($ResponseJSON->response_message, 'error');
			}
		}
		return ($error === false);
	}

	/**
	 * The mark_shipped call is used to attempt to mark an order as shipped and will attempt to reauthorize sale if specified in call.
	 * This call will send the SHIP_STATUS_CHANGED INS message.
	 * @link https://www.2checkout.com/documentation/api/sales-mark_shipped/
	 * 
	 * @param array $options
	 * @return bool
	 */
	public function markOrderShipped($options = array()){
		$defaults = array(
			'sale_id'         => null,  // The order number/sale ID to mark shipped. Optional when invoice_id is present.
			'invoice_id'      => null,  // ID of the invoice to add tracking information to. Required on sales with more than one invoice.
			'tracking_number' => null,  // The tracking number issued by the shipper. Required.
			'cc_customer'     => false, // Specify whether the customer should be automatically notified. Defaults to false. Optional.
			'reauthorize'     => false, // Reauthorize payment if payment authorization has expired. Defaults to false. Optional.
			'comment'         => null   // Any text except for ‘<’ and ‘>’ up to 255 chars in length. Optional.
		);
		
		$o = array_merge($defaults, $options);
		
		$error = false;
		if (
			(is_null($o['invoice_id']) === true && is_null($o['sale_id']) === true) ||
			(is_null($o['tracking_number']) === true)
		){
			$error = true;
		}
		
		if ($error === false){
			$o['comment'] = strip_tags($o['comment']);
			
			$ResponseErrorCodes = array(
				'PARAMETER_MISSING' => 'Required parameter missing: <parameter name>',
				'PARAMETER_INVALID' => 'Invalid value for parameter: <parameter name>',
				'RECORD_NOT_FOUND'  => 'Unable to find record.',
				'FORBIDDEN'         => 'Access denied to sale.',
				'NOTHING_TO_DO'     => 'Item not shippable.',
				'TOO_LATE'          => 'Payment is already pending or deposited and cannot be reauthorized.',
				'TOO_SOON'          => 'Please wait until the next day before trying to reauthorize again.',
				'FAILED'            => 'Failed to reauthorize payment.',
				'INTERNAL_ERROR'    => 'Failed to marked shipped but reauthorization succeeded.',
				'INTERNAL_ERROR'    => 'Failed to marked shipped.'
			);

			$ResponseJSON = $this->callApi(self::URL_MARK_SHIPPED, $o, 'post');
			if ($ResponseJSON->response_code == self::RESPONSE_OK){
				$this->reportApiResponse($ResponseJSON->response_message, 'success');
			}else{
				$error = true;
				$this->reportApiResponse($ResponseJSON->response_message, 'error');
			}
		}
		return ($error === false);
	}

	/**
	 * The create_comment call is used to add a comment to a specified sale.
	 * @link https://www.2checkout.com/documentation/api/sales-create_comment/ 
	 * 
	 * @param array $options
	 * @return bool
	 */
	public function commentOrder($options = array()){
		$defaults = array(
			'sale_id'      => null, // The order number/sale ID of a sale to look for. Required.
			'sale_comment' => null, // String value of comment to be submitted. Required.
			'cc_vendor'    => 1,    // Set to 1 to have a copy sent to seller. Optional.
			'cc_customer'  => 1     // Set to 1 to have the customer sent an email copy. Optional.
		);
		
		$o = array_merge($defaults, $options);
		
		$error = false;
		if (
			is_null($o['sale_id']) === true || 
			is_null($o['sale_comment']) === true
		){
			$error = true;
		}

		if ($error === false){
			$o['sale_comment'] = strip_tags($o['sale_comment']);

			$ResponseErrorCodes = array(
				'PARAMETER_MISSING' => 'Required parameter missing: <parameter name>',
				'PARAMETER_INVALID' => 'Invalid value for parameter: <parameter name>',
				'RECORD_NOT_FOUND'  => 'Unable to find record.',
				'FORBIDDEN'         => 'Access denied to sale.'
			);

			$ResponseJSON = $this->callApi(self::URL_CREATE_COMMENT, $o, 'post');
			if ($ResponseJSON->response_code == self::RESPONSE_OK){
				$this->reportApiResponse($ResponseJSON->response_message, 'success');
			}else{
				$error = true;
				$this->reportApiResponse($ResponseJSON->response_message, 'error');
			}
		}
		return ($error === false);
	}
}
