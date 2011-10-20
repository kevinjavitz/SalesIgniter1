<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class GiftCertificatesToPurchaseTypes extends Doctrine_Record {

    public function setUp(){
        parent::setUp();
        $this->setUpParent();

        $this->hasOne('GiftCertificates', array(
                                     'local' => 'gift_certificates_id',
                                     'foreign' => 'gift_certificates_id'
                                ));

    }

    public function setUpParent(){
        $GiftCertificates = Doctrine::getTable('GiftCertificates')->getRecordInstance();

        $GiftCertificates->hasMany('GiftCertificatesToPurchaseTypes', array(
                                                              'local' => 'gift_certificates_id',
                                                              'foreign' => 'gift_certificates_id'
                                                         ));
    }

    public function setTableDefinition(){
        $this->setTableName('gift_certificates_to_purchase_types');

        $this->hasColumn('gift_certificates_id', 'integer', 11, array(
            'type' => 'integer',
            'length' => 11,
            'unsigned' => 0,
            'primary' => true,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('purchase_type', 'string', 16, array(
            'type' => 'string',
            'length' => 16,
            'primary' => true,
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('gift_certificates_value', 'decimal', 15, array(
            'type' => 'decimal',
            'length' => 15,
            'unsigned' => 0,
            'default' => '0.0000',
            'notnull' => true,
            'autoincrement' => false,
        ));

    }
}