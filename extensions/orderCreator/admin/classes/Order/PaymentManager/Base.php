<?php
class OrderCreatorPaymentManager extends OrderPaymentManager implements Serializable {

	public function serialize(){
		$data = array(
			'orderId' => $this->orderId,
			'History' => $this->History,
			'PaymentsTotal' => $this->PaymentsTotal
		);
		return serialize($data);
	}

	public function unserialize($data){
		$data = unserialize($data);
		foreach($data as $key => $dInfo){
			$this->$key = $dInfo;
		}
	}
	
	public function processPayment($moduleName, &$CollectionObj = null){
		global $Editor;
		$Module = OrderPaymentModules::getModule($moduleName);
		$BillingAddress = $Editor->AddressManager->getAddress('billing');

		if(is_object($BillingAddress)){
			$RequestData = array(
				'amount' => $_POST['payment_amount'],
				'currencyCode' => $Editor->getCurrency(),
				'orderID' => $Editor->getOrderId(),
				'description' => 'Administration Order Payment',
				'customerId' => $Editor->getCustomerId(),
				'customerEmail' => $Editor->getEmailAddress(),
				'customerTelephone' => $Editor->getTelephone(),
				'customerFirstName' => $BillingAddress->getFirstName(),
				'customerLastName' => $BillingAddress->getLastName(),
				'customerStreetAddress' => $BillingAddress->getStreetAddress(),
				'customerPostcode' => $BillingAddress->getPostcode(),
				'customerCity' => $BillingAddress->getCity(),
				'customerState' => $BillingAddress->getState(),
				'customerCountry' => $BillingAddress->getCountry()
			);
			if(sysConfig::get('ACCOUNT_COMPANY') == 'true'){
				$RequestData['customerCompany'] = $BillingAddress->getCompany();
			}
		}else{
			$Address = $Editor->AddressManager->getAddress('customer');
			$name = explode(' ',  $Address->getName());

			$RequestData = array(
				'amount' => $_POST['payment_amount'],
				'currencyCode' => $Editor->getCurrency(),
				'orderID' => $Editor->getOrderId(),
				'description' => 'Administration Order Payment',
				'customerId' => $Editor->getCustomerId(),
				'customerEmail' => $Editor->getEmailAddress(),
				'customerTelephone' => $Editor->getTelephone(),
				'customerFirstName' => (isset($name[0])?$name[0]:''),
				'customerLastName' => (isset($name[1])?$name[1]:''),
				'customerStreetAddress' => $Address->getStreetAddress(),
				'customerPostcode' => $Address->getPostcode(),
				'customerCity' => $Address->getCity(),
				'customerState' => $Address->getState(),
				'customerCountry' => $Address->getCountry()
			);
			if(sysConfig::get('ACCOUNT_COMPANY') == 'true'){
				$RequestData['customerCompany'] = $Address->getCompany();
			}
		}
		
		if (isset($_POST['payment_cc_number']) && $_POST['payment_cc_number']!='' && $_POST['payment_cc_cvv']!=''){
			$RequestData['cardNum'] = $_POST['payment_cc_number'];
			$RequestData['cardExpDate'] = $_POST['cardExpMonth'].$_POST['cardExpYear'];
			$expDate[0] = $_POST['cardExpYear'];
			$expDate[1] = $_POST['cardExpMonth'];
			if(count($expDate) == 2){
				$RequestData['cardExpDateCIM'] = $expDate[0].'-'.$expDate[1];
			}

			$RequestData['cardCvv'] = $_POST['payment_cc_cvv'];
		}

		if (is_null($CollectionObj) === false){
			$Module->logToCollection($CollectionObj);
			$RequestData['orderID'] = $CollectionObj->orders_id;
		}
		$success = $Module->sendPaymentRequest($RequestData);
		if ($success === true){
			return true;
		}else{
			return array(
				'error_message' => $Module->getErrorMessage()
			);
		}
	}

	public function refundPayment($moduleName, $history_id, $amount, &$CollectionObj = null){
		global $Editor;
		$Module = OrderPaymentModules::getModule($moduleName);
		if (is_null($CollectionObj) === false){
			$Module->logToCollection($CollectionObj);
		}

		$Qhistory = Doctrine_Query::create()
		->from('OrdersPaymentsHistory')
		->where('payment_history_id = ?', $history_id)
		->limit(1)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		$paymentHistory = $Qhistory[0];

		$requestData = array(
			'amount' => -((isset($amount)?$amount:$paymentHistory['payment_amount'])),
			'orderID' => $paymentHistory['orders_id'],
			'transactionID' => $paymentHistory['gateway_message'],
			'cardDetails' => unserialize(cc_decrypt($paymentHistory['card_details']))
		);

		$success = $Module->refundPayment($requestData);
		if ($success === true){
			return true;
		}else{
			return array(
				'error_message' => $Module->getErrorMessage()
			);
		}
	}
	
	public function edit(){
		global $currencies, $Editor;
		$paymentHistoryTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->addClass('paymentsTable')
		->css('width', '100%');

		$hasCard = false;
		foreach($this->History as $paymentHistory){
			$cardInfo = false;
			$hasCard = false;
		
			if (array_key_exists('card_details', $paymentHistory) && is_null($paymentHistory['card_details']) === false){
				$cardInfo = unserialize(cc_decrypt($paymentHistory['card_details']));
				if (empty($cardInfo['cardNumber'])){
					unset($cardInfo);
				}
			}
		
			if ($paymentHistory['success'] == 0){
				$iconClass = 'ui-icon-closethick';
			}elseif ($paymentHistory['success'] == 1){
				$iconClass = 'ui-icon-check';
			}elseif ($paymentHistory['success'] == 2){
				$iconClass = 'ui-icon-alert';
			}
			
			$rowColumns = array(
				array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none'
					),
					'text' => tep_date_short($paymentHistory['date_added'])
				),
				array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => $paymentHistory['payment_method']),
				array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => stripslashes($paymentHistory['gateway_message'])
				),
				array(
					'addCls' => 'ui-widget-content',
					'align' => 'center',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => '<span class="ui-icon ' . $iconClass . '">'
				),
				array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => $currencies->format($paymentHistory['payment_amount'])
				)
			);
			
			//if (array_key_exists('card_details', $paymentHistory) && $paymentHistory['card_details'] != 'null'){
				$hasCard = true;
				$rowColumns[] = array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => (isset($cardInfo) && is_array($cardInfo) ? $cardInfo['cardNumber'] : '')
				);
				$rowColumns[] = array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => (isset($cardInfo) && is_array($cardInfo) ? $cardInfo['cardExpMonth'] . ' / ' . $cardInfo['cardExpYear'] : '')
				);
				$rowColumns[] = array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => (isset($cardInfo) && is_array($cardInfo) && isset($cardInfo['cardCvvNumber']) ? $cardInfo['cardCvvNumber'] : 'N/A')
				);
				$rowColumns[] = array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => htmlBase::newElement('button')->addClass('paymentRefundButton')->setText('Refund')->attr('data-payment_module', $paymentHistory['payment_module'])->attr('data-payment_history_id', $paymentHistory['payment_history_id'])->draw()
				);

			//}

			$paymentHistoryTable->addBodyRow(array(
				'columns' => $rowColumns
			));
			unset($cardInfo);
		}
	
		$headerColumns = array(
			array(
				'addCls' => 'main ui-widget-header',
				'align' => 'left',
				'text' => 'Date Added'
			),
			array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'Payment Method'
			),
			array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'Message'
			),
			array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'Status'
			),
			array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'Amount Paid'
			)
		);
	
		//if ($hasCard === true){
			$headerColumns[] = array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'Card Number'
			);
			$headerColumns[] = array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'Exp Date'
			);
			$headerColumns[] = array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'CVV Code'
			);

		
		$headerColumns[] = array(
			'addCls' => 'main ui-widget-header',
			'css' => array(
				'border-left' => 'none'
			),
			'text' => '&nbsp;'
		);
		

		
		$PaymentMethodDrop = htmlBase::newElement('selectbox')
		->setName('payment_method')
		->css('width', '300px');

		foreach(OrderPaymentModules::getModules() as $Module){
			if ($Module->hasFormUrl() === false){
				$PaymentMethodDrop->addOption($Module->getCode(), $Module->getTitle());
			}
		}
	
		$headerPaymentCols = array(
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'align' => 'left',
				'css' => array(
					'border-top' => 'none'
				),
				'text' => date('m/d/Y')
			),
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'css' => array(
					'border-top' => 'none',
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => $PaymentMethodDrop->draw()
			),
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'css' => array(
					'border-top' => 'none',
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => '&nbsp;'
			),
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'css' => array(
					'border-top' => 'none',
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => '&nbsp;'
			),
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'css' => array(
					'border-top' => 'none',
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => '<input type="text" class="ui-widget-content" name="payment_amount" size="10">'
			),
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'css' => array(
					'border-top' => 'none',
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => '<input type="text" class="ui-widget-content" name="payment_cc_number" size="18" maxlength="16">'
			),
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'css' => array(
					'border-top' => 'none',
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => '<input type="text" class="ui-widget-content" name="payment_cc_expires" size="6" maxlength="4">'
			),
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'css' => array(
					'border-top' => 'none',
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => '<input type="text" class="ui-widget-content" name="payment_cc_cvv" size="4" maxlength="4">'
			),
			array(
				'addCls' => 'ui-widget-content ui-state-hover',
				'css' => array(
					'border-top' => 'none',
					'border-left' => 'none'
				),
				'text' => (isset($_GET['oID']) ? htmlBase::newElement('button')->addClass('paymentProcessButton')->setText('Process') : 'Will process on save')
			)
		);
		
		$paymentHistoryTable->addHeaderRow(array(
			'columns' => $headerColumns
		));
	
		$paymentHistoryTable->addHeaderRow(array(
			'columns' => $headerPaymentCols
		));
		
		return $paymentHistoryTable;
	}
}
?>