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
	->select('r.reviews_id, r.reviews_rating, rd.reviews_text, r.products_id, r.customers_name, pd.products_name, p.products_id, r.date_added, p.products_image')
	->from('Reviews r')
	->leftJoin('r.ReviewsDescription rd')
	->leftJoin('r.Products p')
	->leftJoin('p.ProductsDescription pd')
	->andWhere('rd.languages_id = ?', $lID)
	->andWhere('pd.language_id = ?', $lID)
	->orderBy('r.date_added');

	EventManager::notify('ReviewsQueryBeforeExecute', &$Qreviews);

	$tableContent = '';
	$Result = $Qreviews->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    if ($Result){
		foreach($Result as $rInfo){
			/*
			 * @TODO: Move product review info into the product application, since it only deals with that product 
			 *        and can be more easily dealt with for adding reviews of other things
			 */
			$infoLink = itw_app_link('appExt=reviews&products_id=' . $rInfo['products_id'] . '&reviews_id=' . $rInfo['reviews_id'], 'product_review', 'details');
			$productName = $rInfo['Products'][0]['ProductsDescription'][0]['products_name'];			
			$customersName = sprintf(sysLanguage::get('TEXT_REVIEW_BY'), $rInfo['customers_name']);
			$dateAdded = sprintf(sysLanguage::get('TEXT_REVIEW_DATE_ADDED'), tep_date_long($rInfo['date_added']));
			$rating = $rInfo['reviews_rating'];
			
			/*
			 * @TODO: Change to html class "image"
			 */
			$productImage = '<img src="imagick_thumb.php?width=150&height=150&path=rel&imgSrc=' .  sysConfig::get('DIR_WS_IMAGES') . $rInfo['Products'][0]['products_image'] . '" alt="' . $productName . '"/>';
			$reviewText = nl2br(tep_break_string(htmlspecialchars($rInfo['ReviewsDescription'][0]['reviews_text']), 60, '-<br>') . ((strlen($rInfo['reviews_text']) >= 100) ? '..' : ''));
			$reviewRating = sprintf(TEXT_REVIEW_RATING, tep_image(sysConfig::get('DIR_WS_IMAGES') . 'stars_' . $rating . '.gif', sprintf(TEXT_OF_5_STARS, $rating)), sprintf(TEXT_OF_5_STARS, $rating));

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
	->setHref(itw_app_link(tep_get_all_get_params(), 'product', 'info'))
	->draw() . 
	htmlBase::newElement('button')
	->setText(sysLanguage::get('TEXT_BUTTON_WRITE_REVIEW'))
	->setHref(itw_app_link('appExt=reviews&products_id=' . $_GET['products_id'], 'product_review', 'write'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
