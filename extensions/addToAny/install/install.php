<?php

/**
 * @package Extension_AddToAny
 * @brief Add social buttons using addtoany.com platform
 *
 * @details
 * Soccial bookmarking
 *
 * @author Erick Romero
 * @version 1
 *
 * I.T. Web Experts, Rental Store v2
 * http://www.itwebexperts.com
 * Copyright (c) 2009 I.T. Web Experts
 * This script and it's source is not redistributable
 */


class addToAnyInstall extends extensionInstaller {

	public function __construct(){
		parent::__construct('addToAny');
	}

	public function install(){
		if (defined('EXTENSION_ADDTOANY_ENABLED')) return;

		parent::install();
	}

	public function uninstall($remove = false){
		if (!defined('EXTENSION_ADDTOANY_ENABLED')) return;

		parent::uninstall($remove);
	}
}

?>
