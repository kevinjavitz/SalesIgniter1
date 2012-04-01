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
		if(isset($WidgetSettings->image_source)){
			return '<img src="' . fixImagesPath($WidgetSettings->image_source) . '" />';
		}else{
			$return = '';
			if (isset($WidgetSettings->images) && sizeof($WidgetSettings->images) == 1){
				foreach($WidgetSettings->images as $iInfo){
					if (isset($iInfo->image->{Session::get('languages_id')})){
						$return = '<img src="' . fixImagesPath($iInfo->image->{Session::get('languages_id')}) . '" />';
						break;
					}

				}
			}else{
				$return = $this->getBoxCode() . '<br>' . sizeof($WidgetSettings->images) . ' Images';
			}
			return $return;
		}
	}

	public function show(){
			global $appExtension;
			$boxWidgetProperties = $this->getWidgetProperties();
			if(isset($boxWidgetProperties->image_source)){
				$htmlLink = '';
				if (!empty($boxWidgetProperties->image_link)){
					ob_start();
				if(strpos($boxWidgetProperties->image_link,'itw_app_link') === false){
					eval("?>".'<?php echo "'.$boxWidgetProperties->image_link.'";?>');
				}else{
					eval("?>".'<?php echo '.$boxWidgetProperties->image_link.';?>');
				}
					$htmlLink = ob_get_contents();
					ob_end_clean();
				}

			
				if($htmlLink == ''){
					$htmlText =  '<img src="'.fixImagesPath($boxWidgetProperties->image_source).'"/>';
				}else{
					$htmlText = '<a href="'.$htmlLink.'">'. '<img src="'.fixImagesPath($boxWidgetProperties->image_source).'"/></a>';
				}
			}else{
			
				$ImageHtml = array();
				foreach($boxWidgetProperties->images as $iInfo){
					$linkInfo = $iInfo->link;
			
					$ImageEl = htmlBase::newElement('image')
						->setSource(fixImagesPath($iInfo->image->{Session::get('languages_id')}));
			
					if ($linkInfo !== false){
						$LinkEl = htmlBase::newElement('a')
							->append($ImageEl);
						$this->parseLink($LinkEl, $linkInfo);

						$ImageHtml[] = $LinkEl->draw();
					}else{
						$ImageHtml[] = $ImageEl->draw();
					}
				}
				$htmlText = implode('', $ImageHtml);
			}	
			$this->setBoxContent($htmlText);
			return $this->draw();
	}
	private function parseLink(&$LinkEl, $lInfo){
		if ($lInfo->type == 'app'){
			$getParams = null;
			if (stristr($lInfo->application, '/')){
				$extInfo = explode('/', $lInfo->application);
				$application = $extInfo[1];
				$getParams = 'appExt=' . $extInfo[0];
			}
			else {
				$application = $lInfo->application;
			}

			$LinkEl->setHref(itw_app_link($getParams, $application, $lInfo->page));
		}
		elseif ($lInfo->type == 'category'){
			$LinkEl->setHref(itw_app_link($lInfo->get_vars, $lInfo->application, $lInfo->page));
		}
		elseif ($lInfo->type == 'custom') {
			$LinkEl->setHref($lInfo->url);
		}

		if ($lInfo->type != 'none'){
			if ($lInfo->target == 'new'){
				$LinkEl->attr('target', '_blank');
			}
			elseif ($lInfo->target == 'dialog') {
				$LinkEl->attr('onclick', 'Javascript:popupWindow(this.href);');
			}
		}
	}
}
?>