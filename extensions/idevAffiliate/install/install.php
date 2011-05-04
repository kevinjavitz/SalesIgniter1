<?php

class idevAffiliateInstall extends extensionInstaller {

	public function __construct(){
		parent::__construct('idevAffiliate');
	}

	public function install(){
		if (defined('EXTENSION_IDEVAFFILIATE_ENABLED')) return;

		parent::install();
	}

	public function uninstall($remove = false){
		if (!defined('EXTENSION_IDEVAFFILIATE_ENABLED')) return;

		parent::uninstall($remove);
	}
}

?>
