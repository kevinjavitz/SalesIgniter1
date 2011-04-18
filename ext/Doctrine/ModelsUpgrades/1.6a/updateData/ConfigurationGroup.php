<?php
	$ConfigurationGroup = Doctrine_Core::getTable('ConfigurationGroup');
	
	$KnownConfigs = array(
		'My Store' => 'coreMyStore',
		'Minimum Values' => 'coreMinValues',
		'Maximum Values' => 'coreMaxValues',
		'Images' => 'coreImages',
		'Customer Details' => 'coreCustomerDetails',
		'Module Options ' => 'coreModuleOptions',
		'Shipping/Packaging' => 'coreShipping',
		'Product Listing' => 'coreProductListing',
		'Logging' => 'coreLogging',
		'Cache' => 'coreCache',
		'E-Mail Options' => 'coreEmail',
		'Download' => 'coreDownload',
		'GZip Compression' => 'coreGzip',
		'Sessions' => 'coreSessions',
		'Rental' => 'rentals',
		'SEO URLs' => 'coreSeoUrls',
		'Error Reporting' => 'coreErrorReporting'
	);
		
	foreach($KnownConfigs as $title => $groupKey){
		Doctrine_Query::create()
		->update('ConfigurationGroup')
		->set('configuration_group_key', '?', $groupKey)
		->where('configuration_group_title = ?', $title)
		->execute();
	}

