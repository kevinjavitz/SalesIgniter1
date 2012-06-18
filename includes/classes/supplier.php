<?php

class Supplier {

    public function __construct($sID){
        global $appExtension;

        $supplierQuery = Doctrine_Query::create()
            ->from('Suppliers s')
            ->where('s.suppliers_id = ?', (int)$sID);

        $supplierInfo = $supplierQuery->fetchOne();

        if ($supplierInfo){
            $this->id = $supplierInfo['suppliers_id'];
            $this->name = $supplierInfo['suppliers_name'];
            $this->address = $supplierInfo['suppliers_address'];
            $this->phone = $supplierInfo['suppliers_phone'];
            $this->website = $supplierInfo['suppliers_website'];
            $this->notes = $supplierInfo['suppliers_notes'];
            $this->date_added = $supplierInfo['suppliers_date_added'];
            $this->last_modified = $supplierInfo['suppliers_last_modified'];
        }
        $supplierQuery->free();
        $supplierQuery = null;
        unset($supplierQuery);
    }



    /* GET Methods -- Begin --*/
    function getID(){ return (int)$this->id; }
    function getName(){ return $this->name; }
    function getAddress(){ return $this->address; }
    function getPhone(){ return $this->phone; }
    function getWeb(){ return $this->website; }
    function getNotes(){ return $this->notes; }
    function getDateAdded(){ return $this->date_added; }
    function getLastModified(){ return $this->last_modified; }

}
?>