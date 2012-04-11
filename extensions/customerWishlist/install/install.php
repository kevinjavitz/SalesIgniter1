<?php
/*
	Customer Wishlist Version 1.0
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class customerWishlistInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('customerWishlist');
	}
	
	public function install(){
		if (sysConfig::exists('EXTENSION_CUSTOMER_WISHLIST_ENABLED') === true) return;
		
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_CUSTOMER_WISHLIST_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>