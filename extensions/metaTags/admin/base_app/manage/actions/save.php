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


	//are the default params available?
	if (isset($_POST['metatags']) && is_array($_POST['metatags']) ){
		metatags_store($_POST['metatags'], 'D');
	}

	//are Specials params available?
	if (isset($_POST['metatags_special']) && is_array($_POST['metatags_special']) ){
		metatags_store($_POST['metatags_special'], 'S');
	}

	//are best seller params available?
	if (isset($_POST['metatags_best']) && is_array($_POST['metatags_best']) ){
		metatags_store($_POST['metatags_best'], 'B');
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'), 'redirect');


	/**
	 * store the metatags into DB
	 *
	 * @public
	 * @param	$langs		(array)	POST metatags array
	 * @param	type_page	(char)
	 * @see 	for values expected on "type_page" param, see MetaTags model, type_page column definition
	 *
	 * @return void
	 */
	function metatags_store($langs, $type_page) {

		foreach ($langs as $langid => $val) {

			$metaid = isset($val['i']) ? intval($val['i']) : 0;

			$Metatags = Doctrine::getTable('MetaTags');
			$Metatags = $Metatags->find($metaid);

			if ( ! $Metatags) {
				$Metatags = Doctrine::getTable('MetaTags');
				$Metatags = $Metatags->create();
				$Metatags->language_id	= $langid;
			}

			$Metatags->title 		= trim($val['t']);
			$Metatags->description 	= trim($val['d']);
			$Metatags->keywords 	= trim($val['k']);
			$Metatags->type_page	= strtoupper($type_page);
			$Metatags->type_page_id	= 0;
			EventManager::notify('MetaTagsAdminSaveQueryBeforeExecute', &$Metatags);
			$Metatags->save();

		}
	}

?>
