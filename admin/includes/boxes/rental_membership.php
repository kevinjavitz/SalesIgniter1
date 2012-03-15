<?php
/*
	Sales Igniter E-Commerce System
	Version: 1.0
	
	I.T. Web Experts
	http://www.itwebexperts.com
	
	Copyright (c) 2010 I.T. Web Experts
	
	This script and its source are not distributable without the written conscent of I.T. Web Experts
*/

	$contents = array(
		'text' => sysLanguage::get('BOX_HEADING_RENTAL_MEMBERSHIP'),
		'link' => false,
		'children' => array()
	);
	
	if (sysPermissions::adminAccessAllowed('membership', 'packages') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'membership', 'packages', 'SSL'),
			'text' => 'Edit Packages'
		);
	}
	
	if (sysPermissions::adminAccessAllowed('rental_queue') === true){
		if (sysPermissions::adminAccessAllowed('rental_queue', 'availability') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'rental_queue', 'availability', 'SSL'),
				'text' => sysLanguage::get('BOX_RENTAL_MEMBERSHIP_AVAILABILITY')
			);
		}
		
		if (sysPermissions::adminAccessAllowed('rental_queue', 'default') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'rental_queue', 'default', 'SSL'),
				'text' => sysLanguage::get('BOX_RENTAL_MEMBERSHIP_RENTAL_QUEUE')
			);
		}
		
		if (sysPermissions::adminAccessAllowed('rental_queue', 'issues') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'rental_queue', 'issues', 'SSL'),
				'text' => sysLanguage::get('BOX_RENTAL_MEMBERSHIP_RENTAL_ISSUES')
			);
		}

		if (sysPermissions::adminAccessAllowed('rental_queue', 'pastdue') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'rental_queue', 'pastdue', 'SSL'),
				'text' => sysLanguage::get('BOX_RENTAL_MEMBERSHIP_RENTAL_PASTDUE')
			);
		}

		if (sysPermissions::adminAccessAllowed('rental_queue', 'pickup_requests') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'rental_queue', 'pickup_requests', 'SSL'),
				'text' => sysLanguage::get('BOX_RENTAL_MEMBERSHIP_RENTAL_PICKUP_REQUESTS')
			);
		}

		if (sysPermissions::adminAccessAllowed('rental_queue', 'pickup_requests_types') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'rental_queue', 'pickup_requests_types', 'SSL'),
				'text' => sysLanguage::get('BOX_RENTAL_MEMBERSHIP_RENTAL_PICKUP_REQUESTS_TYPES')
			);
		}

		if (sysPermissions::adminAccessAllowed('rental_queue', 'pickup_requests_report') === true){
			$contents['children'][] = array(
				'link' => itw_app_link(null, 'rental_queue', 'pickup_requests_report', 'SSL'),
				'text' => sysLanguage::get('BOX_RENTAL_MEMBERSHIP_RENTAL_PICKUP_REQUESTS_REPORT')
			);
		}
		
		if (
			sysPermissions::adminAccessAllowed('rental_queue', 'rented') === true || 
			sysPermissions::adminAccessAllowed('rental_queue', 'return_barcode') === true || 
			sysPermissions::adminAccessAllowed('rental_queue', 'return') === true
		){
			$subChildren = array();
			if (sysPermissions::adminAccessAllowed('rental_queue', 'rented') === true){
				$subChildren[] = array(
					'link' => itw_app_link(null, 'rental_queue', 'rented', 'SSL'),
					'text' => 'By Customer'
				);
			}
			
			if (sysPermissions::adminAccessAllowed('rental_queue', 'return_barcode') === true){
				$subChildren[] = array(
					'link' => itw_app_link(null, 'rental_queue', 'return_barcode', 'SSL'),
					'text' => 'By Barcode'
				);
			}
			
			if (sysPermissions::adminAccessAllowed('rental_queue', 'return') === true){
				$subChildren[] = array(
					'link' => itw_app_link(null, 'rental_queue', 'return', 'SSL'),
					'text' => 'By List Of All Checked Out'
				);
			}
			
			$contents['children'][] = array(
				'link' => false,
				'text' => 'Return Rentals',
				'children' => $subChildren
			);
		}
	}
	
	if (sysPermissions::adminAccessAllowed('rental_history', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'rental_history', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_RENTAL_HISTORY')
		);
	}
	
	if (sysPermissions::adminAccessAllowed('label_maker', 'default') === true){
		$contents['children'][] = array(
			'link' => itw_app_link(null, 'label_maker', 'default', 'SSL'),
			'text' => sysLanguage::get('BOX_RENTAL_GENERAL_BATCH_PRINT')
		);
	}

	EventManager::notify('BoxMembershipAddLink', &$contents);
if(count($contents['children']) == 0){
	$contents = array();
}
?>