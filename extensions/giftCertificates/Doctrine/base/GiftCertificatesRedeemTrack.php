<?php
class GiftCertificatesRedeemTrack extends Doctrine_Record {

    public function preInsert($event){
        $this->redeem_date = date('Y-m-d H:i:s');
        $this->redeem_ip = $_SERVER['REMOTE_ADDR'];
    }

    public function setUp(){
        $this->hasOne('Customers', array(
                                        'local' => 'customers_id',
                                        'foreign' => 'customers_id'
                                   ));
    }

    public function setTableDefinition(){
        $this->setTableName('gift_certificates_redeem_track');

        $this->hasColumn('unique_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => true,
             'autoincrement' => true,
        ));
        $this->hasColumn('gift_certificates_id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'unsigned' => 0,
             'primary' => false,
             'default' => '0',
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
        $this->hasColumn('redeem_date', 'timestamp', null, array(
            'type' => 'timestamp',
            'primary' => false,
            'default' => '0000-00-00 00:00:00',
            'notnull' => true,
            'autoincrement' => false,
        ));
        $this->hasColumn('redeem_ip', 'string', 32, array(
            'type' => 'string',
            'length' => 32,
            'fixed' => false,
            'primary' => false,
            'default' => '',
            'notnull' => true,
            'autoincrement' => false,
        ));
    }
}