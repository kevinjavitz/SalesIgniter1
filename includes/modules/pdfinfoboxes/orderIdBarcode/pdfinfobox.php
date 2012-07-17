<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxorderIdBarcode extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('orderIdBarcode');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(isset($_GET['oID'])){
		        $oID = $_GET['oID'];

                $htmlText = '<barcode code="'.$oID.'" type="C39" class="barcode" />';
			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>