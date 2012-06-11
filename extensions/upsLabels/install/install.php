<?php


class upsLabelsInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('upsLabels');
	}
	
	public function install(){

		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_UPSLABELS_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>