<?php
	$RentalAvailability = Doctrine_Core::getTable('RentalAvailability');
	$RentalAvailabilityDescription = Doctrine_Core::getTable('RentalAvailabilityDescription');
	
	$All = $RentalAvailability->findAll();
	if ($All){
		foreach($All as $aInfo){
			$Description = $RentalAvailabilityDescription->create();
			$Description->name = $aInfo->name;
			$Description->language_id = Session::get('languages_id');
			
			$aInfo->RentalAvailabilityDescription->add($Description);
		}
		$All->save();
	}
	