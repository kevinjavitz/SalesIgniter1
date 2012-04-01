<?php
/*
	Reviews Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_reviews extends ExtensionBase {
			  
	public function __construct(){
		parent::__construct('reviews');
	}
	
	public function init(){
		global $appExtension;
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'ProductInfoButtonBarAddButton'
		), null, $this);
	}
	
	public function ProductInfoButtonBarAddButton($product){
		/*return htmlBase::newElement('button')
		->css('float', 'right')
		->setText(sysLanguage::get('TEXT_BUTTON_REVIEWS'))
		->setHref(itw_app_link('appExt=reviews&products_id=' . $product->getId(), 'product_review', 'default'))
		->draw();*/
		return '';
	}
}
?>