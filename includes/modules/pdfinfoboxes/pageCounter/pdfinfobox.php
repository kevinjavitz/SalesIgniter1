<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxPageCounter extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('pageCounter');
	}

	public function show(){
			global $appExtension;
			//$boxWidgetProperties = $this->getWidgetProperties();
			$this->setBoxContent('<span class="page-number"></span>');
			return $this->draw();
	}
}
?>