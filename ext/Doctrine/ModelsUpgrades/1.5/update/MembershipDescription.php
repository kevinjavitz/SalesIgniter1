<?php
	$Membership = Doctrine_Core::getTable('Membership');
	$MembershipDescription = Doctrine_Core::getTable('MembershipPlanDescription');
	
	$All = $Membership->findAll();
	if ($All){
		foreach($All as $mInfo){
			$Description = $MembershipDescription->create();
			$Description->name = $mInfo->package_name;
			$Description->language_id = Session::get('languages_id');
			
			$mInfo->MembershipPlanDescription->add($Description);
		}
		$All->save();
	}
	