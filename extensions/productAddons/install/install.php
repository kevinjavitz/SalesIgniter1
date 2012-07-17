<?php


class productAddonsInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('productAddons');
	}
	
	public function install(){

		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_PRODUCT_ADDONS_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>