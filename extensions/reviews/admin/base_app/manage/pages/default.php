<?php
	$tableGrid = htmlBase::newElement('grid');

	$tableGrid->addHeaderRow(array(
		'columns' => array(
			array('text' => sysLanguage::get('TABLE_HEADING_PRODUCTS')),
			array('text' => sysLanguage::get('TABLE_HEADING_RATING')),
			array('text' => sysLanguage::get('TABLE_HEADING_DATE_ADDED')),
			array('text' => sysLanguage::get('TABLE_HEADING_ACTION'))
		)
	));

	$currentPage = (int)(isset($_GET['page']) ? $_GET['page'] : 0);
	$allGetParams = tep_get_all_get_params(array('action', 'rID'));

	$Qreviews = dataAccess::setQuery('select * from {reviews} order by date_added desc')
	->setTable('{reviews}', TABLE_REVIEWS)
	->setPagination($currentPage, MAX_DISPLAY_SEARCH_RESULTS);
	while($Qreviews->next() !== false){
		$reviewId = $Qreviews->getVal('reviews_id');
		$productId = $Qreviews->getVal('products_id');

		if ((!isset($_GET['rID']) || $_GET['rID'] == $reviewId) && !isset($rInfo)){
			$QextraInfo = dataAccess::setQuery('select p.products_image, pd.products_name, length(rd.reviews_text) as reviews_text_size, (avg(r.reviews_rating) / 5 * 100) as average_rating from {reviews} r left join {reviews_description} rd using(reviews_id), {products} p left join {products_description} pd using(products_id) where p.products_id = r.products_id and pd.language_id = {language_id} and rd.reviews_id = r.reviews_id and r.reviews_id = {review_id}')
			->setTable('{reviews}', TABLE_REVIEWS)
			->setTable('{reviews_description}', TABLE_REVIEWS_DESCRIPTION)
			->setTable('{products}', TABLE_PRODUCTS)
			->setTable('{products_description}', TABLE_PRODUCTS_DESCRIPTION)
			->setValue('{review_id}', $reviewId)
			->setValue('{language_id}', Session::get('languages_id'))
			->runQuery();

			$rInfo = new objectInfo(array_merge($Qreviews->toArray(), $QextraInfo->toArray()));
		}

		$arrowIcon = htmlBase::newElement('icon')->setType('info')
		->setHref(itw_app_link($allGetParams . 'rID=' . $reviewId));

		$addCls = '';
		$onClickLink = itw_app_link($allGetParams . 'rID=' . $reviewId);
		if (isset($rInfo) && $reviewId == $rInfo->reviews_id){
			$addCls = 'ui-state-default';
			$onClickLink = itw_app_link($allGetParams . 'rID=' . $reviewId, null, 'preview');
			$arrowIcon->setType('circleTriangleEast');
		}

		$ratingBar = htmlBase::newElement('ratingbar')->setStars(5)->setValue($Qreviews->getVal('reviews_rating'));

		$tableGrid->addBodyRow(array(
			'addCls'  => $addCls,
			'click'   => 'document.location=\'' . $onClickLink . '\'',
			'columns' => array(
				array('text' => tep_get_products_name($productId)),
				array('text' => $ratingBar->draw(), 'align' => 'center'),
				array('text' => tep_date_short($Qreviews->getVal('date_added')), 'align' => 'center'),
				array('text' => $arrowIcon->draw(), 'align' => 'right')
			)
		));
	}

	$infoBox = htmlBase::newElement('infobox');

	switch($action){
		case 'delete':
			$infoBox->setHeader('<b>' . sysLanguage::get('TEXT_INFO_HEADING_DELETE_REVIEW') . '</b>');
			$infoBox->setForm(array(
				'name'   => 'reviews',
				'action' => itw_app_link($allGetParams . 'action=deleteConfirm&rID=' . $rInfo->reviews_id)
			));

			$deleteButton = htmlBase::newElement('button')->setType('submit')->usePreset('delete');
			$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
			->setHref(itw_app_link($allGetParams . 'rID=' . $rInfo->reviews_id, null, 'default'));

			$infoBox->addButton($deleteButton)->addButton($cancelButton);

			$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DELETE_REVIEW_INTRO'));
			$infoBox->addContentRow('<b>' . $rInfo->products_name . '</b>');
			break;
		default:
			if (isset($rInfo)){
				$infoBox->setButtonBarLocation('top');
				$infoBox->setHeader('<b>' . $rInfo->products_name . '</b>');

				$editButton = htmlBase::newElement('button')->usePreset('edit')
				->setHref(itw_app_link($allGetParams . 'rID=' . $rInfo->reviews_id, null, 'edit'));

				$deleteButton = htmlBase::newElement('button')->usePreset('delete')
				->setHref(itw_app_link($allGetParams . 'action=delete&rID=' . $rInfo->reviews_id));

				$infoBox->addButton($editButton)->addButton($deleteButton);

				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_DATE_ADDED') . ' ' . tep_date_short($rInfo->date_added));

				if (!is_null($rInfo->last_modified)){
					$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_LAST_MODIFIED') . ' ' . tep_date_short($rInfo->last_modified));
				}

				$infoBox->addContentRow(tep_info_image($rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT));
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_REVIEW_AUTHOR') . ' ' . $rInfo->customers_name);

				$ratingBar = htmlBase::newElement('ratingbar')->setStars(5)->setValue($rInfo->reviews_rating);
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_REVIEW_RATING') . ' ' . $ratingBar->draw());
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_REVIEW_READ') . ' ' . $rInfo->reviews_read);
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_REVIEW_SIZE') . ' ' . $rInfo->reviews_text_size . ' bytes');
				$infoBox->addContentRow(sysLanguage::get('TEXT_INFO_PRODUCTS_AVERAGE_RATING') . ' ' . number_format($rInfo->average_rating, 2) . '%');
			}
			break;
	}
?>
 <div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_REVIEWS');?></div>
 <br />
 <div style="width:75%;float:left;">
  <div class="ui-widget ui-widget-content ui-corner-all" style="width:99%;margin-right:5px;margin-left:5px;">
   <div style="width:99%;margin:5px;">
   <?php echo $tableGrid->draw();?>
   <table border="0" width="100%" cellspacing="0" cellpadding="2">
    <tr>
     <td class="smallText" valign="top"><?php echo $Qreviews->showPageCount();?></td>
     <td class="smallText" align="right"><?php echo $Qreviews->showPageLinks();?></td>
    </tr>
   </table>
   </div>
  </div>
 </div>
 <div style="width:25%;float:right;"><?php echo $infoBox->draw();?></div>