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
			$insVal = isset($_GET['insValue'])?$_GET['insValue']:'';

			if(!empty($insVal)){
				$insurance = 'Yes: '.$currencies->format($insVal);
			}else{
				$insurance = 'Decline';
			}
			$boxWidgetProperties = $this->getWidgetProperties();

			$this->setBoxContent($insurance);
			return $this->draw();
	}
}
?>