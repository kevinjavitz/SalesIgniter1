<?php
	$pointOfSale->setSendTo($_GET['address_book_id']);
	EventManager::attachActionResponse(pointOfSaleHTML::getAddressTable('delivery', false, false), 'html');
?>