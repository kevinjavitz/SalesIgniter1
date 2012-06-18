<?php
	if (isset($_GET['suppliers_id'])){
		$Suppliers = Doctrine_Core::getTable('Suppliers')->findOneBySuppliersId($_GET['suppliers_id']);
		if ($Suppliers){
			$Suppliers->delete();
		}
		
		$messageStack->addSession('pageStack', 'Supplier has been removed', 'success');
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'suppliers_id'))), 'redirect');
?>