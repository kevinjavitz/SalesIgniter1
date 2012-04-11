<?php
/*
	Customer Wishlist Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class Extension_customerWishlist extends ExtensionBase {

	public function __construct(){
		parent::__construct('customerWishlist');
	}

	public function init(){
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
		), null, $this);
	}

}
?>