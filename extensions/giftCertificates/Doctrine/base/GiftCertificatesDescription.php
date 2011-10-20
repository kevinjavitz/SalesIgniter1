<?php
/*
	Royalties System Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

    class GiftCertificatesDescription extends Doctrine_Record {

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

            $GiftCertificates->hasMany('GiftCertificatesDescription', array(
                'local' => 'gift_certificates_id',
                'foreign' => 'gift_certificates_id'
            ));
        }

        public function setTableDefinition(){
            $this->setTableName('gift_certificates_description');

            $this->hasColumn('gift_certificates_description_id', 'integer', 11, array(
                 'type' => 'integer',
                 'length' => 11,
                 'unsigned' => 0,
                 'primary' => true,
                 'notnull' => true,
                 'autoincrement' => true,
            ));
            $this->hasColumn('gift_certificates_id', 'integer', 11, array(
                 'type' => 'integer',
                 'length' => 11,
                 'unsigned' => 0,
                 'notnull' => true,
            ));
            $this->hasColumn('language_id', 'integer', 11, array(
                 'type' => 'integer',
                 'length' => 11,
                 'unsigned' => 0,
                 'notnull' => true
            ));
            $this->hasColumn('gift_certificates_name', 'string', 32, array(
                 'type' => 'string',
                 'length' => 32,
                 'notnull' => true
            ));
            $this->hasColumn('gift_certificates_description', 'string', null, array(
                 'type' => 'string',
                 'notnull' => true,
                 'autoincrement' => false,
            ));

        }
    }