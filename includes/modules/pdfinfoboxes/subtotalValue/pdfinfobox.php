<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxSubtotalValue extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('subtotalValue');
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
				->andWhereIn('ot.module_type', array('subtotal', 'ot_subtotal'))
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			    /*->leftJoin('o.OrdersAddresses a')
			    ->leftJoin('o.OrdersStatus s')
			    ->leftJoin('s.OrdersStatusDescription sd')
			    ->where('sd.language_id = ?', Session::get('languages_id'))
			    ->andWhere('o.orders_status != ?', sysConfig::get('ORDERS_STATUS_ESTIMATE_ID'))
			    ->andWhereIn('ot.module_type', array('total', 'ot_total'))
			    ->andWhere('a.address_type = ?', 'customer')
			    ->orderBy('o.date_purchased desc');
		        EventManager::notify('AdminOrdersListingBeforeExecute', &$Qorders);
				*/

				$totalValue = $currencies->format($Qorders[0]['OrdersTotal'][0]['value']);

				switch($boxWidgetProperties->type){
					case 'top': $htmlText = $boxWidgetProperties->text.'<br/>'.$totalValue;
								break;
					case 'bottom': $htmlText = $totalValue.'<br/>'.$boxWidgetProperties->text;
								break;
					case 'left': $htmlText = $boxWidgetProperties->text.$totalValue;
								break;
					case 'right': $htmlText = $totalValue.$boxWidgetProperties->text;
								break;
				}
			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>