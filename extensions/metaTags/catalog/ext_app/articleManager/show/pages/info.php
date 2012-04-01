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

class metaTags_catalog_articleManager_show_info extends Extension_metaTags {

	/**
	 * constructor
	 * @public
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * Loaded by core (extensions)
	 * Define the events to listen to
	 * @public
	 * @return void
	 */
	public function load(){
		if ($this->isEnabled() === false) return;

		EventManager::attachEvents(array(
			'HeaderTagsTitle',
			'HeaderTagsMetaDescription',
			'HeaderTagsMetaKeywords',
		), null, $this);
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * listen for HeaderTagsTitle event (fired by core)
	 * Return the title header
	 *
	 * @public
	 * @return string
	 */
	public function HeaderTagsTitle() {
		global $Article;
		$tmp = trim($Article->data['ArticlesDescription'][0]['articles_head_title_tag']);

		if ($tmp != '') {
			return $tmp;
		}
		else {
			return trim($Article->data['ArticlesDescription'][0]['articles_name']);
		}
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * listen for HeaderTagsMetaDescription event (fired by core)
	 * Return the description meta tag
	 *
	 * @public
	 * @return string
	 */
	public function HeaderTagsMetaDescription() {
		global $Article;
		$tmp = trim($Article->data['ArticlesDescription'][0]['articles_head_desc_tag']);

		if ($tmp != '') {
			return $tmp;
		}
		else {
			return trim($Article->data['ArticlesDescription'][0]['articles_description']);
		}
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * listen for HeaderTagsMetaKeywords event (fired by core)
	 * Return the keywords meta tag
	 *
	 * @public
	 * @return string
	 */
	public function HeaderTagsMetaKeywords() {
		global $Article;
		$tmp = trim($Article->data['ArticlesDescription'][0]['articles_head_keywords_tag']);

		if ($tmp != '') {
			return $tmp;
		}
		else {
			return trim($Article->data['ArticlesDescription'][0]['articles_name']);
		}
	}

}

?>
