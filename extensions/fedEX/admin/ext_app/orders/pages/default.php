<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class fedEX_admin_orders_default extends Extension_fedEX {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		global $appExtension;
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
		    'AdminOrdersAfterTableDraw'
		), null, $this);
	}

    public function AdminOrdersAfterTableDraw(){
         /*$addressCsvButton = htmlBase::newElement('button')
					->setType('submit')
                    ->usePreset('save')
                    ->setText('Export To FEDEX');
        echo $addressCsvButton->draw();*/
    }



	

}
?>