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

class metaTags_admin_infoPages_manage_newFieldPage extends Extension_metaTags {

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
			'InfoFieldPagesFormMiddle',
			'InfoFieldPagesDescriptionsBeforeSave'
		), null, $this);
	}

	// -------------------------------------------------------------------------------------------

	/**
	 * listen for InfoFieldPagesFormMiddle event (fired by core)
	 * creates and set the form's elements to add meta tags (title, description and keyword)
	 *
	 * @public
	 * @param	langid	(int)	the language id
	 * @param	content	(array)	variable to store the form's elements
	 * @return string
	 */
	public function InfoFieldPagesFormMiddle($langid, &$content) {

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
			global $Qpage;
			if (isset($Qpage[0]['PagesFields'])) {
				$values = array(
					't' => $Qpage[0]['PagesFields']['pages_head_title_tag'],
					'd' => $Qpage[0]['PagesFields']['pages_head_desc_tag'],
					'k' => $Qpage[0]['PagesFields']['pages_head_keywords_tag']
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
	 * listen for InfoFieldPagesDescriptionsBeforeSave event (fired by core)
	 * add the meta varialbes to description from $_POST
	 *
	 * @public
	 * @param	$descriptions	(array)	variable to store the metatags
	 * @return string
	 */
	public function InfoFieldPagesDescriptionsBeforeSave($descriptions) {

		//right now newFieldPage do no uses multi-language

		if (isset($_POST['metatags'])) {
			foreach ($_POST['metatags'] as $langid => $vals) {
				$descriptions->pages_head_title_tag		= $vals['t'];
				$descriptions->pages_head_desc_tag 		= $vals['d'];
				$descriptions->pages_head_keywords_tag	= $vals['k'];
			}
		}

	}

}

?>
