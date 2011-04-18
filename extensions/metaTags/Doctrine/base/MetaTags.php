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

class MetaTags extends Doctrine_Record {

	public function setTableDefinition(){
		$this->setTableName('meta_tags');

		//the autoincrement ID',
		$this->hasColumn('metatags_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => true,
		));

		//The title tag',
		$this->hasColumn('title', 'string', null, array(
			'type'          => 'string',
			'notnull'       => true,
			'collation'		=> 'utf8_general_ci',
		));

		//The meta-tag description',
		$this->hasColumn('description', 'string', null, array(
			'type'          => 'string',
			'notnull'       => true,
			'collation'		=> 'utf8_general_ci',
		));

		//The meta-tag keywords',
		$this->hasColumn('keywords', 'string', null, array(
			'type'          => 'string',
			'notnull'       => true,
			'collation'		=> 'utf8_general_ci',
		));

		//The language ID for this meta-tags',
		$this->hasColumn('language_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'notnull'       => true,
		));

		/**
		 * the type of page that this metatags refers to.
		 * the intented values are: 'D', 'S', 'B'
		 * - Default for whole site (home page)
		 * - Specials Page
		 * - Best Seller
		 */
		$this->hasColumn('type_page', 'char', 1, array(
			'type'      => 'char',
			'length'	=> 1,
			'notnull'   => true,
			'default' 	=> 'D',
		));


		//The numeric id of the page that this metatags refers to (if any)
		$this->hasColumn('type_page_id', 'integer', 4, array(
			'type'          => 'integer',
			'length'        => 4,
			'unsigned'      => 0,
			'notnull'       => true,
			'default' 		=> 0,
		));
	}
}
