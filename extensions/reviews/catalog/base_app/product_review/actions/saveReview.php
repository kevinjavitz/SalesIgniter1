<?php
	if (isset($_POST['rating']) && !empty($_POST['rating'])){
		$rating = $_POST['rating'];
		$review = $_POST['review'];

		$review_date = date('Y-m-d');
		$Reviews = Doctrine_Core::getTable('Reviews');

		$Review = new Reviews();
		$Review->date_added = $review_date;
		$Review->products_id = $_POST['products_id'];
		$Review->reviews_rating = $rating;
		$Review->customers_id = (int)$userAccount->getCustomerId();
		$Review->customers_name = $userAccount->getFirstName(). ' '.$userAccount->getLastName();

		$Review->ReviewsDescription[Session::get('languages_id')]->reviews_text = $review;

		$Review->save();

		$messageStack->addSession('pageStack','Review added!','success');
		$link = itw_app_link('appExt=reviews', 'product_review', 'default');
	}else{
		$messageStack->addSession('pageStack','You must select a rating!','error');
		$link = itw_app_link('appExt=reviews&products_id=' . $_POST['products_id'], 'product_review', 'write');
	}

	EventManager::attachActionResponse($link, 'redirect');
?>