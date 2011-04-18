<?php
	$queueID = (int)$_POST['queue_id'];
	$status = ($_GET['status'] == 'ok' ? 'A' : 'B');
	$comments = tep_db_prepare_input($_POST['comments']);
	if ($appExtension->isEnabled('inventoryCenters') == 'True' && isset($_POST['inventory_center'])){
		$invCenter = (int)$_POST['inventory_center'];
	}

	$Qrented = Doctrine_Query::create()
	->from('RentedQueue')
	->where('customers_queue_id = ?', $queueID)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	if (tep_not_null($comments)){
		$Comment = new ProductsInventoryBarcodesComments();
		$Comment->barcode_id = $Qrented[0]['products_barcode'];
		$Comment->comments = $comments;
		
		$Comment->save();
	}

	$Barcode = Doctrine_Core::getTable('ProductsInventoryBarcodes')->find($Qrented[0]['products_barcode']);
	if ($Barcode){
		$Barcode->status = $status;
		$Barcode->save();
	}

	$RentedProduct = Doctrine_Core::getTable('RentedProducts')->find($queueID);
	if ($RentedProduct){
		$RentedProduct->broken = '0';
		$RentedProduct->return_date = date('Y-m-d');
		if ($_GET['status'] == 'broken'){
			$RentedProduct->broken = '1';
		}
		$RentedProduct->save();
	}

	if (isset($invCenter)){
		$QinvCheck = Doctrine_Query::create()
		->select('inventory_center_id')
		->from('ProductsInventoryBarcodesToInventoryCenters')
		->where('barcode_id = ?', $Qrented[0]['products_barcode'])
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($QinvCheck[0]['inventory_center_id'] != $invCenter){
			Doctrine_Query::create()
			->update('ProductsInventoryBarcodesToInventoryCenters')
			->set('inventory_center_id', '?', $invCenter)
			->where('barcode_id = ?', $Qrented[0]['products_barcode'])
			->execute();
		}
	}

	Doctrine_Query::create()
	->delete('RentedQueue')
	->where('customers_queue_id = ?', $queueID)
	->execute();

	$Qcustomer = Doctrine_Query::create()
	->select('customers_firstname, customers_lastname, customers_email_address, language_id')
	->from('Customers')
	->where('customers_id = ?', $Qrented[0]['customers_id'])
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$emailEvent = new emailEvent('rental_returned', $Qcustomer[0]['language_id']);
	$emailEvent->setVars(array(
		'firstname' => $Qcustomer[0]['customers_firstname'],
		'lastname' => $Qcustomer[0]['customers_lastname'],
		'full_name' => $Qcustomer[0]['customers_firstname'] . ' ' . $Qcustomer[0]['customers_lastname'],
		'rented_product' => tep_get_products_name($Qrented[0]['products_id'], $Qcustomer[0]['language_id'])
	));

	$emailEvent->sendEmail(array(
		'name' => $Qcustomer[0]['customers_firstname'] . ' ' . $Qcustomer[0]['customers_lastname'],
		'email' => $Qcustomer[0]['customers_email_address']
	));

	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>