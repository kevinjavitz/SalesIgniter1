<?php
/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class infoPages_catalog_index extends Extension_infoPages {
	protected $pageInfo;
	
	public function __construct(){
		parent::__construct();
		
		if (basename($_SERVER['PHP_SELF']) != 'index.php'){
			$this->enabled = false;
		}
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvent('IndexDefaultCustomerGreeting', null, $this);
	}
	
	public function IndexDefaultCustomerGreeting($pagesId = null){
		if ($this->isEnabled() === false) return;
		
		if (is_null($pagesId) === false){
			$Query = $this->getInfoPage($pagesId)->toArray();
		}else{
			$Query = $this->getInfoPage(1)->toArray();
		}
		
		$messageContainer = htmlBase::newElement('div')
		->addClass('main')
		->html($Query['PagesDescription'][Session::get('languages_id')]['pages_html_text']);
		
		return $messageContainer->draw();
	}
}
?>