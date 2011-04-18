<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCustomText extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('customText');
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$page_id = $boxWidgetProperties->selected_page;
			$infoPageExt = $appExtension->getExtension('infoPages');
			$htmlText  = $infoPageExt->displayContentBlock($page_id);
			//print_r($htmlPage);
//			$htmlText = $htmlPage['PagesDescription'][Session::get('languages_id')]['pages_html_text'];
			$this->setBoxContent($htmlText);
			if($this->getBoxHeading() == ''){
				//$this->setBoxHeading($htmlPage['PagesDescription'][Session::get('languages_id')]['pages_title']);
			}
			return $this->draw();
	}
}
?>