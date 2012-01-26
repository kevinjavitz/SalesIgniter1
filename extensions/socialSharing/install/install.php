<?php
/*
	Google Analytics Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class socialSharingInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('socialSharing');
	}
	
	public function install(){
		if (sysConfig::exists('EXTENSION_SOCIAL_SHARING_ENABLED') === true) return;
		
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_SOCIAL_SHARING_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>