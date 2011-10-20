<?php
class GiftCertificatesEmailTrack extends Doctrine_Record {

    public function preInsert($event){
        $this->date_sent = date('Y-m-d');
    }

    public function preUpdate($event){
    }

    public function setTableDefinition(){
        $this->setTableName('gift_certificates_email_track');

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

        $this->hasColumn('customer_id_sent', 'integer', 4, array(
                                                                'type' => 'integer',
                                                                'length' => 4,
                                                                'unsigned' => 0,
                                                                'primary' => false,
                                                                'default' => '0',
                                                                'notnull' => true,
                                                                'autoincrement' => false,
                                                           ));

        $this->hasColumn('sent_firstname', 'string', 32, array(
                                                              'type' => 'string',
                                                              'length' => 32,
                                                              'fixed' => false,
                                                              'primary' => false,
                                                              'notnull' => false,
                                                              'autoincrement' => false,
                                                         ));

        $this->hasColumn('sent_lastname', 'string', 32, array(
                                                             'type' => 'string',
                                                             'length' => 32,
                                                             'fixed' => false,
                                                             'primary' => false,
                                                             'notnull' => false,
                                                             'autoincrement' => false,
                                                        ));

        $this->hasColumn('emailed_to', 'string', 32, array(
                                                          'type' => 'string',
                                                          'length' => 32,
                                                          'fixed' => false,
                                                          'primary' => false,
                                                          'notnull' => false,
                                                          'autoincrement' => false,
                                                     ));

        $this->hasColumn('date_sent', 'timestamp', null, array(
                                                              'type' => 'timestamp',
                                                              'primary' => false,
                                                              'default' => '0000-00-00 00:00:00',
                                                              'notnull' => true,
                                                              'autoincrement' => false,
                                                         ));
    }
}
?>