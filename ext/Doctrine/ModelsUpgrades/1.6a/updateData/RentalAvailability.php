<?php
	$RentalAvailability = Doctrine_Core::getTable('RentalAvailability');
	$RentalAvailabilityDescription = Doctrine_Core::getTable('RentalAvailabilityDescription');
	
	$All = $DoctrineConnection->fetchAll('select * from ' . $RentalAvailability->getTableName());
	if ($All){
		foreach($All as $aInfo){
			$Description = $RentalAvailabilityDescription->create();
			$Description->rental_availability_id = $aInfo['rental_availability_id'];
			$Description->name = $aInfo['name'];
			$Description->language_id = Session::get('languages_id');
			$Description->save();
		}
	}
