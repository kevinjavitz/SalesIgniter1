<?php
/*
	Product Custom Fields Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class ordersProductsNotesInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('ordersProductsNotes');
	}
	
	public function install(){

		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_ORDERS_PRODUCTS_NOTES_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>