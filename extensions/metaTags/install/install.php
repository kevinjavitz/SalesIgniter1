<?php

/**
 * @brief Handle Meta Tags
 *
 * @details
 * Add Meta tags into html header
 *
 * @author Erick Romero
 * @version 1
 *
 * I.T. Web Experts, Rental Store v2
 * http://www.itwebexperts.com
 * Copyright (c) 2009 I.T. Web Experts
 * This script and it's source is not redistributable
 */

class metaTagsInstall extends extensionInstaller {

	public function __construct(){
		parent::__construct('metaTags');
	}

	public function install(){

		parent::install();
	}

	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_METATAGS_ENABLED') === false) return;

		parent::uninstall($remove);
	}
}

?>
