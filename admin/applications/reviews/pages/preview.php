<?php
	if (isset($_POST) && !empty($_POST)){
		$rInfo = new objectInfo($_POST);
	}else{
		$Qreview = dataAccess::setQuery('select r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating, p.products_image, pd.products_name from {reviews} r, {reviews_description} rd, {products} p left join {products_description} pd using(products_id) where p.products_id = r.products_id and pd.language_id = {language_id} and r.reviews_id = {review_id} and r.reviews_id = rd.reviews_id')
		->setTable('{reviews}', TABLE_REVIEWS)
		->setTable('{reviews_description}', TABLE_REVIEWS_DESCRIPTION)
		->setTable('{products}', TABLE_PRODUCTS)
		->setTable('{products_description}', TABLE_PRODUCTS_DESCRIPTION)
		->setValue('{language_id}', Session::get('languages_id'))
		->setValue('{review_id}', (int)$_GET['rID'])
		->runQuery();
		
		$rInfo = new objectInfo($Qreview->toArray());
	}
	$ratingBar = htmlBase::newElement('ratingbar')->setStars(5)->setValue($rInfo->reviews_rating);
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<form name="update" action="<?php echo itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'action=save&rID=' . (int)$_GET['rID']);?>" method="post">
<div style="width:100%;display:inline-block;">
	<p>
		<?php echo tep_image(DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5" align="right"');?>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_PRODUCT'); ?></b> <?php echo $rInfo->products_name; ?></div>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_FROM'); ?></b> <?php echo $rInfo->customers_name; ?></div>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_DATE'); ?></b> <?php echo tep_date_short($rInfo->date_added); ?></div>
	</p>
	<p>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_REVIEW'); ?></b></div>
		<div class="main"><?php echo nl2br(tep_break_string($rInfo->reviews_text, 15)); ?></div>
	</p>
	<p>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_RATING'); ?></b></div>
		<div class="main"><?php echo $ratingBar->draw(); ?><br /><small>[<?php echo sprintf(sysLanguage::get('TEXT_OF_5_STARS'), $rInfo->reviews_rating); ?>]</small></div>
	</p>
</div>
<br />
<div style="display:inline-block;width:100%;text-align:right"><?php
	if (isset($_POST) && !empty($_POST)){
		/* Re-Post all POST'ed variables */
		reset($_POST);
		while(list($key, $value) = each($_POST)) echo tep_draw_hidden_field($key, $value);
		
		$updateButton = htmlBase::newElement('button')->setType('submit')->usePreset('save');
		$backButton = htmlBase::newElement('button')->usePreset('back')
		->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rInfo->reviews_id, null, 'edit'));
		$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
		->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rInfo->reviews_id, null, 'default'));
		
		echo $backButton->draw() . $updateButton->draw() . $cancelButton->draw();
	}else{
		if (isset($_GET['origin'])){
			$back_url = $_GET['origin'];
			$back_url_params = '';
		}else{
			$back_url = FILENAME_REVIEWS;
			$back_url_params = tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rInfo->reviews_id;
		}
		$backButton = htmlBase::newElement('button')->usePreset('back')
		->setHref(itw_app_link($back_url_params, null, 'default'));
		
		echo $backButton->draw();
	}
?></div>
</form>