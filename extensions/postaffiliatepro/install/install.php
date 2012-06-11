<?php

class postaffiliateproInstall extends extensionInstaller {

	public function __construct(){
		parent::__construct('postaffiliatepro');
	}

	public function install(){

		parent::install();
	}

	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_PAP_ENABLED') === false) return;

		parent::uninstall($remove);
	}
}

?>
