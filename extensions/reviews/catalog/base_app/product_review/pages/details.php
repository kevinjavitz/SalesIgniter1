<?php
	$rowTemplate = addslashes('<div>' .
		'<a href="$infoLink"><u><b>$productName</b></u></a> ' .
			'<span class="smallText">$customersName</span>' .
			'<span style="float:right;">$dateAdded</span>' .
	'</div>' .
	'<div class="ui-widget ui-widget-content ui-corner-all"><table cellpadding="3" cellspacing="0" border="0" width="100%" style="margin:.5em;">' .
		'<tr>' .
			'<td><a href="$infoLink">$productImage</a></td>' .
			'<td class="main">$reviewText<br><br><i>$reviewRating</i></td>' .
		'</tr>' .
	'</table></div>');

	$lID = (int)Session::get('languages_id');

	$Qreviews = Doctrine_Query::create()
	->select('r.reviews_id,r.reviews_rating,rd.reviews_text, r.products_id,r.customers_name, pd.products_name, p.products_id, r.date_added,p.products_image')
	->from('Reviews r')
	->leftJoin('r.ReviewsDescription rd')
	->leftJoin('r.Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('r.reviews_id = ?', $_GET['reviews_id'])
	->andWhere('rd.languages_id = ?', $lID)
	->andWhere('pd.language_id = ?', $lID)
	->orderBy('r.date_added');

	$multiStore = $appExtension->getExtension('multiStore');
	$multiStoreEnabled = ($multiStore !== false && $multiStore->isEnabled() === true);

	if ($multiStoreEnabled){
		$Qreviews->leftJoin('p.ProductsToStores p2s')
		->andWhere('p2s.stores_id = ?', Session::get('current_store_id'));
	}

	$myReviews = $Qreviews->execute(array(),Doctrine_Core::HYDRATE_ARRAY);
	if ($myReviews){
		foreach($myReviews as $reviews){
			$infoLink = itw_app_link('products_id=' . $reviews['products_id'] . '&reviews_id=' . $reviews['reviews_id'], 'reviews', 'info_product');
			$productName = $reviews['Products'][0]['ProductsDescription'][0]['products_name'];
			$customersName = sprintf(sysLanguage::get('TEXT_REVIEW_BY'), tep_output_string_protected($reviews['customers_name']));
			$dateAdded = sprintf(sysLanguage::get('TEXT_REVIEW_DATE_ADDED'), tep_date_long($reviews['date_added']));
			$productImage = '<img src="imagick_thumb.php?width=150&height=150&path=rel&imgSrc='.  'images/'. $reviews['Products'][0]['products_image'].'" alt="'.$productName.'"/>';
			$reviewText = nl2br(tep_output_string_protected($reviews['ReviewsDescription'][0]['reviews_text']));
			$reviewRating = sprintf(TEXT_REVIEW_RATING, tep_image(sysConfig::get('DIR_WS_IMAGES') . 'stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])), sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating']));

			eval("\$tableContent .= \"<tr><td>$rowTemplate</td></tr>\";");
		}
	}else{
		$tableContent .= '<tr>' .
			'<td>' . 
				sysLanguage::get('TEXT_NO_REVIEWS') . 
			'</td>' .
		'</tr>';
	}
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_DEFAULT');
	$pageContents = $tableContent;
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->css(array(
		'float' => 'left'
	))
	->setHref(itw_app_link(tep_get_all_get_params(), 'product', 'info'))
	->draw() . 
	htmlBase::newElement('button')
	->setText(sysLanguage::get('TEXT_BUTTON_WRITE_REVIEW'))
	->setHref(itw_app_link('appExt=reviews&products_id=' . $_GET['products_id'], 'product_review', 'write'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
