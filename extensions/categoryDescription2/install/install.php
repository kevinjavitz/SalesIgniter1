<?php
/*
	Categories Description 2 Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class categoryDescription2Install extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('categoryDescription2');
	}
	
	public function install(){
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_CATEGORIES_DESCRIPTION2_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>