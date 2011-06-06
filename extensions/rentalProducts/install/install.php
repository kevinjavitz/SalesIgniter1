<?php
/*
	Rental Products Extension Version 1
	
	I.T. Web Experts, SalesIgniter v1
	http://www.itwebexperts.com

	Copyright (c) 2011 I.T. Web Experts

	This script and it's source is not redistributable
*/

class rentalProductsInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('rentalProducts');
	}
	
	public function install(){
		if (sysConfig::exists('EXTENSION_RENTAL_PRODUCTS_ENABLED') === true) return;
		
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_RENTAL_PRODUCTS_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>