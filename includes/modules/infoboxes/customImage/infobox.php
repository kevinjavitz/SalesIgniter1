<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class InfoBoxCustomImage extends InfoBoxAbstract {

	public function __construct(){
		global $App;
		$this->init('customImage');
	}

	public function showLayoutPreview($WidgetSettings){
		$htmlCode = $WidgetSettings->image_source;
		if(sysConfig::getDirWsCatalog() == '/' || (strpos($htmlCode, sysConfig::getDirWsCatalog()) === 0)){
			$imgPath = $htmlCode;
		}else{
			$imgPath = sysConfig::getDirWsCatalog() .$htmlCode;
		}
		$imgPath = str_replace('//','/', $imgPath);
		return '<img src="' . $imgPath . '" />';
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			$htmlCode = $boxWidgetProperties->image_source;
			$htmlLink = '';
			if (!empty($boxWidgetProperties->image_link)){
				ob_start();
				eval("?>".'<?php echo "'.$boxWidgetProperties->image_link.'";?>');
				$htmlLink = ob_get_contents();
				ob_end_clean();
			}
		    if(sysConfig::getDirWsCatalog() == '/' || (strpos($htmlCode, sysConfig::getDirWsCatalog()) === 0)){
				$imgPath = $htmlCode;
		    }else{
			    $imgPath = sysConfig::getDirWsCatalog() .$htmlCode;
		    }
			$imgPath = str_replace('//','/', $imgPath);
			
			if($htmlLink == ''){
				$htmlText =  '<img src="'.$imgPath.'"/>';
			}else{
				$htmlText = '<a href="'.$htmlLink.'">'. '<img src="'.$imgPath.'"/></a>';
			}
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
}
?>