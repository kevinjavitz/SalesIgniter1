<?php

class idevAffiliateInstall extends extensionInstaller {

	public function __construct(){
		parent::__construct('idevAffiliate');
	}

	public function install(){
		if (sysConfig::exists('EXTENSION_IDEVAFFILIATE_ENABLED') === true) return;

		parent::install();
	}

	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_IDEVAFFILIATE_ENABLED') === false) return;

		parent::uninstall($remove);
	}
}

?>
