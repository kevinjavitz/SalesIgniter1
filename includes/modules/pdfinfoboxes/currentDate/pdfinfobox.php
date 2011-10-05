<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class PDFInfoBoxCurrentDate extends PDFInfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('currentDate');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlText = '';

			if($boxWidgetProperties->short == 'short'){
				$curDate =  strftime(sysLanguage::getDateFormat('short'), strtotime(date('Y-m-d')));
			}else{
				$curDate =  strftime(sysLanguage::getDateFormat('long'), strtotime(date('Y-m-d')));
			}

			switch($boxWidgetProperties->type){
				case 'top': $htmlText = $boxWidgetProperties->text.'<br/>'.$curDate;
							break;
				case 'bottom': $htmlText = $curDate.'<br/>'.$boxWidgetProperties->text;
				            break;
				case 'left': $htmlText = $boxWidgetProperties->text.$curDate;
				            break;
				case 'right': $htmlText = $curDate.$boxWidgetProperties->text;
				            break;
			}

			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>