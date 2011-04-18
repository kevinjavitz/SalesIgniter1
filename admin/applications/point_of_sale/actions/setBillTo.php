<?php
	$pointOfSale->setBillTo($_GET['address_book_id']);
	EventManager::attachActionResponse(pointOfSaleHTML::getAddressTable('billing', false, false), 'html');
?>