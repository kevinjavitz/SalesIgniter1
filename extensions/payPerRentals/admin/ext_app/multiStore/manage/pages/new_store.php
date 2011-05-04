<?php
/*
	Product Designer Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentals_admin_multiStore_manage_new_store extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct();
	}
	
	public function load(){
		if ($this->enabled === false) return;
		
		EventManager::attachEvents(array(
			'NewStoreAddTab'
		), null, $this);
	}
	
	public function NewStoreAddTab(&$tabsObj){

		$googleKey = htmlBase::newElement('input')
		->setName('google_key');
		if (isset($_GET['sID'])){
			$QstoreInfo = Doctrine_Query::create()
			->select('google_key')
			->from('Stores')
			->where('stores_id = ?', $_GET['sID'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$googleKey->val($QstoreInfo[0]['google_key']);
		}
		$mainTable = htmlBase::newElement('table')->setCellPadding(3)->setCellSpacing(0);
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'colspan' => '2', 'text' => '<b><u></u></b>')
			)
		));
		
		$mainTable->addBodyRow(array(
			'columns' => array(
				array('addCls' => 'main', 'text' => 'Google Maps API Key:'),
				array('addCls' => 'main', 'text' => $googleKey->draw()),
			)
		));

		
		$tabsObj->addTabHeader('tab_' . $this->getExtensionKey(), array('text' => sysLanguage::get('TAB_PAY_PER_RENTAL')))
		->addTabPage('tab_' . $this->getExtensionKey(), array('text' => $mainTable->draw()));
	}
}
?>