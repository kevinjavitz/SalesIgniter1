<?php
	$TaxClasses = Doctrine_Core::getTable('TaxClass');
	if (isset($_GET['cID'])){
		$TaxClass = $TaxClasses->find((int)$_GET['cID']);
	}else{
		$TaxClass = $TaxClasses->create();
	}
	
	$TaxClass->tax_class_title = $_POST['tax_class_title'];
	$TaxClass->tax_class_description = $_POST['tax_class_description'];
	

	$TaxClass->save();
	
	EventManager::attachActionResponse(array(
		'success' => true,
		'cID'     => $TaxClass->tax_class_id
	), 'json');
?>