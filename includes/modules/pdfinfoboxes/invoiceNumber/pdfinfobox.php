<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxInvoiceNumber extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('invoiceNumber');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(isset($_GET['oID'])){
		        $oID = $_GET['oID'];



				switch($boxWidgetProperties->type){
					case 'top': $htmlText = $boxWidgetProperties->text.'<br/>'.$oID;
								break;
					case 'bottom': $htmlText = $oID.'<br/>'.$boxWidgetProperties->text;
								break;
					case 'left': $htmlText = $boxWidgetProperties->text.$oID;
								break;
					case 'right': $htmlText = $oID.$boxWidgetProperties->text;
								break;
				}
			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>