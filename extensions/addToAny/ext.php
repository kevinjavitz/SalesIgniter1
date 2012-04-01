<?php

/**
 * @package Extension_AddToAny
 * @brief Add social buttons using addtoany.com platform
 *
 * @details
 * Social bookmarking
 *
 * @author Erick Romero
 * @version 1
 *
 * I.T. Web Experts, Rental Store v2
 * http://www.itwebexperts.com
 * Copyright (c) 2009 I.T. Web Experts
 * This script and it's source is not redistributable
 */

class Extension_addToAny extends ExtensionBase {

	/**
	 * class constructor
	 * @public
	 * @return void
	 */
	public function __construct() {
		parent::__construct('addToAny');
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Initialize this class. (loaded by core)
	 *
	 * @public
	 * @return void
	 */
	public function init() {
		global $App, $appExtension, $Template;
		if ($this->isEnabled() === FALSE) return;

		/*
		EventManager::attachEvents(array(
			'ShoppingCartListingAddBodyColumn',
			'AccountDefaultMyAccountAddLink',
		), null, $this);
		*/

	}


}

?>
