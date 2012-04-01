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


class metaTags_catalog_specials_show_specials_default extends Extension_metaTags {

	private $title		= '';
	private $desc		= '';
	private $keyword	= '';

	static $already_fetched;

	/**
	 * constructor
	 * @public
	 * @return void
	 */
	public function __construct(){
		parent::__construct();
		self::$already_fetched = 0;
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
	 * auto-detect what type of metatag we need, then fetch and set them
	 *
	 * @private
	 * @return void
	 */
	private function fetchMetatags () {

		if (self::$already_fetched != 0) {
			return;
		}

		$lID = Session::get('languages_id');

		$metaTagsSpecials = $this->fetchPageMetaTags('S', $lID);

		if ($metaTagsSpecials) {
			$this->title	= $metaTagsSpecials[$lID]['t'];
			$this->desc		= $metaTagsSpecials[$lID]['d'];
			$this->keyword	= $metaTagsSpecials[$lID]['k'];
		}

		self::$already_fetched = 1;
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
		$this->fetchMetatags();
		return $this->title;
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
		$this->fetchMetatags();
		return $this->desc;
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
		$this->fetchMetatags();
		return $this->keyword;
	}

}

?>
