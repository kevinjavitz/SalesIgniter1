<?php
	$CustomersInfo = Doctrine_Core::getTable('CustomersInfo')->getTableName();
	if ($DoctrineImport->tableColumnExists($CustomersInfo, 'customers_info_source_id') === true){
		$DoctrineExport->alterTable($CustomersInfo, array(
			'remove' => array(
				'customers_info_source_id' => array()
			)
		));
	}

	$CustomersMembership = Doctrine_Core::getTable('CustomersMembership')->getTableName();
	if (
		$DoctrineImport->tableColumnExists($CustomersMembership, 'membership_start_streaming') === true ||
		$DoctrineImport->tableColumnExists($CustomersMembership, 'membership_end_streaming') === true
	){
		$remove = array();
		if ($DoctrineImport->tableColumnExists($CustomersMembership, 'membership_start_streaming') === true){
			$remove['membership_start_streaming'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($CustomersMembership, 'membership_end_streaming') === true){
			$remove['membership_end_streaming'] = array();
		}
		
		$DoctrineExport->alterTable($CustomersMembership, array(
			'remove' => $remove
		));
	}

	$EmailTemplates = Doctrine_Core::getTable('EmailTemplates')->getTableName();
	if (
		$DoctrineImport->tableColumnExists($EmailTemplates, 'email_templates_html') === true ||
		$DoctrineImport->tableColumnExists($EmailTemplates, 'languages_id') === true
	){
		$remove = array();
		if ($DoctrineImport->tableColumnExists($EmailTemplates, 'email_templates_html') === true){
			$remove['email_templates_html'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($EmailTemplates, 'languages_id') === true){
			$remove['languages_id'] = array();
		}
		
		$DoctrineExport->alterTable($EmailTemplates, array(
			'remove' => $remove
		));
	}
	
	$Membership = Doctrine_Core::getTable('Membership')->getTableName();
	if (
		$DoctrineImport->tableColumnExists($Membership, 'package_name') === true ||
		$DoctrineImport->tableColumnExists($Membership, 'streaming_allowed') === true ||
		$DoctrineImport->tableColumnExists($Membership, 'streaming_no_of_views') === true ||
		$DoctrineImport->tableColumnExists($Membership, 'streaming_views_period') === true ||
		$DoctrineImport->tableColumnExists($Membership, 'streaming_views_time') === true ||
		$DoctrineImport->tableColumnExists($Membership, 'streaming_views_time_period') === true ||
		$DoctrineImport->tableColumnExists($Membership, 'streaming_access_hours') === true
	){
		$remove = array();
		if ($DoctrineImport->tableColumnExists($Membership, 'package_name') === true){
			$remove['package_name'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($Membership, 'streaming_allowed') === true){
			$remove['streaming_allowed'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($Membership, 'streaming_no_of_views') === true){
			$remove['streaming_no_of_views'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($Membership, 'streaming_views_period') === true){
			$remove['streaming_views_period'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($Membership, 'streaming_views_time') === true){
			$remove['streaming_views_time'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($Membership, 'streaming_views_time_period') === true){
			$remove['streaming_views_time_period'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($Membership, 'streaming_access_hours') === true){
			$remove['streaming_access_hours'] = array();
		}
		
		$DoctrineExport->alterTable($Membership, array(
			'remove' => $remove
		));
	}
	
	$OrdersStatus = Doctrine_Core::getTable('OrdersStatus')->getTableName();
	if (
		$DoctrineImport->tableColumnExists($OrdersStatus, 'orders_status_name') === true ||
		$DoctrineImport->tableColumnExists($OrdersStatus, 'language_id') === true
	){
		$remove = array();
		if ($DoctrineImport->tableColumnExists($OrdersStatus, 'orders_status_name') === true){
			$remove['orders_status_name'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($OrdersStatus, 'language_id') === true){
			$remove['language_id'] = array();
		}
		
		$DoctrineExport->alterTable($OrdersStatus, array(
			'remove' => $remove
		));
	}
	
	$RentalAvailability = Doctrine_Core::getTable('RentalAvailability')->getTableName();
	if ($DoctrineImport->tableColumnExists($RentalAvailability, 'name') === true){
		$remove = array(
			'name' => array()
		);
		
		$DoctrineExport->alterTable($RentalAvailability, array(
			'remove' => $remove
		));
	}

	$TemplatesInfoboxes = Doctrine_Core::getTable('TemplatesInfoboxes')->getTableName();
	if (
		$DoctrineImport->tableColumnExists($TemplatesInfoboxes, 'template_column') === true ||
		$DoctrineImport->tableColumnExists($TemplatesInfoboxes, 'template_name') === true ||
		$DoctrineImport->tableColumnExists($TemplatesInfoboxes, 'template_file') === true ||
		$DoctrineImport->tableColumnExists($TemplatesInfoboxes, 'sort_order') === true
	){
		$remove = array();
		if ($DoctrineImport->tableColumnExists($TemplatesInfoboxes, 'template_column') === true){
			$remove['template_column'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($TemplatesInfoboxes, 'template_name') === true){
			$remove['template_name'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($TemplatesInfoboxes, 'template_file') === true){
			$remove['template_file'] = array();
		}
		
		if ($DoctrineImport->tableColumnExists($TemplatesInfoboxes, 'sort_order') === true){
			$remove['sort_order'] = array();
		}
		
		$DoctrineExport->alterTable($TemplatesInfoboxes, array(
			'remove' => $remove
		));
		
		Doctrine_Query::create()->delete('TemplatesInfoboxes')->execute();
		Doctrine_Query::create()->delete('TemplatesInfoboxesDescription')->execute();
	}
