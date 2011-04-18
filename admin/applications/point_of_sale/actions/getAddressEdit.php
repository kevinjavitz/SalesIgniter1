<?php
	EventManager::attachActionResponse(pointOfSaleHTML::getAddressTable($_GET['address_book_id']), 'html');
?>