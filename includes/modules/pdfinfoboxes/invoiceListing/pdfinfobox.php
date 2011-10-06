<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxInvoiceListing extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('invoiceListing');
	}

	public function show(){
			global $appExtension, $currencies;
			$oID = isset($_GET['oID'])?$_GET['oID']:'';
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(!empty($oID)){
				$Order = new Order($oID);
				$htmlText = $Order->listProducts($boxWidgetProperties->tableHeading,$boxWidgetProperties->showQty, $boxWidgetProperties->showBarcode, $boxWidgetProperties->showModel,$boxWidgetProperties->showName,$boxWidgetProperties->showExtraInfo,$boxWidgetProperties->showPrice,$boxWidgetProperties->showPriceTax,$boxWidgetProperties->showTotal,$boxWidgetProperties->showTotalTax,$boxWidgetProperties->showTax)->draw();
			}


			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>