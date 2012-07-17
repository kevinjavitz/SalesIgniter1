<?php
	$aSuppliers = Doctrine_Core::getTable('Suppliers');
	if (isset($_GET['suppliers_id'])){
			$aSupplier = $aSuppliers->findOneBySuppliersId((int)$_GET['suppliers_id']);
		    $aSupplier2 = $aSupplier->copy(true);
		    $aSupplier2->save();

		    $messageStack->addSession('pageStack', 'Supplier has been copied', 'success');
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'suppliers_id'))), 'redirect');
?>