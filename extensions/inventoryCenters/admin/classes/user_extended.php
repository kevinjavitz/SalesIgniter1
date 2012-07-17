<?php

class RentalStoreUserExtended extends RentalStoreUser{

	public function __construct($father_class){

		foreach ($father_class as $variable=>$value) {
            $this->$variable = $value;
        }
		//parent::__construct($cID);
	}

	public function setProvider($val){
		$this->customerInfo['is_provider'] = $val;
	}

	public function updateCustomerAccountExt(){
		$Customer = Doctrine::getTable('Customers')->find($this->getCustomerId());
		if($Customer){
            $Customer->is_provider = $this->customerInfo['is_provider'];
            $Customer->save();
        }
	}

	public function isProvider(){ return ($this->customerInfo['is_provider'] == 0 ? false : true); }

}
?>