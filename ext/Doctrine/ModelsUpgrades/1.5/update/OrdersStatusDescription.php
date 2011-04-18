<?php
	$OrdersStatus = Doctrine_Core::getTable('OrdersStatus');
	$OrdersStatusDescription = Doctrine_Core::getTable('OrdersStatusDescription');
	
	$All = $OrdersStatus->findAll();
	if ($All){
		foreach($All as $sInfo){
			$Description = $OrdersStatusDescription->create();
			$Description->orders_status_name = $sInfo->orders_status_name;
			$Description->language_id = Session::get('languages_id');
			
			$sInfo->OrdersStatusDescription->add($Description);
		}
		$All->save();
	}
	