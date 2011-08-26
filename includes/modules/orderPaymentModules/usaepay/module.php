<?php
class OrderPaymentUsaepay extends CreditCardModule {

	public function __construct(){
		global $order;
		/*
		 * Default title and description for modules that are not yet installed
		 */
		$this->setTitle('Credit Card via USAePay');
		$this->setDescription('Credit Card via USAePay');
		
		$this->init('usaepay');
		
		if ($this->isEnabled() === true){
			$this->isCron = false;
			$this->timeout = 45;

			############### Additional code for curl start #####################
			// Set default values.
			if (Session::exists('payment_rental') === true && isset($order->info['free_trial_amount']) && isset($order->info['free_trial'])&& (int)$order->info['free_trial_amount']>0 && (int)$order->info['free_trial']>0){
				$this->command="preauth";
			}else{
				$this->command="sale";
			}
			$this->result="Error";
			$this->resultcode="E";
			$this->error="Transaction not processed yet.";
			$this->timeout=45;
			$this->cardpresent=false;
			$this->software="USAePay PHP API v" . sysConfig::get('USAEPAY_VERSION');
			############### Additional code for curl End #####################

			$this->setFormUrl('https://www.usaepay.com/gate.php');
		}
	}

	function onSelect(){
		$cardTypeSelect = htmlBase::newElement('selectbox')
		->setId('cardType')
		->setName('cardType');
		foreach(sysConfig::explode('MODULE_PAYMENT_USAEPAY_CARD_TYPES', ',') as $type){
			$cardTypeSelect->addOption($type, $type);
		}

		$paymentInfo = OrderPaymentModules::getPaymentInfo();

		if (isset($paymentInfo['cardDetails']['cardType'])){
			$cardTypeSelect->selectOptionByValue($paymentInfo['cardDetails']['cardType']);
		}

		$return = parent::onSelect();
		
		$return['fields'] = array(
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_USAEPAY_TEXT_CREDIT_CARD_OWNER'),
				'field' => $this->getCreditCardOwnerField()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_USAEPAY_TEXT_CREDIT_CARDS_ACCEPTED'),
				'field' => $cardTypeSelect->draw()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_USAEPAY_TEXT_CREDIT_CARD_NUMBER'),
				'field' => $this->getCreditCardNumber()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_USAEPAY_TEXT_CREDIT_CARD_EXPIRES'),
				'field' => $this->getCreditCardExpMonthField() . '&nbsp;' . $this->getCreditCardExpYearField()
			),
			array(
				'title' => sysLanguage::get('MODULE_PAYMENT_USAEPAY_TEXT_CREDIT_CARD_CHECKNUMBER'). ' ' .'<a onclick="popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'cvv_help') . '\',400,300);return false;">' . '<u><i>(What is it?)</i></u></a>',
				'field' => $this->getCreditCardCvvField() . '&nbsp;<small>' . sysLanguage::get('MODULE_PAYMENT_USAEPAY_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION') . '</small>'
			)
		);
		
		return $return;
	}

	public function hasHiddenFields(){
		return true;
	}
	
	public function getHiddenFields(){
		global $order, $onePageCheckout;
		
		$userAccount = &Session::getReference('userAccount');
		$addressBook = $userAccount->plugins['addressBook'];

		$defaultAddress = $addressBook->getAddress($addressBook->getDefaultAddressId());
		$deliveryAddress = $addressBook->getAddress('delivery');
		$billingAddress = $addressBook->getAddress('billing');

		$countryInfoBilling = $addressBook->getCountryInfo($billingAddress['country_id']);
		$countryInfoDelivery = $addressBook->getCountryInfo($deliveryAddress['country_id']);

		$paymentInfo = $onePageCheckout->onePage['info']['payment']['cardDetails'];

		$hiddenFields = array(
			'UMkey'         => sysConfig::get('MODULE_PAYMENT_USAEPAY_KEY'),
			'UMredir'       => itw_app_link('action=processNormalCheckout', 'checkout', 'default', 'SSL', false),
			'UMamount'      => number_format($order->info['total'], 2),
			'UMinvoice'     => date('YmdHis'),
			'UMcommand'     => $this->command,
			'UMtestmode'    => (sysConfig::get('MODULE_PAYMENT_USAEPAY_TESTMODE') == 'Test' ? '1' : '0'),
			'UMcard'        => $paymentInfo['cardNumber'],
			'UMcvv2'        => $paymentInfo['cardCvvNumber'],
			'UMexpir'       => $paymentInfo['cardExpMonth'] . substr($paymentInfo['cardExpYear'], -2),
			'UMname'        => $paymentInfo['cardOwner'],
			'UMstreet'      => $defaultAddress['street_address'],
			'UMbillfname'   => $billingAddress['firstname'],
			'UMbilllname'   => $billingAddress['lastname'],
			'UMbillcompany' => $billingAddress['company'],
			'UMbillstreet'  => $billingAddress['street_address'],
			'UMbillcity'    => $billingAddress['city'],
			'UMbillstate'   => $billingAddress['state'],
			'UMbillcountry' => $countryInfoBilling['title'],
			'UMbillzip'     => $billingAddress['postcode'],
			'UMzip'         => $billingAddress['postcode'],
			'UMcustemail'   => $userAccount->getEmailAddress(),
			'UMbillphone'   => $userAccount->getTelephoneNumber(),
			'UMshipfname'   => $deliveryAddress['firstname'],
			'UMshiplname'   => $deliveryAddress['lastname'],
			'UMshipstreet'  => $deliveryAddress['street_address'],
			'UMshipcity'    => $deliveryAddress['city'],
			'UMshipstate'   => $deliveryAddress['state'],
			'UMshipzip'     => $deliveryAddress['postcode'],
			'UMshipcountry' => $countryInfoDelivery['title'],
			'UMcustid'      => $userAccount->getCustomerId(),
			'UMechofields'  => '1'
		);

		if ($hiddenFields['UMcommand'] == 'preauth'){
			$newTotal = ($order->info['total'] - $order->info['subtotal']);
			$prods = $order->products;
			for($i=0; $i<sizeof($prods); $i++){
				if ($prods[$i]['auth_method'] == 'auth'){
					if ($prods[$i]['auth_charge'] > 0){
						$newTotal += $prods[$i]['auth_charge'] * $prods[$i]['quantity'];
					}else{
						$newTotal += $prods[$i]['final_price'] * $prods[$i]['quantity'];
					}
				}else{
					$newTotal += $prods[$i]['final_price'] * $prods[$i]['quantity'];
				}
			}
			$hiddenFields['UMamount'] = number_format($newTotal, 2);
		}
		
		if ($onePageCheckout->isMembershipCheckout() === true){
			if ($order->info['free_trial_amount'] > 0 && $order->info['free_trial'] > 0){
				$UMcommand = 'preauth';
				$UMamount = $order->info['free_trial_amount'];
			}else{
				$UMcommand = 'sale';
				$UMamount = number_format($order->info['total'], 2);
			}

			$hiddenFields['UMcommand'] = $UMcommand;
			$hiddenFields['UMamount'] = $UMamount;
			if ($hiddenFields['UMcommand'] == 'preauth'){
				$newTotal = ($order->info['total'] - $order->info['subtotal']);
				$prods = $order->products;
				for($i=0; $i<sizeof($prods); $i++){
					if ($prods[$i]['auth_method'] == 'auth'){
						if ($prods[$i]['auth_charge'] > 0){
							$newTotal += $prods[$i]['auth_charge'] * $prods[$i]['quantity'];
						}else{
							$newTotal += $prods[$i]['final_price'] * $prods[$i]['quantity'];
						}
					}else{
						$newTotal += $prods[$i]['final_price'] * $prods[$i]['quantity'];
					}
				}
				$hiddenFields['UMamount'] = number_format($newTotal, 2);
			}
		}

		$process_button_string = '';
		foreach($hiddenFields as $var => $val){
			$process_button_string .= tep_draw_hidden_field($var, $val);
		}
		return $process_button_string;
	}

	function processPayment($orderID = null, $amount = null){
		global $order, $userAccount;
		switch ($_GET['UMstatus']){
			case 'Approved':
				$sql = 'insert into '.TABLE_MEMBERSHIP_BILLING_REPORT.' set customers_id='.(int)$customer_id.', error = "Transaction Approved", date=now(), status="A"';
			break;
			case 'Declined':
				$sql = 'insert into '.TABLE_MEMBERSHIP_BILLING_REPORT.' set customers_id='.(int)$customer_id.', error = "'.$_GET['UMerror'].'", date=now(), status="D"';
				tep_db_query($sql);
				tep_redirect(itw_app_link('error_message=' . urlencode(MODULE_PAYMENT_USAEPAY_TEXT_DECLINED_MESSAGE), 'checkout', 'default', 'SSL', true, false));
			break;
			case 'Error':
				$sql = 'insert into '.TABLE_MEMBERSHIP_BILLING_REPORT.' set customers_id='.(int)$customer_id.', error = "'.$_GET['UMerror'].'", date=now(), status="D"';
				tep_db_query($sql);
				tep_redirect(itw_app_link('error_message=' . urlencode($_GET['UMerror']), 'checkout', 'default', 'SSL', true, false));
			break;
		}
	}

	function processPaymentCron($orderID){
		$this->isCron = true;
		$Qorder = Doctrine_Query::create()
		->from('Orders o')
		->leftJoin('o.Customers c')
		->leftJoin('o.OrdersAddresses oa')
		->leftJoin('o.OrdersTotal ot')
		->leftJoin('c.CustomersMembership m')
		->where('o.orders_id = ?', $orderID)
		->andWhere('oa.address_type = ?', 'billing')
		->andWhereIn('ot.module_type', array('total', 'ot_total'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if (!class_exists('umTransaction')){
			include(sysConfig::getDirFsCatalog() . 'includes/classes/usaepay_curl.php');
		}
		if (!function_exists('cc_decrypt')){
			include(sysConfig::getDirFsCatalog() . 'includes/functions/crypt.php');
		}

		$tran = new umTransaction;

		$tran->key = sysConfig::get('MODULE_PAYMENT_USAEPAY_KEY');
		$tran->ip = $_SERVER['REMOTE_ADDR'];
		$tran->testmode = 0;

		$tran->cardholder = $Qorder[0]['OrdersAddresses'][0]['entry_name'];
		$tran->card = cc_decrypt($Qorder[0]['Customers']['CustomersMembership']['card_num']);
		$tran->exp = cc_decrypt($Qorder[0]['CustomersMembership']['exp_date']);

		$tran->amount = $Qorder[0]['OrdersTotal'][0]['value'];
		$tran->invoice = date('YmdHis');
		$tran->street = $Qorder[0]['OrdersAddresses'][0]['entry_street_address'];
		$tran->zip = $Qorder[0]['OrdersAddresses'][0]['entry_postcode'];
		$tran->description = STORE_NAME . ' Subscription Payment';

		if ($tran->Process()){
			$this->cronMsg = 'Payment Successful';
			return true;
		}else{
			$this->cronMsg = 'Payment Unsuccessful ( ' . $tran->error . ' )';
			return false;
		}
		
		return true;
	}
		
	private function onResponse($logData){
		$this->onSuccess($logData);
		return true;
	}
		
	private function onSuccess($logData){
		$this->logPayment($logData);
	}
		
	private function onFail($info){
	}
}
?>
