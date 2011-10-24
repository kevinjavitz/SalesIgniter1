<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxInsuranceValue extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('insuranceValue');
	}

	public function show(){
			global $appExtension, $currencies;
			$oID = isset($_GET['oID'])?$_GET['oID']:'';
			$boxWidgetProperties = $this->getWidgetProperties();
			$insurance = 'Decline';
			if(!empty($oID)){
				$Qorders = Doctrine_Query::create()
					->from('Orders o')
					->leftJoin('o.OrdersProducts op')
					->leftJoin('op.OrdersProductsReservation opr')
					->where('orders_id=?', $oID)
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
				$ins = 0;
				foreach($Qorders as $oInfo){
					foreach($oInfo['OrdersProducts'] as $opInfo){
						foreach($opInfo['OrdersProductsReservation'] as $oprInfo){
							$ins += $oprInfo['insurance'];
						}
					}
				}
				if($ins > 0){
					$insurance = 'Yes: '.$currencies->format($ins);
				}
			}

			$this->setBoxContent($insurance);
			return $this->draw();
	}
}
?>