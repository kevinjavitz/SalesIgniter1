<?php

class payPerRentals_admin_membership_packages extends Extension_payPerRentals {

	public function __construct(){
		parent::__construct('payPerRentals');
	}
	
	public function load(){
		if ($this->isEnabled() === false) return;
		
		EventManager::attachEvents(array(
			'MembershipPackageEditWindowBeforeDraw'
		), null, $this);
	}
	
	public function MembershipPackageEditWindowBeforeDraw(&$infoBox, &$Package){
		if(sysConfig::get('EXTENSION_PAY_PER_RENTAL_ALLOW_MEMBERSHIP') == 'True'){
			$noRentals = htmlBase::newElement('input')
			->setLabel('Number of Reservations')
			->setName('ppr_rentals')
			->setValue($Package->ppr_rentals);

			$infoBox->addContentRow('<hr><br>Rentals Based');
			$infoBox->addContentRow($noRentals->draw());
		}

	}
}
