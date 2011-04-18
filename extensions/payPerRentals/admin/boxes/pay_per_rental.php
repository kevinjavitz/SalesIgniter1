<?php
/*
	Pay Per Rentals Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

	$heading = sysLanguage::get('BOX_HEADING_RENTAL_ONETIME');

	$contents = array(
		array(
			'link' => tep_admin_file_access_allowed(FILENAME_ONETIME_RENTAL_SEND),
			'text' => 'Send Rentals'
		),
		array(
			'link' => '',
			'text' => 'Return Rentals',
			'children' => array(
				array(
					'link' => tep_admin_file_access_allowed(FILENAME_RETURN_ONETIME_RENTALS),
					'text' => 'By Date'
				),
				array(
					'link' => tep_admin_file_access_allowed(FILENAME_RETURN_RENTALS_BAR_CODE),
					'text' => 'By Barcode'
				)
			)
		),
		array(
			'link' => 'application.php',
			'linkParams' => 'app=label_maker&appPage=default',
			'text' => sysLanguage::get('BOX_RENTAL_GENERAL_BATCH_PRINT')
		)
	);

	if (Event::exists('boxPayPerRental_addLink')){
		Event::run('boxPayPerRental_addLink', &$contents);
	}
?>