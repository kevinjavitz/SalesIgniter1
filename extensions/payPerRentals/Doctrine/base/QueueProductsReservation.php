<?php

class QueueProductsReservation extends Doctrine_Record {

	public function setUp(){
		$this->setUpParent();
		$this->hasOne('Products', array(
				'local' => 'products_id',
				'foreign' => 'products_id'
			));

		$this->hasOne('Customers', array(
				'local' => 'customers_id',
				'foreign' => 'customers_id'
		));

		$this->hasOne('ProductsInventoryBarcodes', array(
			'local'   => 'barcode_id',
			'foreign' => 'barcode_id'
		));

		$this->hasOne('ProductsInventoryQuantity', array(
			'local'   => 'quantity_id',
			'foreign' => 'quantity_id'
		));

	}

	public function setUpParent(){
		$ProductsInventoryBarcodes = Doctrine_Core::getTable('ProductsInventoryBarcodes')->getRecordInstance();
		$Products = Doctrine_Core::getTable('Products')->getRecordInstance();
		$Customers = Doctrine_Core::getTable('Customers')->getRecordInstance();
		$ProductsInventoryQuantity = Doctrine_Core::getTable('ProductsInventoryQuantity')->getRecordInstance();

		$Products->hasMany('QueueProductsReservation', array(
				'local'   => 'products_id',
				'foreign' => 'products_id'
		));

		$Customers->hasMany('QueueProductsReservation', array(
				'local'   => 'customers_id',
				'foreign' => 'customers_id'
		));

		$ProductsInventoryBarcodes->hasMany('QueueProductsReservation', array(
			'local'   => 'barcode_id',
			'foreign' => 'barcode_id'
		));
		
		$ProductsInventoryQuantity->hasMany('QueueProductsReservation', array(
			'local'   => 'quantity_id',
			'foreign' => 'quantity_id'
		));
	}

	public function preInsert($event){
	}

	public function preUpdate($event){
		if ($this->rental_state == 'out'){
			$this->date_shipped = date('Y-m-d H:i:s');
		}elseif ($this->rental_state == 'returned'){
			$this->date_returned = date('Y-m-d H:i:s');
		}
	}

	public function setTableDefinition(){
		$this->setTableName('queue_products_reservation');

		$this->hasColumn('queue_products_reservations_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => true,
			'autoincrement' => true,
		));

		$this->hasColumn('start_date', 'datetime', null, array(
			'type' => 'datetime',
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('event_date', 'datetime', null, array(
			'type' => 'datetime',
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('event_name', 'string', 250, array(
			'type' => 'string',
			'length' => 250,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('event_gate', 'string', 250, array(
				'type' => 'string',
				'length' => 250,
				'fixed' => false,
				'primary' => false,
				'notnull' => true,
				'autoincrement' => false,
		));
		$this->hasColumn('end_date', 'datetime', null, array(
			'type' => 'datetime',
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
				
		$this->hasColumn('insurance', 'decimal', 15, array(
			'type' => 'decimal',
			'scale' => 4,
			'length' => 15,
			'fixed' => true,
			'primary' => false,
			'notnull' => true,
			'default' => '0.0000',
			'autoincrement' => false,
		));
		$this->hasColumn('parent_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		$this->hasColumn('rental_state', 'string', 32, array(
			'type' => 'string',
			'length' => 32,
			'fixed' => false,
			'primary' => false,
			'default' => 'reserved',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('date_shipped', 'date', null, array(
			'type' => 'date',
			'primary' => false,
			'default' => '0000-00-00',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('date_returned', 'date', null, array(
			'type' => 'date',
			'primary' => false,
			'default' => '0000-00-00',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('broken', 'integer', 1, array(
			'type' => 'integer',
			'length' => 1,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('shipping_method', 'string', 64, array(
			'type' => 'string',
			'length' => 64,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('shipping_method_title', 'string', 128, array(
			'type' => 'string',
			'length' => 128,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('shipping_cost', 'decimal', 15, array(
			'type' => 'decimal',
			'length' => 15,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0.0000',
			'notnull' => true,
			'autoincrement' => false,
			'scale' => 4,
		));

		$this->hasColumn('amount_payed', 'decimal', 15, array(
				'type' => 'decimal',
				'length' => 15,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0.0000',
				'notnull' => true,
				'autoincrement' => false,
				'scale' => 4,
		));

		$this->hasColumn('shipping_days_before', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('shipping_days_after', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'default' => '0',
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('quantity_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		$this->hasColumn('barcode_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));
		$this->hasColumn('track_method', 'string', 16, array(
			'type' => 'string',
			'length' => 16,
			'fixed' => false,
			'primary' => false,
			'default' => 'barcode',
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('rental_status_id', 'integer', 4, array(
			'type' => 'integer',
			'length' => 4,
			'unsigned' => 0,
			'primary' => false,
			'notnull' => false,
			'autoincrement' => false,
		));

		$this->hasColumn('semester_name', 'string', 250, array(
			'type' => 'string',
			'length' => 250,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('tracking_number', 'string', 250, array(
			'type' => 'string',
			'length' => 250,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));

		$this->hasColumn('tracking_type', 'string', 30, array(
			'type' => 'string',
			'length' => 30,
			'fixed' => false,
			'primary' => false,
			'notnull' => true,
			'autoincrement' => false,
		));
		$this->hasColumn('products_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));
		$this->hasColumn('products_model', 'string', 32, array(
				'type' => 'string',
				'length' => 32,
				'fixed' => false,
				'primary' => false,
				'notnull' => false,
				'autoincrement' => false,
			));
		$this->hasColumn('products_name', 'string', 64, array(
				'type' => 'string',
				'length' => 64,
				'fixed' => false,
				'primary' => false,
				'default' => '',
				'notnull' => true,
				'autoincrement' => false,
			));
		$this->hasColumn('products_price', 'decimal', 15, array(
				'type' => 'decimal',
				'length' => 15,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0.0000',
				'notnull' => true,
				'autoincrement' => false,
				'scale' => 4,
			));
		$this->hasColumn('final_price', 'decimal', 15, array(
				'type' => 'decimal',
				'length' => 15,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0.0000',
				'notnull' => true,
				'autoincrement' => false,
				'scale' => 4,
			));
		$this->hasColumn('products_tax', 'decimal', 7, array(
				'type' => 'decimal',
				'length' => 7,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0.0000',
				'notnull' => true,
				'autoincrement' => false,
				'scale' => 4,
			));
		$this->hasColumn('products_quantity', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));
		$this->hasColumn('products_date_available', 'timestamp', null, array(
				'type' => 'timestamp',
				'primary' => false,
				'default' => '0000-00-00 00:00:00',
				'notnull' => true,
				'autoincrement' => false,
			));
		$this->hasColumn('purchase_type', 'string', 12, array(
				'type' => 'string',
				'length' => 12,
				'fixed' => false,
				'primary' => false,
				'notnull' => true,
				'autoincrement' => false,
			));
		$this->hasColumn('customers_id', 'integer', 4, array(
				'type' => 'integer',
				'length' => 4,
				'unsigned' => 0,
				'primary' => false,
				'default' => '0',
				'notnull' => true,
				'autoincrement' => false,
			));
		$this->hasColumn('currency', 'string', 3, array(
				'type' => 'string',
				'length' => 3,
				'fixed' => true,
				'primary' => false,
				'notnull' => false,
				'autoincrement' => false,
			));
		$this->hasColumn('currency_value', 'decimal', 14, array(
				'type' => 'decimal',
				'length' => 14,
				'unsigned' => 0,
				'primary' => false,
				'notnull' => false,
				'autoincrement' => false,
				'scale' => 4,
		));
		$this->hasColumn('pinfo', 'string', null, array(
				'type' => 'string',
				'length' => null,
				'primary' => false,
				'notnull' => false,
				'autoincrement' => false,
			));

	}
}
?>