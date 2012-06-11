<?php


class subAccountsInstall extends extensionInstaller {

	public function __construct(){
		parent::__construct('subAccounts');
	}

	public function install(){

		parent::install();
	}

	public function uninstall($remove = false){
		if (!defined('EXTENSION_SUBACCOUNTS_ENABLED')) return;

		parent::uninstall($remove);
	}
}

?>
