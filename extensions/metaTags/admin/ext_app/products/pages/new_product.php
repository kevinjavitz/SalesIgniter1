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

class metaTags_admin_products_new_product extends Extension_metaTags {

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
			'ProductsFormMiddle',
			'ProductsDescriptionsBeforeSave'
		), null, $this);
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * listen for ProductsFormMiddle event (fired by core)
	 * creates and set the form's elements to add meta tags (title, description and keyword)
	 *
	 * @public
	 * @param	langid	(int)	the language id
	 * @param	content	(array)	variable to store the form's elements
	 * @return string
	 */
	public function ProductsFormMiddle($langid, &$content) {

		$values = false;

		//post found? it means that the info was posted but some validation stopped the process
		//get post values instead of stored ones
		if (isset($_POST['metatags'])) {
			$values = array(
				't' => $_POST['metatags'][$langid]['t'],
				'd' => $_POST['metatags'][$langid]['d'],
				'k' => $_POST['metatags'][$langid]['k']
			);
		}
		else {
			global $Product;
			if (isset($Product->ProductsDescription[$langid])) {
				$values = array(
					't' => $Product->ProductsDescription[$langid]['products_head_title_tag'],
					'd' => $Product->ProductsDescription[$langid]['products_head_desc_tag'],
					'k' => $Product->ProductsDescription[$langid]['products_head_keywords_tag']
				);
			}
		}

		$elements = $this->createFormElements($langid, $values);
		$content[] = array(
			'label' => sysLanguage::get('HEADER_META_TITLE'),
			'content' => $elements['t']->draw()
		);
		$content[] = array(
			'label' => sysLanguage::get('HEADER_META_DESC'),
			'content' => $elements['d']->draw()
		);
		$content[] = array(
			'label' => sysLanguage::get('HEADER_META_KEYWORD'),
			'content' => $elements['k']->draw()
		);
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * listen for ProductsDescriptionsBeforeSave event (fired by core)
	 * add the meta varialbes to description from $_POST
	 *
	 * @public
	 * @param	$descriptions	(array)	variable to store the metatags
	 * @return string
	 */
	public function ProductsDescriptionsBeforeSave($descriptions) {

		if (isset($_POST['metatags'])) {
			foreach ($_POST['metatags'] as $langid => $vals) {
				$descriptions[$langid]->products_head_title_tag		= $vals['t'];
				$descriptions[$langid]->products_head_desc_tag 		= $vals['d'];
				$descriptions[$langid]->products_head_keywords_tag	= $vals['k'];
			}
		}
	}

}

?>
