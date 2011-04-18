<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCustomPHP extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('customPHP');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlCode = $boxWidgetProperties->php_text;
			ob_start();
			eval("?>".$htmlCode);
			$htmlText = ob_get_contents();
			ob_end_clean();
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>