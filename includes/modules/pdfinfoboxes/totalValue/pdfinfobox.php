<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxTotalValue extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('totalValue');
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