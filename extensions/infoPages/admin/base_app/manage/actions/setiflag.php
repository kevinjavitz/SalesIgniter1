<?php
/*
	Info Pages Extension Version 1
	
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	if ($_GET['flag'] == '0' || $_GET['flag'] == '1'){
		Doctrine_Query::create()
		->update('Pages')
		->set('infobox_status', '?', ((int) $_GET['flag'] == '0' ? '0' : '1'))
		->where('pages_id = ?', (int) $_GET['pID'])
		->execute();

		$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_PAGE_INFOBOX_STATUS_UPDATED'), 'success');
	}else{
		$messageStack->addSession('pageStack', sysLanguage::get('ERROR_UNKNOWN_STATUS_FLAG'), 'error');
	}
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'flag'))), 'redirect');
?>