<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxPaymentListing extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('paymentListing');
	}

	public function show(){
			global $appExtension, $currencies;
			$oID = isset($_GET['oID'])?$_GET['oID']:'';
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(!empty($oID)){
				$Order = new Order($oID);
				$htmlText = $Order->listPaymentHistory(false)->draw();
			}


			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>