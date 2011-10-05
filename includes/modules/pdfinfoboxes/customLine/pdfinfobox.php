<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxCustomLine extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('customLine');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';
			$lineWidth = '';
			for($i=0;$i<$boxWidgetProperties->width;$i++){
				$lineWidth .= '_';
			}
			switch($boxWidgetProperties->type){
				case 'top': $htmlText = $boxWidgetProperties->text.'<br/>'.$lineWidth;
							break;
				case 'bottom': $htmlText = $lineWidth.'<br/>'.$boxWidgetProperties->text;
				            break;
				case 'left': $htmlText = $boxWidgetProperties->text.$lineWidth;
				            break;
				case 'right': $htmlText = $lineWidth.$boxWidgetProperties->text;
				            break;
			}

			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>