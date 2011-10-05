<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxPageBreak extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('pageBreak');
	}

	public function show(){
			global $appExtension;
			//$boxWidgetProperties = $this->getWidgetProperties();
			$this->setBoxContent('<hr>');
			return $this->draw();
	}
}
?>