<?php
/*
	Manage QuickBooks Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2012 I.T. Web Experts

	This script and it's source is not redistributable
*/

class manageQuickBooksInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('manageQuickBooks');
	}
	
	public function install(){

		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_MANAGE_QUICKBOOKS_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}

?>