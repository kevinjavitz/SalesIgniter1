<?php
/**
 * CustomerWishlistSettings
 */
class CustomerWishlistSettings extends Doctrine_Record {
	
	public function setUp(){
		parent::setUp();
		//$this->setUpParent();

		/*$this->hasOne('Customers', array(
			'local' => 'customers_id',
			'foreign' => 'customers_id'
		));*/
	}
	
	public function setUpParent(){
		$Customers = Doctrine::getTable('Customers')->getRecordInstance();

		/*$Customers->hasOne('CustomerWishlist', array(
			'local' => 'customers_id',
			'foreign' => 'customers_id',
			'cascade' => array('delete')
		));*/
	}
	
	public function setTableDefinition(){
		$this->setTableName('customer_wishlist_settings');
		
		$this->hasColumn('wishlist_id', 'integer', 11, array(
			'type' => 'integer',
			'length' => 11,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));
		
		$this->hasColumn('customers_id', 'integer', 11, array(
			'type' => 'integer',
			'length' => 11,
			'unsigned' => 0,
			'primary' => false,
			'autoincrement' => false,
		));
		
		$this->hasColumn('wishlist_public', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false
		));

		$this->hasColumn('wishlist_search', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false
		));

	}
}