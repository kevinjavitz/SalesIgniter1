<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxInvoiceComments extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('invoiceComments');
	}

	public function show(){
			global $appExtension, $currencies;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(isset($_GET['oID'])){
		        $oID = $_GET['oID'];


		        $Qhistory = Doctrine_Query::create()
			        ->from('OrdersStatusHistory')
			        ->where('orders_id = ?', $oID)
			        ->orderBy('orders_status_history_id DESC')
			        ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				$comments = '';
				foreach($Qhistory as $oHistory){
					$comments .= nl2br(stripslashes($oHistory['comments']));
				}
				if($comments != ''){
					$htmlText = 'Order Comments:<br/><br/>'.$comments;
				}


			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>