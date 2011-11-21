<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class GiftCertificates extends Doctrine_Record {

    public function preInsert($event){
        $this->date_created = date('Y-m-d H:i:s');
    }

    public function preUpdate($event){
        $this->date_modified = date('Y-m-d H:i:s');
    }

    public function setUp(){
        parent::setUp();
        $this->setUpParent();

        $this->hasOne('GiftCertificatesDescription', array(
            'local' => 'gift_certificates_id',
            'foreign' => 'gift_certificates_id',
            'cascade' => array('delete')
        ));
        $this->hasOne('GiftCertificatesToPurchaseTypes', array(
            'local' => 'gift_certificates_id',
            'foreign' => 'gift_certificates_id',
            'cascade' => array('delete')
        ));
        $this->hasOne('TaxRates', array(
            'local' => 'gift_certificates_tax_class_id',
            'foreign' => 'tax_rates_id'
        ));
    }

    public function setUpParent(){

    }

    public function setTableDefinition(){
        $this->setTableName('gift_certificates');
        $this->hasColumn('gift_certificates_id', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'unsigned' => 0,
            'primary' => true,
            'notnull' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('gift_certificates_price', 'decimal', 15, array(
            'type' => 'decimal',
            'scale' => 4,
            'length' => 15,
            'unsigned' => 0,
            'default' => '0.0000',
            'notnull' => true
        ));
        $this->hasColumn('gift_certificates_tax_class_id', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'unsigned' => 0,
            'notnull' => true
        ));
        $this->hasColumn('gift_certificates_status', 'string', 1, array(
            'type' => 'string',
            'length' => 1,
            'default' => 'Y',
            'notnull' => true
        ));
        $this->hasColumn('gift_certificates_redeemed', 'string', 1, array(
            'type' => 'string',
            'length' => 1,
            'default' => 'N',
            'notnull' => true
        ));
        $this->hasColumn('gift_certificates_code', 'string', 10, array(
            'type' => 'string',
            'length' => 10,
            'notnull' => true
        ));
        $this->hasColumn('date_created', 'timestamp', null, array(
            'type' => 'timestamp',
            'primary' => false,
            'default' => '0000-00-00 00:00:00',
            'notnull' => true,
            'autoincrement' => false,
        ));

        $this->hasColumn('date_modified', 'timestamp', null, array(
            'type' => 'timestamp',
            'primary' => false,
            'default' => '0000-00-00 00:00:00',
            'notnull' => true,
            'autoincrement' => false,
        ));
    }
}