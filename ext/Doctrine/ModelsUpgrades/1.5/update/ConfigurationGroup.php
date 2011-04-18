<?php
	$ConfigurationGroup = Doctrine_Core::getTable('ConfigurationGroup');
	
	$addColumns = array();
	if ($ConfigurationGroup->hasColumn('configuration_group_key') === false){
		$addColumns['config_home'] = array(
			'type' => 'string',
			'notnull' => true,
			'length' => 128
		);
	}
	
	if (!empty($addColumns)){
		$DoctrineExport->alterTable($ConfigurationGroup->getTableName(), array(
			'add' => $addColumns
		));
		
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
			->where('configuration_title = ?', $title)
			->execute();
		}
	}
	
