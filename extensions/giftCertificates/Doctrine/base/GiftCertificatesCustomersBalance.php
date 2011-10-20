<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

    class GiftCertificatesCustomersBalance extends Doctrine_Record {

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

            $Customers->hasMany('GiftCertificatesCustomersBalance', array(
                'local' => 'customers_id',
                'foreign' => 'customers_id'
            ));
        }

        public function setTableDefinition(){
            $this->setTableName('gift_certificates_customers_balance');

            $this->hasColumn('gift_certificates_customers_balance_id', 'integer', 11, array(
                'type' => 'integer',
                'length' => 11,
                'unsigned' => 0,
                'primary' => true,
                'notnull' => true,
                'autoincrement' => true,
            ));
            $this->hasColumn('customers_id', 'integer', 11, array(
                'type' => 'integer',
                'length' => 11,
                'unsigned' => 0,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('purchase_type', 'string', 16, array(
                'type' => 'string',
                'length' => 16,
                'primary' => false,
                'notnull' => true,
                'autoincrement' => false,
            ));
            $this->hasColumn('value', 'decimal', 15, array(
                'type' => 'decimal',
                'length' => 15,
                'default' => '0.0000',
                'notnull' => true,
                'autoincrement' => false,
            ));

        }
    }