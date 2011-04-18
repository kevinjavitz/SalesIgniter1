<?php
	$addressID = $pointOfSale->saveCustomerAddress();
	if (isset($_GET['order_address_key'])){
		$html = $addressBook->formatAddress($_GET['order_address_key'], true);
	}else{
		$html = pointOfSaleHTML::getAddressBlock(array(
			'customerID'    => $userAccount->getCustomerId(),
			'addressBookID' => $addressID
		));
	}
	EventManager::attachActionResponse($html, 'html');
?>