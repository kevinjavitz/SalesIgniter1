<?php
//echo 'POST::<pre>';print_r($_POST);echo '</pre>';
//echo 'FILES::<pre>';print_r($_FILES);echo '</pre>';
//echo '{ success:true }';
//itwExit();

	
	$Suppliers = Doctrine_Core::getTable('Suppliers');
	if (isset($_GET['sID'])){
		$Supplier = $Suppliers->findOneBySuppliersId((int)$_GET['sID']);
	}elseif (isset($_POST['supplier_id'])){
		$Supplier = $Suppliers->findOneBySuppliersId((int)$_POST['supplier_id']);
	}else{
		$Supplier = $Suppliers->create();
	}

	$Supplier->suppliers_name = $_POST['suppliers_name'];
    $Supplier->suppliers_address = $_POST['suppliers_address'];
    $Supplier->suppliers_phone = $_POST['suppliers_phone'];
    $Supplier->suppliers_website = $_POST['suppliers_website'];
    $Supplier->suppliers_notes = $_POST['suppliers_notes'];

	//echo '<pre>';print_r($_POST);print_r($Supplier->toArray(true));exit;
	$Supplier->save();


	if (isset($_GET['rType']) && $_GET['rType'] == 'ajax'){
		EventManager::attachActionResponse(array(
			'success' => true,
			'sID'     => $Supplier->suppliers_id
		), 'json');
	}else{
		$link = itw_app_link(tep_get_all_get_params(array('action', 'sID')) . 'sID=' . $Supplier->suppliers_id, null, 'default');

		EventManager::attachActionResponse($link, 'redirect');
	}
?>
