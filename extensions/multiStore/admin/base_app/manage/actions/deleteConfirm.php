<?php
/*
	Multi Stores Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	Doctrine_Query::create()
	->delete('Stores')
	->where('stores_id = ?', (int)$_POST['store_id'])
	->execute();
	
	EventManager::attachActionResponse(array(
		'success' => true
	), 'json');
?>