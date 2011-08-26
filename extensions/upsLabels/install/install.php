<?php


class upsLabelsInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('upsLabels');
	}
	
	public function install(){
		if (sysConfig::exists('EXTENSION_UPSLABELS_ENABLED') === true) return;
		
		parent::install();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_UPSLABELS_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
?>