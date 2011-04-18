<?php
/*
	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$page_error = false;
	$languages = tep_get_languages();
	for($i=0, $n=sizeof($languages); $i<$n; $i++){
		if (empty($_POST['pages_title'][$languages[$i]['id']])){
			$messageStack->addSession('pageStack', sysLanguage::get('ERROR_PAGE_TITLE_REQUIRED'), 'error');
			$page_error = true;
		}
	}

	if ($page_error === false){
		if (isset($_GET['pID'])){
			$pID = (int)$_GET['pID'];
			$Pages = Doctrine_Core::getTable('Pages')->findOneByPagesId((int)$pID);
		}

		if (!isset($Pages) || !$Pages){
			$Pages = new Pages();
		}

		$Pages->sort_order = (int)$_POST['sort_order'];
		$Pages->status = '1';
		$Pages->infobox_status = (int)$_POST['infobox_status'];
		$Pages->page_type = $_POST['page_type'];
		$Pages->page_key = $_POST['page_key'];

		for($i=0, $n=sizeof($languages); $i<$n; $i++){
			$lID = $languages[$i]['id'];

			$Pages->PagesDescription[$lID]->language_id = $lID;
			$Pages->PagesDescription[$lID]->pages_title = $_POST['pages_title'][$lID];
			$Pages->PagesDescription[$lID]->pages_html_text = $_POST['pages_html_text'][$lID];
			$Pages->PagesDescription[$lID]->intorext = $_POST['intorext'][$lID];
			$Pages->PagesDescription[$lID]->externallink = (isset($_POST['externallink'][$lID]) ? $_POST['externallink'][$lID] : null);
			$Pages->PagesDescription[$lID]->link_target = (isset($_POST['link_target'][$lID]) ? $_POST['link_target'][$lID] : null);
		}

		/*
		 * anything additional to handle into $Pages->PagesDescription ?
		 */
		EventManager::notify('InfoPagesDescriptionsBeforeSave', &$Pages->PagesDescription);


		$Pages->save();
		if (isset($_GET['pID'])){
			$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_PAGE_UPDATED'), 'success');
		}else{
			$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_PAGE_INSERTED'), 'success');
		}

		EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'pID')), null, 'default'), 'redirect');
	} else {
		$action = 'new';
	}
?>
