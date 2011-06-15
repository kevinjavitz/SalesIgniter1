<?php
	$Review = Doctrine_Core::getTable('Reviews')->findOneByReviewsId((int)$_GET['rID']);
	if ($Review){
		$Review->delete();
	}

	EventManager::attachActionResponse(itw_app_link(tep_get_all_get_params(array('action', 'rID')), null, 'default'), 'redirect');
?>