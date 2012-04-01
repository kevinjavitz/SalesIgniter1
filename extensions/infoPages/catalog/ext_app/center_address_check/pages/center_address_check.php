<?php
/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class infoPages_catalog_center_address_check extends Extension_infoPages {
	protected $pageInfo;
	
	public function __construct(){
		parent::__construct();
		
		if (basename($_SERVER['PHP_SELF']) != 'center_address_check.php'){
			$this->enabled = false;
		}
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvent('CenterAddressCheckAfterTitle', null, $this);
	}
	
	public function CenterAddressCheckAfterTitle(&$template){
		if ($this->isEnabled() === false) return;
		$Query = $this->getInfoPage(14);
		
		$messageContainer = htmlBase::newElement('div')
		->addClass('main')
		->html($Query[0]['PagesDescription'][0]['pages_html_text']);
		
		return $messageContainer->draw();
	}
}
?>