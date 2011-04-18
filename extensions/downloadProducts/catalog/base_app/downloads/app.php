<?php
/*
	Stream Products Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	
	$appContent = $App->getAppContentFile();
	
	if ($App->getAppPage() == 'get'){
	}
	
	if ($App->getAppPage() == 'listing'){
		$QDownloads = Doctrine_Query::create()
		->from('ProductsDownloads')
		->where('products_id = ?', (int) $_GET['pID'])
		->andWhere('provider_id = ?', 0)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if ($QDownloads && sizeof($QDownloads) == 1){
			tep_redirect(itw_app_link('appExt=downloadProducts&pID=' . (int) $_GET['pID'] . '&dID=' . $QDownloads[0]['download_id'], 'downloads', 'get'));
		}
			
		$QproductsName = Doctrine_Query::create()
		->select('products_name')
		->from('ProductsDescription')
		->where('products_id = ?', (int) $_GET['pID'])
		->andWhere('language_id = ?', Session::get('languages_id'))
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	}
?>