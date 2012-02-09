<?php
	require(sysConfig::getDirFsAdmin() . 'includes/classes/pdf_labels.php');

	$PDF_Labels = new PDF_Labels();
	$PDF_Labels->setStartDate($_GET['start_date']);
	$PDF_Labels->setEndDate($_GET['end_date']);
	$PDF_Labels->setFilter($_GET['filter']);
	if ($appExtension->isEnabled('inventoryCenters') && isset($_GET['invCenter']) && !empty($_GET['invCenter'])){
		$PDF_Labels->setInventoryCenter($_GET['invCenter']);
	}
	$PDF_Labels->runListingQuery();
	$pData = $PDF_Labels->parseListingData('json');

	EventManager::attachActionResponse(array(
		'success'     => true,
		'listingData' => $pData
	), 'json');
?>