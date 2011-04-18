<?php
	$CustomersInfo = Doctrine_Core::getTable('CustomersInfo');
	
	if ($CustomersInfo->hasColumn('customers_info_source_id') === true){
		$DoctrineExport->alterTable($CustomersInfo->getTableName(), array(
			'remove' => array(
				'customers_info_source_id' => array()
			)
		));
	}

	$CustomersMembership = Doctrine_Core::getTable('CustomersMembership');
	
	if (
		$CustomersMembership->hasColumn('membership_start_streaming') === true || 
		$CustomersMembership->hasColumn('membership_end_streaming') === true
	){
		$remove = array();
		if ($CustomersMembership->hasColumn('membership_start_streaming') === true){
			$remove['membership_start_streaming'] = array();
		}
		
		if ($CustomersMembership->hasColumn('membership_end_streaming') === true){
			$remove['membership_end_streaming'] = array();
		}
		
		$DoctrineExport->alterTable($CustomersInfo->getTableName(), array(
			'remove' => $remove
		));
	}

	$EmailTemplates = Doctrine_Core::getTable('EmailTemplates');
	if (
		$EmailTemplates->hasColumn('email_templates_html') === true || 
		$EmailTemplates->hasColumn('languages_id') === true
	){
		$remove = array();
		if ($EmailTemplates->hasColumn('email_templates_html') === true){
			$remove['email_templates_html'] = array();
		}
		
		if ($EmailTemplates->hasColumn('languages_id') === true){
			$remove['languages_id'] = array();
		}
		
		$DoctrineExport->alterTable($CustomersInfo->getTableName(), array(
			'remove' => $remove
		));
	}
	
	$Membership = Doctrine_Core::getTable('Membership');
	if (
		$Membership->hasColumn('package_name') === true || 
		$Membership->hasColumn('streaming_allowed') === true || 
		$Membership->hasColumn('streaming_no_of_views') === true || 
		$Membership->hasColumn('streaming_views_period') === true || 
		$Membership->hasColumn('streaming_views_time') === true || 
		$Membership->hasColumn('streaming_views_time_period') === true || 
		$Membership->hasColumn('streaming_access_hours') === true
	){
		$remove = array();
		if ($Membership->hasColumn('package_name') === true){
			$remove['package_name'] = array();
		}
		
		if ($Membership->hasColumn('streaming_allowed') === true){
			$remove['streaming_allowed'] = array();
		}
		
		if ($Membership->hasColumn('streaming_no_of_views') === true){
			$remove['streaming_no_of_views'] = array();
		}
		
		if ($Membership->hasColumn('streaming_views_period') === true){
			$remove['streaming_views_period'] = array();
		}
		
		if ($Membership->hasColumn('streaming_views_time') === true){
			$remove['streaming_views_time'] = array();
		}
		
		if ($Membership->hasColumn('streaming_views_time_period') === true){
			$remove['streaming_views_time_period'] = array();
		}
		
		if ($Membership->hasColumn('streaming_access_hours') === true){
			$remove['streaming_access_hours'] = array();
		}
		
		$DoctrineExport->alterTable($Membership->getTableName(), array(
			'remove' => $remove
		));
	}
	
	$OrdersStatus = Doctrine_Core::getTable('OrdersStatus');
	if (
		$OrdersStatus->hasColumn('orders_status_name') === true || 
		$OrdersStatus->hasColumn('language_id') === true
	){
		$remove = array();
		if ($OrdersStatus->hasColumn('orders_status_name') === true){
			$remove['orders_status_name'] = array();
		}
		
		if ($OrdersStatus->hasColumn('language_id') === true){
			$remove['language_id'] = array();
		}
		
		$DoctrineExport->alterTable($OrdersStatus->getTableName(), array(
			'remove' => $remove
		));
	}
	
	$RentalAvailability = Doctrine_Core::getTable('RentalAvailability');
	if ($RentalAvailability->hasColumn('name') === true){
		$remove = array(
			'name' => array()
		);
		
		$DoctrineExport->alterTable($RentalAvailability->getTableName(), array(
			'remove' => $remove
		));
	}

	$TemplatesInfoboxes = Doctrine_Core::getTable('TemplatesInfoboxes');
	if (
		$TemplatesInfoboxes->hasColumn('template_column') === true || 
		$TemplatesInfoboxes->hasColumn('template_name') === true || 
		$TemplatesInfoboxes->hasColumn('template_file') === true || 
		$TemplatesInfoboxes->hasColumn('sort_order') === true
	){
		$remove = array();
		if ($TemplatesInfoboxes->hasColumn('template_column') === true){
			$remove['template_column'] = array();
		}
		
		if ($TemplatesInfoboxes->hasColumn('template_name') === true){
			$remove['template_name'] = array();
		}
		
		if ($TemplatesInfoboxes->hasColumn('template_file') === true){
			$remove['template_file'] = array();
		}
		
		if ($TemplatesInfoboxes->hasColumn('sort_order') === true){
			$remove['sort_order'] = array();
		}
		
		$DoctrineExport->alterTable($TemplatesInfoboxes->getTableName(), array(
			'remove' => $remove
		));
		
		Doctrine_Query::create()->delete('TemplatesInfoboxes')->execute();
		Doctrine_Query::create()->delete('TemplatesInfoboxesDescription')->execute();
	}
