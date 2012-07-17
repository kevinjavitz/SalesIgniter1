<?php
if(isset($_POST['selectedSuppliers'])){
		foreach($_POST['selectedSuppliers'] as $supplierId){
			$Suppliers = Doctrine_Core::getTable('Suppliers')->findOneBySuppliersId($supplierId);
			if ($Suppliers){
				$Suppliers->delete();
			}
		}
		$messageStack->addSession('pageStack', 'Suppliers has been removed', 'success');
	}

$json = array(
	'success' => true
);
EventManager::attachActionResponse($json, 'json');
?>