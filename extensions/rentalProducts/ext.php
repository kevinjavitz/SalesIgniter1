<?php
/*
	Rental Products Extension Version 1

	I.T. Web Experts, SalesIgniter v1
	http://www.itwebexperts.com

	Copyright (c) 2011 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_rentalProducts extends ExtensionBase {
			  
	public function __construct(){
		parent::__construct('rentalProducts');
	}
	
	public function init(){
		global $appExtension;
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'ProductInfoButtonBarAddButton'
		), null, $this);
	}
}
?>