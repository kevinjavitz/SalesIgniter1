<?php
require(dirname(__FILE__) . '/AddressManager/Base.php');
require(dirname(__FILE__) . '/ProductManager/Base.php');
require(dirname(__FILE__) . '/TotalManager/Base.php');
require(dirname(__FILE__) . '/PaymentManager/Base.php');

/**
 * Order Class
 * @package Order
 */
class Order {
	protected $mode = 'details';
	protected $Order = null;
	protected $orderId = null;
	protected $customerId = null;

	public function __construct($orderId = null){
		if (is_null($orderId) === false){
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
			->orderBy('ot.sort_order ASC, osh.date_added DESC');

			EventManager::notify('OrderQueryBeforeExecute', &$Qorder);

			//echo $Qorder->getSqlQuery();
			$Order = $Qorder->execute()->toArray();
			$this->Order = $Order[0];
			$this->customerId = $this->Order['customers_id'];

			$this->AddressManager = new OrderAddressManager($this->Order['OrdersAddresses']);
			$this->AddressManager->setOrderId($this->Order['orders_id']);

			$this->ProductManager = new OrderProductManager($this->Order['OrdersProducts']);
			$this->ProductManager->setOrderId($this->Order['orders_id']);

			$this->TotalManager = new OrderTotalManager($this->Order['OrdersTotal']);
			$this->TotalManager->setOrderId($this->Order['orders_id']);

			$this->PaymentManager = new OrderPaymentManager($this->Order['OrdersPaymentsHistory']);
			$this->PaymentManager->setOrderId($this->Order['orders_id']);
		}
	}

	public function setOrderId($val){
		$this->orderId = $val;
	}
	
	public function getOrderId(){
		return $this->orderId;
	}

	public function getCustomerId(){
		return $this->customerId;
	}

	public function getOrderInfo(){
		return $this->Order;
	}
	
	public function getCurrency(){
		return $this->Order['currency'];
	}
	
	public function getCurrencyValue(){
		return $this->Order['currency_value'];
	}
	
	public function hasTaxes(){
		return ($this->TotalManager->getTotalValue('tax') > 0);
	}
	
	public function hasShippingMethod(){
		return (empty($this->Order['shipping_method']) === false);
	}
	
	public function getShippingMethod(){
		return $this->Order['shipping_method'];
	}
	
	public function getTotal(){
		return $this->TotalManager->getTotalValue('total');
	}

	public function getCurrentStatus($isID = false){
		/*
		 * DO NOT CHANGE FROM 0, IT IS ORDERED DESC SO 0 WILL ALWAYS ME THE MOST RECENT
		 */
		if (isset($this->Order['OrdersStatusHistory'][0])){
			if($isID === false){
				return $this->Order['OrdersStatusHistory'][0]['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_name'];
			}else{
				return $this->Order['OrdersStatusHistory'][0]['OrdersStatus']['OrdersStatusDescription'][Session::get('languages_id')]['orders_status_id'];
			}
		}
		return null;
		//return $this->Order['orders_status'];
	}
	
	public function getDatePurchased(){
		return $this->Order['date_purchased'];
	}

	public function hasStatusHistory(){
		$history = $this->getStatusHistory();
		return (!empty($history));
	}

	public function getStatusHistory(){
		return $this->Order['OrdersStatusHistory'];
	}

	public function listPaymentHistory(){
		return $this->PaymentManager->show();
	}

	public function listTotals(){
		return $this->TotalManager->show();
	}

	public function getFormattedAddress($type){
		return $this->AddressManager->getFormattedAddress($type, true);
	}
	
	public function listAddresses(){
		return $this->AddressManager->listAll();
	}

	public function getProducts(){
		return $this->ProductManager->getContents();
	}

	public function listProducts(){
		return $this->ProductManager->listProducts();
	}

	public function getTelephone(){
		$telephone = '';
		if (isset($this->Order['customers_telephone'])){
			$telephone = $this->Order['customers_telephone'];
		}
		return $telephone;
	}

	public function getIPAddress(){
		$ip = '';
		if (isset($this->Order['ip_address'])){
			$ip = $this->Order['ip_address'];
		}
		return $ip;
	}

	public function getEmailAddress(){
		$email = '';
		if (isset($this->Order['customers_email_address'])){
			$email = $this->Order['customers_email_address'];
		}
		return $email;
	}
}
?>