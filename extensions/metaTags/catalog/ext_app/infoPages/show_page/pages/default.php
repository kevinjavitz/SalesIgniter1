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

class metaTags_catalog_infoPages_show_page_default extends Extension_metaTags {

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
		global $pageInfo;
		if ($pageInfo['page_type'] == 'field') {
			$tmp = trim($pageInfo['PagesFields']['pages_head_title_tag']);
		}
		else {
			$tmp = trim($pageInfo['PagesDescription'][Session::get('languages_id')]['pages_head_title_tag']);
			if ($tmp == '') $tmp = trim($pageInfo['PagesDescription'][Session::get('languages_id')]['pages_title']);
		}

		return $tmp;
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
		global $pageInfo;
		if ($pageInfo['page_type'] == 'field') {
			$tmp = trim($pageInfo['PagesFields']['pages_head_desc_tag']);
		}
		else {
			$tmp = trim($pageInfo['PagesDescription'][Session::get('languages_id')]['pages_head_desc_tag']);
			if ($tmp == '') $tmp = trim($pageInfo['PagesDescription'][Session::get('languages_id')]['pages_head_desc_tag']);
		}

		return $tmp;
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
		global $pageInfo;
		if ($pageInfo['page_type'] == 'field') {
			$tmp = trim($pageInfo['PagesFields']['pages_head_keywords_tag']);
		}
		else {
			$tmp = trim($pageInfo['PagesDescription'][Session::get('languages_id')]['pages_head_keywords_tag']);
			if ($tmp == '') $tmp = trim($pageInfo['PagesDescription'][Session::get('languages_id')]['pages_head_keywords_tag']);
		}

		return $tmp;
	}

}

?>
