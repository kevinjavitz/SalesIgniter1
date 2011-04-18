<?php
/**
 * Payment manager for the order class
 *
 * @package Order
 * @author Stephen Walker <stephen@itwebexperts.com>
 * @copyright Copyright (c) 2010, I.T. Web Experts
 */

class OrderPaymentManager {
	protected $orderId = null;
	protected $History = array();
	protected $PaymentsTotal = 0;
	protected $PendingPaymentsTotal = 0;
	
	public function __construct($PaymentHistory = null){
		if (is_null($PaymentHistory) === false){
			$this->History = $PaymentHistory;
			foreach($this->getPaymentHistory() as $hInfo){
				if ($hInfo['success'] == 1){
					$this->PaymentsTotal += $hInfo['payment_amount'];
				}elseif ($hInfo['success'] == 2){
					$this->PendingPaymentsTotal += $hInfo['payment_amount'];
				}
			}
		}
	}

	public function setOrderId($val){
		$this->orderId = $val;
	}
	
	public function getPaymentsTotal(){
		return $this->PaymentsTotal;
	}
	
	public function getPendingPaymentsTotal(){
		return $this->PendingPaymentsTotal;
	}
	
	public function getPaymentHistory(){
		return $this->History;
	}
	
	public function show(){
		global $Order, $currencies, $App;
		$paymentHistoryTable = htmlBase::newElement('table')
		->setCellPadding(3)
		->setCellSpacing(0)
		->css('width', '100%');

		$hasCard = false;
		$hasPayment = false;
		foreach($this->History as $paymentHistory){
			$cardInfo = false;
			$hasCard = false;
		    $hasPayment = true;
			if (array_key_exists('card_details', $paymentHistory) && is_null($paymentHistory['card_details']) === false){
				$cardInfo = unserialize(cc_decrypt($paymentHistory['card_details']));
				if (empty($cardInfo['cardNumber'])){
					$cardInfo = false;
				}
			}
			
			if ($App->getEnv() == 'catalog'){
				$cardInfo = false;
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
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => $currencies->format($paymentHistory['payment_amount'], true, $Order->getCurrency(), $Order->getCurrencyValue())
				)
			);
			
			if ($cardInfo !== false){
				$hasCard = true;
				$rowColumns[] = array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => (is_array($cardInfo) ? $cardInfo['cardNumber'] : '')
				);
				$rowColumns[] = array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => (is_array($cardInfo) ? $cardInfo['cardExpMonth'] . ' / ' . $cardInfo['cardExpYear'] : '')
				);
				$rowColumns[] = array(
					'addCls' => 'ui-widget-content',
					'css' => array(
						'border-top' => 'none',
						'border-left' => 'none'
					),
					'text' => (is_array($cardInfo) && isset($cardInfo['cardCvvNumber']) ? $cardInfo['cardCvvNumber'] : 'N/A')
				);
			}

			$paymentHistoryTable->addBodyRow(array(
				'columns' => $rowColumns
			));
			unset($cardInfo);
		}
	
		$headerColumns = array(
			array(
				'addCls' => 'main ui-widget-header',
				'align' => 'left',
				'text' => sysLanguage::get('TEXT_PAYMENT_HISTORY_DATE_ADDED')
			),
			array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => sysLanguage::get('TEXT_PAYMENT_HISTORY_PAYMENT_METHOD')
			),
			array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => sysLanguage::get('TEXT_PAYMENT_HISTORY_MESSAGE')
			),
			array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => sysLanguage::get('TEXT_PAYMENT_HISTORY_AMOUNT_PAID')
			)
		);
	
		if ($hasCard === true){
			$headerColumns[] = array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'Card Used'
			);
			$headerColumns[] = array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'Exp Date Used'
			);
			$headerColumns[] = array(
				'addCls' => 'main ui-widget-header',
				'css' => array(
					'border-left' => 'none'
				),
				'align' => 'left',
				'text' => 'CVV Code Used'
			);
		}
	    if ($hasPayment){
			$paymentHistoryTable->addHeaderRow(array(
				'columns' => $headerColumns
			));
		}


		return $paymentHistoryTable;

	}
}
?>