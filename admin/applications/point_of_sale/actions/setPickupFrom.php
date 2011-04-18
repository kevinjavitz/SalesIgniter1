<?php
	$pointOfSale->setPickupFrom($_GET['address_book_id']);
	EventManager::attachActionResponse(pointOfSaleHTML::getAddressTable('pickup', false, false), 'html');
?>