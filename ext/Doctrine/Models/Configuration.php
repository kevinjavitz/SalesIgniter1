<?php
/*
 * Sales Igniter E-Commerce System
 * Version: 2.0
 *
 * I.T. Web Experts
 * http://www.itwebexperts.com
 *
 * Copyright (c) 2011 I.T. Web Experts
 *
 * This script and its source are not distributable without the written conscent of I.T. Web Experts
 */

class Configuration extends Doctrine_Record
{

	public function setUp() {
		parent::setUp();
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'configuration_key');
	}

	public function setTableDefinition() {
		$this->setTableName('configuration');

		$this->hasColumn('configuration_id', 'integer', 4, array(
			'type'		  => 'integer',
			'length'		=> 4,
			'unsigned'	  => 0,
			'primary'	   => true,
			'autoincrement' => true
		));

		$this->hasColumn('configuration_key', 'string', 200, array(
			'type'		  => 'string',
			'length'		=> 200,
			'fixed'		 => false,
			'primary'	   => false,
			'default'	   => '',
			'notnull'	   => true,
			'autoincrement' => false
		));

		$this->hasColumn('configuration_value', 'string', 999, array(
			'type'		  => 'string',
			'fixed'		 => false,
			'primary'	   => false,
			'notnull'	   => true,
			'autoincrement' => false
		));

		$this->hasColumn('configuration_group_key', 'string', 128, array(
			'type'		  => 'integer',
			'length'		=> 128,
			'default'	   => '',
			'notnull'	   => true
		));
	}
}