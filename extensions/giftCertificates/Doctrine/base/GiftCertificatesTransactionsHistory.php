<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

    class GiftCertificatesTransactionsHistory extends Doctrine_Record {

        public function preInsert($event){
            $this->date_added = date('Y-m-d H:i:s');
        }

        public function setUp(){
            parent::setUp();
            $this->setUpParent();

            $this->hasOne('Customers', array(
                'local' => 'customers_id',
                'foreign' => 'customers_id',
                'cascade' => array('delete')
            ));
        }

        public function setUpParent(){
            $Customers = Doctrine::getTable('Customers')->getRecordInstance();

            $Customers->hasMany('GiftCertificatesTransactionsHistory', array(
                'local' => 'customers_id',
                'foreign' => 'customers_id'
            ));
        }

        public function setTableDefinition(){
            $this->setTableName('gift_certificates_transaction_history');

            $this->hasColumn('gift_certificates_transaction_history_id', 'integer', 4, array(
                'type' => 'integer',
                'length' => 4,
                'unsigned' => 0,
                'primary' => true,
                'notnull' => true,
                'autoincrement' => true,
            ));
            $this->hasColumn('customers_id', 'integer', 4, array(
                'type' => 'integer',
                'length' => 4,
                'unsigned' => 0,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('transaction_type', 'string', 1, array(
                'type' => 'string',
                'length' => 1,
                'default' => '+',
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('gift_certificates_id', 'integer', 4, array(
                'type' => 'integer',
                'length' => 4,
                'unsigned' => 0,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('valutec_card_number', 'string', 32, array(
                'type' => 'integer',
                'length' => 32,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('orders_id', 'integer', 4, array(
                'type' => 'integer',
                'length' => 4,
                'unsigned' => 0,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('date_added', 'timestamp', null, array(
                'type' => 'timestamp',
                'default' =>'0000-00-00 00:00:00',
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('purchase_type_values', 'string', 64, array(
                'type' => 'string',
                'length' => 64,
                'default' => '',
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('amount', 'decimal', 15, array(
                'type' => 'decimal',
                'length' => 15,
                'default' => '0.0000',
                'notnull' => true,
                'autoincrement' => false,
            ));
        }
    }

 
