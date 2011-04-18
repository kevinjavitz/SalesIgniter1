<?php
/*
	Inventory Centeres Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class inventoryCenters_admin_customers_edit extends Extension_inventoryCenters {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvent('CustomerInfoAddTableContainer', null, $this);
	}

	public function CustomerInfoAddTableContainer(&$cInfo){
		$checkBox = htmlBase::newElement('checkbox')
		->setName('isProvider')
		->setLabel('Provider')
		->setLabelPosition('after')
		->setValue('1')
		->setChecked(($cInfo['is_provider'] == 1));

		 return '<div class="main" style="margin-top:.5em;font-weight:bold;">Inventory Centers</div><div class="ui-widget ui-widget-content ui-corner-all" style="padding:.5em;">'.$checkBox->draw().'</div>';
	}
}
?>