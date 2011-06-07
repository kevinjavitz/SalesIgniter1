<?php
/*
	Related Products Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class packagesInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('packages');
	}
	
	public function install(){
		if (sysConfig::exists('EXTENSION_PACKAGES_ENABLED') === true) return;
		
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_PACKAGES_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>