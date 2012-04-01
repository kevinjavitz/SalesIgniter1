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


class metaTags_catalog_index_default extends Extension_metaTags {

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

		global $current_category_id;
		if ($current_category_id != 0) {
			//we are dealing with categories

			$QCategories = Doctrine_Query::create()
			->from('CategoriesDescription c')
			->where('c.categories_id = ?', intval($current_category_id))
			->andWhere('c.language_id = ?', intval(Session::get('languages_id')))
			->fetchOne(null, Doctrine_Core::HYDRATE_ARRAY);

			if ($QCategories) {
				$this->title	= $QCategories['categories_htc_title_tag'];
				$this->desc		= $QCategories['categories_htc_desc_tag'];
				$this->keyword	= $QCategories['categories_htc_keywords_tag'];

				if (trim($this->title) == '') 	$this->title 	= $QCategories['categories_name'];
				if (trim($this->desc) == '') 	$this->desc 		= $QCategories['categories_description'];
				if (trim($this->keyword) == '') $this->keyword 	= $QCategories['categories_name'];
			}
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
