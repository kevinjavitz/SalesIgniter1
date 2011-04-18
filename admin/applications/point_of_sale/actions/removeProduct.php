<?php
	$pointOfSale->removeProduct($_GET['pID'], $_GET['purchaseType']);
	EventManager::attachActionResponse(pointOfSaleHTML::getProductListing(), 'html');
?>