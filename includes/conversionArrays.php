<?php
	$typesArray = array(
		'text'     => 'Text',
		'textarea' => 'Textarea',
		'select'   => 'Drop Down',
		'upload'   => 'Upload',
		'search'   => 'Click to search'
	);

	$typeNames = array(
		'new'           => 'New',
		'used'          => 'Used',
		'rental'        => 'Member Rental',
		'member_stream' => 'Member Stream',
		'download'      => 'Purchase Download',
		'stream'        => 'Purchase Stream',
		'membership'    => 'Membership'
	);

	$inventoryTypes = array(
		'new'         => 'New',
		'used'        => 'Used',
		'rental'      => 'Member Rental'
	);

	$inventoryColumns = array(
		'A' => 'available',
		'B' => 'broken',
		'O' => 'qty_out',
		'R' => 'reserved',
		'P' => 'purchased'
	);

	$barcodeStatusArray = array(
		array('id' => 'A', 'text' => 'Available'),
		array('id' => 'B', 'text' => 'Broken'),
		array('id' => 'O', 'text' => 'Out'),
		array('id' => 'R', 'text' => 'Reserved'),
		array('id' => 'P', 'text' => 'Purchased')
	);

	$barcodeStatuses = array(
		'A' => 'Available',
		'B' => 'Broken',
		'O' => 'Out',
		'R' => 'Reserved',
		'P' => 'Purchased'
	);

	$extraInfoPages = array(
		'page' => 'Stand Alone',
		'popup' => 'Popup',
		'block' => 'Content Block'
	);
	
	$fileTypeUploadDirs = array(
		'image' => array(
			'rel' => sysConfig::getDirWsCatalog() . 'images/',
			'abs' => sysConfig::getDirFsCatalog() . 'images/'
		),
		'file' => array(
			'rel' => sysConfig::getDirWsCatalog() . 'files/',
			'abs' => sysConfig::getDirFsCatalog() . 'files/'
		),
		'templates' => array(
			'rel' => sysConfig::getDirWsCatalog() . 'images/templates/',
			'abs' => sysConfig::getDirFsCatalog() . 'images/templates/'
		),
		'movie' => array(
			'rel' => sysConfig::getDirWsCatalog() . 'movies/',
			'abs' => sysConfig::getDirFsCatalog() . 'movies/'
		)
	);
?>