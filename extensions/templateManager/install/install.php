<?php
/*
	Waiting List Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class templateManagerInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('templateManager');
	}
	
	public function install(){

		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_TEMPLATEMANAGER_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>