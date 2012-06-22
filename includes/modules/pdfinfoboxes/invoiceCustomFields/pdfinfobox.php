<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxInvoiceCustomFields extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('invoiceCustomFields');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(isset($_GET['oID'])){
		        $oID = $_GET['oID'];

				$QCustomFields = Doctrine_Query::create()
						->from('OrdersCustomFieldsToOrders')
						->where('orders_id =?',$oID)
						->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				$additionalDetails = '';
				foreach($QCustomFields as $customFields){
						$additionalDetails .= '<b>'.$customFields['field_label'] .'</b>:'.$customFields['value'].'<br/>';
				}

				$invDate = $additionalDetails;

				switch($boxWidgetProperties->type){
					case 'top': $htmlText = $boxWidgetProperties->text.'<br/>'.$invDate;
								break;
					case 'bottom': $htmlText = $invDate.'<br/>'.$boxWidgetProperties->text;
								break;
					case 'left': $htmlText = $boxWidgetProperties->text.$invDate;
								break;
					case 'right': $htmlText = $invDate.$boxWidgetProperties->text;
								break;
				}
			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>