<?php
	if (!isset($_POST['fromOrder'])){
		$pID = (int)$_POST['products_id'];
		if (isset($_POST['start_date']) && $_POST['purchase_type'] == 'reservation'){
			$simPost = array(
				'products_id'     => $pID,
				'purchase_type'   => 'reservation',
				'rental_qty'      => (int)$_POST['qty'],
				'rental_shipping' => $_POST['rental_shipping'],
				'start_date'      => $_POST['start_date'],
				'end_date'        => $_POST['end_date'],
				'barcode'         => (isset($_POST['products_barcode']) ? $_POST['products_barcode'] : false)
			);
		}else{
			$simPost = array(
				'product_id'    => $pID,
				'quantity'      => (int)$_POST['qty'],
				'purchase_type' => $_POST['purchase_type'],
				'barcode'       => (isset($_POST['products_barcode']) ? $_POST['products_barcode'] : false)
			);
		}
		$pointOfSale->addProduct($simPost);
	}

	EventManager::attachActionResponse(pointOfSaleHTML::getProductListing(), 'html');
?>