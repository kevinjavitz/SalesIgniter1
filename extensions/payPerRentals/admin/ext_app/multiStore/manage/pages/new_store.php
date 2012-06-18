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
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'NewStoreAddTab'
		), null, $this);
	}
	
	public function NewStoreAddTab(&$tabsObj){

		$googleKey = htmlBase::newElement('input')
		->setName('google_key');
		if (isset($_GET['sID'])){
			$QstoreInfo = Doctrine_Query::create()
			->select('google_key,commission,commission_type')
			->from('Stores')
			->where('stores_id = ?', $_GET['sID'])
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			$googleKey->val($QstoreInfo[0]['google_key']);
		}

        $numberOf = htmlBase::newElement('input')
            ->addClass('ui-widget-content')
            ->setName('commission')
            ->attr('size', '8')
            ->val($QstoreInfo[0]['commission']);

        $QPayPerRentalTypes = Doctrine_Query::create()
            ->from('PayPerRentalTypes')
            ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

        $type = htmlBase::newElement('selectbox')
            ->addClass('ui-widget-content')
            ->setName('commission_type')
            ->selectOptionByValue($QstoreInfo[0]['commission_type']);

        foreach($QPayPerRentalTypes as $iType){
            $type->addOption($iType['pay_per_rental_types_id'], $iType['pay_per_rental_types_name']);
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

        $mainTable->addBodyRow(array(
            'columns' => array(
                array('addCls' => 'main', 'text' => 'Commission:'),
                array('addCls' => 'main', 'text' => $numberOf->draw().$type->draw()),
            )
        ));

		
		$tabsObj->addTabHeader('tab_' . $this->getExtensionKey(), array('text' => sysLanguage::get('TAB_PAY_PER_RENTAL')))
		->addTabPage('tab_' . $this->getExtensionKey(), array('text' => $mainTable->draw()));
	}
}
?>