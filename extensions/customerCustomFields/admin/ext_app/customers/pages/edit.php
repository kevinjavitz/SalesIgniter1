<?php
/*
	Royalties System Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/



class customerCustomFields_admin_customers_edit extends Extension_customerCustomFields {

	public function __construct(){
		parent::__construct();
	}

	public function load(){
		if ($this->isEnabled() === false) return;
		EventManager::attachEvent('CustomerInfoAddTableContainer', null, $this);
	}

	public function CustomerInfoAddTableContainer(&$cInfo){

		$contents = EventManager::notifyWithReturn('CheckoutSetupFields');
		$htmlVal = '';
		if (!empty($contents)){
			foreach($contents as $content){
				$htmlVal .= $content;
			}
		}

	    return $htmlVal;
	}
}


?>