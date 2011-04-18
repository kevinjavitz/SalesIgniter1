<?php

/**
 * @brief Handle Meta Tags
 *
 * @details
 * Add Meta tags into html header
 *
 * @author Cristian Arcu
 * @version 1
 *
 * I.T. Web Experts, Rental Store v2
 * http://www.itwebexperts.com
 * Copyright (c) 2009 I.T. Web Experts
 * This script and it's source is not redistributable
 */

class metaTags_admin_inventoryCenters_manage_new extends Extension_metaTags {

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
		if ($this->enabled === false) return;

		EventManager::attachEvents(array(
			'InventoryCentersFormMiddle',
			'InventoryCentersBeforeSave'
		), null, $this);
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * listen for InfoPagesFormMiddle event (fired by core)
	 * creates and set the form's elements to add meta tags (title, description and keyword)
	 *
	 * @public
	 * @param	langid	(int)	the language id
	 * @param	content	(array)	variable to store the form's elements
	 * @return string
	 */
	public function InventoryCentersFormMiddle($Inventory, &$content) {

		$values = false;

		//post found? it means that the info was posted but some validation stopped the process
		//get post values instead of stored ones
		if (isset($_POST['metatags'])) {
			$values = array(
				't' => $_POST['metatags']['t'],
				'd' => $_POST['metatags']['d'],
				'k' => $_POST['metatags']['k']
			);
		}
		else {

			$values = array(
				't' => $Inventory['inventorycenter_head_title_tag'],
				'd' => $Inventory['inventorycenter_head_desc_tag'],
				'k' => $Inventory['inventorycenter_head_keywords_tag']
			);

		}

		$elements = $this->createFormElements(0,$values);
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
	 * listen for InfoPagesDescriptionsBeforeSave event (fired by core)
	 * add the meta varialbes to description from $_POST
	 *
	 * @public
	 * @param	$descriptions	(array)	variable to store the metatags
	 * @return string
	 */
	public function InventoryCentersBeforeSave(&$Inventory) {
		//print_r($_POST['metatags']);
		//itwExit();
		if (isset($_POST['metatags'])) {
			$Inventory->inventorycenter_head_title_tag		= $_POST['metatags']['t'];
			$Inventory->inventorycenter_head_desc_tag 		= $_POST['metatags']['d'];
			$Inventory->inventorycenter_head_keywords_tag	= $_POST['metatags']['k'];

		}

	}

}

?>
