<?php

class RentalStoreUserExtended extends RentalStoreUser{

	public function __construct($father_class){

		foreach ($father_class as $variable=>$value) {
            $this->$variable = $value;
        }
	}

	public function setAllow($val){
		$this->customerInfo['allow_one'] = $val;
	}

	public function updateCustomerAccountExt(){
		$Customer = Doctrine::getTable('Customers')->find($this->getCustomerId());
        if($Customer){
            $Customer->allow_one = $this->customerInfo['allow_one'];
            $Customer->save();
        }
	}

	public function isProvider(){ return ($this->customerInfo['allow_one'] == 0 ? false : true); }

}
?>