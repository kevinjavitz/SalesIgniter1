<?php
/*
	Stream Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class streamProductsInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('streamProducts');
	}
	
	public function install(){
		if (sysConfig::exists('EXTENSION_STREAMPRODUCTS_ENABLED') === true) return;
		
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_STREAMPRODUCTS_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>