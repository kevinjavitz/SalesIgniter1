<?php
	$Review = Doctrine_Core::getTable('Reviews')->findOneByReviewsId((int)$_GET['rID']);
	if ($Review){
		$Review->reviews_rating = $_POST['reviews_rating'];
		$Review->ReviewsDescription[Session::get('languages_id')]->reviews_text = $_POST['reviews_text'];
		$Review->save();
	}
	
	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action')), null, 'default'), 'redirect');
?>