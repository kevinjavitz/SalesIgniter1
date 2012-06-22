<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxInvoiceStatus extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('invoiceStatus');
	}

	public function show(){
			global $appExtension, $currencies;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(isset($_GET['oID'])){
		        $oID = $_GET['oID'];
		        $Qorders = Doctrine_Query::create()
			        ->from('Orders o')
			        ->leftJoin('o.OrdersTotal ot')
			        ->where('orders_id=?', $oID)
			        ->andWhereIn('ot.module_type', array('total', 'ot_total'))
			        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		        $totalValue = $Qorders[0]['OrdersTotal'][0]['value'];

		        $Qhistory = Doctrine_Query::create()
			        ->from('OrdersPaymentsHistory')
			        ->where('orders_id = ?', $oID)
				->andWhere('success =?', '1')
			        ->orderBy('payment_history_id DESC')
			        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				$paidValue = 0;
				foreach($Qhistory as $oHistory){
					$paidValue += $oHistory['payment_amount'];
				}

				if($paidValue >= $totalValue){
					$htmlText = 'PAID';
				}else{
					$htmlText = 'NOT PAID ('.$currencies->format($totalValue-$paidValue).' still to be paid)';
				}

			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>