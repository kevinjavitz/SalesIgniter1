<?php

/**
 *
 * I.T. Web Experts, Rental Store v2
 * http://www.itwebexperts.com
 * Copyright (c) 2009 I.T. Web Experts
 * This script and it's source is not redistributable
 */

class ProductsRelatedGlobal extends Doctrine_Record {

	public function setTableDefinition(){
		$this->setTableName('products_related_global');


		$this->hasColumn('type', 'char', 1, array(
			'type'          => 'char',
			'length'        => 1,			
			'primary'       => true,
			'notnull'       => true,
			'autoincrement' => false,
		));

		

		$this->hasColumn('related_global', 'string', null, array(
			'type'          => 'string',
			'notnull'       => true		
		));


	}
}
