<?php
	$OrdersStatus = Doctrine_Core::getTable('OrdersStatus');
	$OrdersStatusDescription = Doctrine_Core::getTable('OrdersStatusDescription');
	
	$All = $DoctrineConnection->fetchAll('select * from ' . $OrdersStatus->getTableName());
	if ($All){
		foreach($All as $sInfo){
			$Description = $OrdersStatusDescription->create();
			$Description->orders_status_id = $sInfo['orders_status_id'];
			$Description->orders_status_name = $sInfo['orders_status_name'];
			$Description->language_id = $sInfo['language_id'];
			$Description->save();
		}
		$DoctrineConnection->exec('delete from ' . $OrdersStatus->getTableName() . ' where language_id != ?', array(
			Session::get('languages_id')
		));
	}
	