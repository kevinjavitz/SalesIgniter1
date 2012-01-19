<?php
class StoresModulesConfiguration extends Doctrine_Record
{

	public function setUp() {
		parent::setUp();
		$this->setAttribute(Doctrine::ATTR_COLL_KEY, 'configuration_key');
	}

	public function setTableDefinition() {
		$this->setTableName('stores_modules_configuration');

		$this->hasColumn('config_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => true,
				'autoincrement' => true
			));

		$this->hasColumn('store_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'autoincrement' => false
			));

		$this->hasColumn('module_code', 'string', 128, array(
				'type' => 'string',
				'length' => 128
			));

		$this->hasColumn('module_type', 'string', 128, array(
				'type' => 'string',
				'length' => 128
			));

		$this->hasColumn('configuration_key', 'string', 128, array(
				'type' => 'string',
				'length' => 128
			));

		$this->hasColumn('configuration_value', 'string', 999, array(
				'type' => 'string',
				'length' => 999
			));
	}
}