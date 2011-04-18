<?php
/*
	Info Pages Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/
	$page_error = false;
	if (empty($_POST['page_key'])){
		$messageStack->addSession('pageStack', 'You must enter a page key', 'error');
		$page_error = true;
	}

	if ($page_error === false){
		$Pages = Doctrine_Core::getTable('Pages');
		if (isset($_GET['pID'])){
			$pID = (int)$_GET['pID'];
			$Page = $Pages->find($pID);
		}else{
			$Page = $Pages->create();
		}

		$Page->sort_order = (int)$_POST['sort_order'];
		$Page->status = '1';
		$Page->page_type = 'field';
		$Page->page_key = $_POST['page_key'];

		$languages = tep_get_languages();
		for($i=0, $n=sizeof($languages); $i<$n; $i++){
			$lID = $languages[$i]['id'];

			$Page->PagesDescription[$lID]->language_id = $lID;
			$Page->PagesDescription[$lID]->pages_title = 'FIELD PAGE';
			$Page->PagesDescription[$lID]->pages_html_text = 'FIELD PAGE';
		}

		$Page->PagesFields->top_content_id = $_POST['top_content_id'];
		$Page->PagesFields->bottom_content_id = $_POST['bottom_content_id'];
		$Page->PagesFields->listing_type = $_POST['listing_type'];
		if ($_POST['listing_type'] == 'attribute'){
			$Page->PagesFields->listing_field_id = null;
			$Page->PagesFields->listing_attribute_id = $_POST['listing_attribute_id'];
		}else{
			$Page->PagesFields->listing_field_id = $_POST['listing_field_id'];
			$Page->PagesFields->listing_attribute_id = null;
		}


		/*
		 * anything additional to handle into $Page->PagesFields-> ?
		 */
		EventManager::notify('InfoFieldPagesDescriptionsBeforeSave', &$Page->PagesFields);


		$Page->save();
		if (isset($_GET['pID'])){
			$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_PAGE_UPDATED'), 'success');
		}else{
			$messageStack->addSession('pageStack', sysLanguage::get('SUCCESS_PAGE_INSERTED'), 'success');
		}

		EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'pID')) . 'pID=' . $Page->pages_id, null, 'default'), 'redirect');
	}
?>
