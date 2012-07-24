<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxAllTotalsValue extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('allTotalsValue');
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
				->orderBy('sort_order')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

				foreach($Qorders as $iOrder){
					foreach($iOrder['OrdersTotal'] as $iTotal){
						$text = $iTotal['title'];
						$totalValue = $currencies->format($iTotal['value']);
						$htmlText .= '<div style="margin-top:7px;">';
						switch($boxWidgetProperties->type){
							case 'top': $htmlText .= $text.'<br/>'.$totalValue;
										break;
							case 'bottom': $htmlText .= $totalValue.'<br/>'.$text;
										break;
							case 'left': $htmlText .= $text.'  '.$totalValue;
										break;
							case 'right': $htmlText .= $totalValue.'  '.$text;
										break;
						}
						$htmlText .= '</div>';
					}
				}
			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>