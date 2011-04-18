<?php
/*
	Multi Store Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class multiStoreInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('multiStore');
	}

	public function install(){
		if (sysConfig::exists('EXTENSION_MULTI_STORE_ENABLED') === true) return;
		
		parent::install();
		
		$storeInfo = array(
			'stores_name'       => 'Default Store',
			'stores_domain'     => $_SERVER['HTTP_HOST'],
			'stores_ssl_domain' => $_SERVER['HTTP_HOST'],
			'stores_email'      => STORE_OWNER_EMAIL_ADDRESS,
			'stores_template'   => 'newred'
		);

		$Qstore = new Stores();
		$Qstore->fromArray($storeInfo);
		$Qstore->save();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_MULTI_STORE_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>