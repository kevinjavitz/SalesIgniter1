<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxWysiwygBlock extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('wysiwygBlock');
	}

	public function show(){
			global $appExtension;
			$WidgetProperties = $this->getWidgetProperties();
			$this->setBoxContent($WidgetProperties->block_html);
			return $this->draw();
	}
}
?>