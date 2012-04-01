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


class InfoBoxAddRoAnyButtons extends InfoBoxAbstract {

	public function __construct(){

		$this->init('socialButtons', __DIR__);
		$this->enabled = true;

		$this->setBoxHeading(sysLanguage::get('INFOBOX_HEADING_SOCIAL_BUTTONS'));
	}

	/**
	 * draw the Javascript code to inject the social buttons
	 *
	 * @public
	 * @return void
	 */

	public function show(){
		if ($this->enabled === false) return;

		$this->setBoxContent(
			'<!-- AddToAny BEGIN -->
			<div class="a2a_kit a2a_default_style">
			<a class="a2a_dd" href="https://www.addtoany.com/share_save">Share</a>
			<span class="a2a_divider"></span>
			<a class="a2a_button_facebook"></a>
			<a class="a2a_button_twitter"></a>
			<a class="a2a_button_email"></a>
			</div>
			<script type="text/javascript" src="https://static.addtoany.com/menu/page.js"></script>
			<!-- AddToAny END -->'
		);

		return $this->draw();
	}
}
?>