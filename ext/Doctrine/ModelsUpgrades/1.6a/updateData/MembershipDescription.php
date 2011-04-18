<?php
	$Membership = Doctrine_Core::getTable('Membership');
	$MembershipDescription = Doctrine_Core::getTable('MembershipPlanDescription');
	
	$All = $DoctrineConnection->fetchAll('select * from ' . $Membership->getTableName());
	if ($All){
		foreach($All as $mInfo){
			$Description = $MembershipDescription->create();
			$Description->plan_id = $mInfo['plan_id'];
			$Description->name = $mInfo['package_name'];
			$Description->language_id = Session::get('languages_id');
			$Description->save();
		}
	}
	