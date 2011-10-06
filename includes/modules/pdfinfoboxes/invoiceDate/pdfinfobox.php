<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxInvoiceDate extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('invoiceDate');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			if(isset($_GET['oID'])){
		        $oID = $_GET['oID'];

				$Qorders = Doctrine_Query::create()
				->from('Orders o')
				->where('orders_id=?', $oID)
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			    /*->leftJoin('o.OrdersTotal ot')
			    ->leftJoin('o.OrdersAddresses a')
			    ->leftJoin('o.OrdersStatus s')
			    ->leftJoin('s.OrdersStatusDescription sd')
			    ->where('sd.language_id = ?', Session::get('languages_id'))
			    ->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'))
			    ->andWhereIn('ot.module_type', array('total', 'ot_total'))
			    ->andWhere('a.address_type = ?', 'customer')
			    ->orderBy('o.date_purchased desc');
		        EventManager::notify('AdminOrdersListingBeforeExecute', &$Qorders);
				*/


				if($boxWidgetProperties->short == 'short'){
					$invDate =  strftime(sysLanguage::getDateFormat('short'), strtotime($Qorders[0]['date_purchased']));
				}else{
					$invDate =  strftime(sysLanguage::getDateFormat('long'), strtotime($Qorders[0]['date_purchased']));
				}

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