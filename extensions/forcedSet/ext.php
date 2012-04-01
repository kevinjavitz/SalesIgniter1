<?php
/*
	Forced Set Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_forcedSet extends ExtensionBase {
	
	public function __construct(){
		parent::__construct('forcedSet');
	}
	
	public function init(){
		global $App, $appExtension, $Template;
		if ($this->isEnabled() === false) return;
		/*
		* Shopping Cart Actions --BEGIN--
		*/
		require(dirname(__FILE__) . '/classEvents/ShoppingCart.php');
		$eventClass = new ShoppingCart_forcedSet();
		$eventClass->init();
	}
	 
	

}

?>