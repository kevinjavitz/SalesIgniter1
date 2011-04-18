<?php
/*
	Pay Per Rentals Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

class payPerRentalsInstall extends extensionInstaller {
	
	public function __construct(){
		parent::__construct('payPerRentals');
	}

	public function install(){
		if (sysConfig::exists('EXTENSION_PAY_PER_RENTALS_ENABLED') === true) return;
		
		parent::install();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'PPR Available';
		$status->rental_status_color = 'c2c2c2';
		$status->rental_status_available = '1';
		$status->save();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'PPR Reserved';
		$status->rental_status_color = '6ccff7';
		$status->rental_status_available = '0';
		$status->save();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'PPR Shipped';
		$status->rental_status_color = 'f7977a';
		$status->rental_status_available = '0';
		$status->save();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'PPR Returned';
		$status->rental_status_color = '0f0';
		$status->rental_status_available = '1';
		$status->save();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'Membership Available';
		$status->rental_status_color = 'ebebeb';
		$status->rental_status_available = '1';
		$status->save();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'Membership Reserved';
		$status->rental_status_color = 'f00';
		$status->rental_status_available = '0';
		$status->save();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'Membership Shipped';
		$status->rental_status_color = 'fff467';
		$status->rental_status_available = '0';
		$status->save();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'Membership Arrived';
		$status->rental_status_color = 'fff799';
		$status->rental_status_available = '0';
		$status->save();
		
		$status = new RentalStatus();
		$status->rental_status_text = 'Membership Returned';
		$status->rental_status_color = '197b30';
		$status->rental_status_available = '1';
		$status->save();

		$pprType = new PayPerRentalTypes();
		$pprType->minutes = '1';
		$pprType->pay_per_rental_types_name = 'Minutes';
		$pprType->save();

		$pprType = new PayPerRentalTypes();
		$pprType->minutes = '60';
		$pprType->pay_per_rental_types_name = 'Hours';
		$pprType->save();

		$pprType = new PayPerRentalTypes();
		$pprType->minutes = '1440';
		$pprType->pay_per_rental_types_name = 'Days';
		$pprType->save();

		$pprType = new PayPerRentalTypes();
		$pprType->minutes = '10080';
		$pprType->pay_per_rental_types_name = 'Weeks';
		$pprType->save();

		$pprType = new PayPerRentalTypes();
		$pprType->minutes = '43200';
		$pprType->pay_per_rental_types_name = 'Months';
		$pprType->save();

		$pprType = new PayPerRentalTypes();
		$pprType->minutes = '525600';
		$pprType->pay_per_rental_types_name = 'Years';
		$pprType->save();
	}
	
	public function uninstall($remove = false){
		if (sysConfig::exists('EXTENSION_PAY_PER_RENTALS_ENABLED') === false) return;
		
		parent::uninstall($remove);
	}
}
