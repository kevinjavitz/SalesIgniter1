<?php
	$Qreview = Doctrine_Query::create()
	->select('r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating, p.products_image, pd.products_name')
	->from('Reviews r')
	->leftJoin('r.ReviewsDescription rd')
	->leftJoin('r.Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('r.reviews_id = ?', (int) $_GET['rID'])
	->andWhere('pd.language_id = ?', )
{products_description} pd using(products_id) where p.products_id = r.products_id and pd.language_id = {language_id} and r.reviews_id = {review_id} and r.reviews_id = rd.reviews_id')
	->setTable('{reviews}', TABLE_REVIEWS)
	->setTable('{reviews_description}', TABLE_REVIEWS_DESCRIPTION)
	->setTable('{products}', TABLE_PRODUCTS)
	->setTable('{products_description}', TABLE_PRODUCTS_DESCRIPTION)
	->setValue('{language_id}', Session::get('languages_id'))
	->setValue('{review_id}', (int)$_GET['rID'])
	->runQuery();

	$rInfo = new objectInfo($Qreview->toArray());
	$ratingBar = htmlBase::newElement('ratingbar')
	->setId('reviewRating')
	->setName('reviews_rating')
	->setStars(5)
	->setValue($rInfo->reviews_rating);
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE');?></div>
<br />
<form name="update" action="<?php echo itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . (int)$_GET['rID'], null, 'preview');?>" method="post">
<div style="width:100%;display:inline-block;">
	<p>
		<?php echo tep_image(DIR_WS_CATALOG_IMAGES . $rInfo->products_image, $rInfo->products_name, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5" align="right"');?>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_PRODUCT'); ?></b> <?php echo $rInfo->products_name; ?></div>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_FROM'); ?></b> <?php echo $rInfo->customers_name; ?></div>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_DATE'); ?></b> <?php echo tep_date_short($rInfo->date_added); ?></div>
	</p>
	<p>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_REVIEW'); ?></b></div>
		<div class="main"><?php echo tep_draw_textarea_field('reviews_text', 'soft', '60', '15', $rInfo->reviews_text); ?></div>
		<div class="main"><?php echo sysLanguage::get('ENTRY_REVIEW_TEXT');?></div>
	</p>
	<p>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_RATING'); ?></b></div>
		<div class="main"><?php echo $ratingBar->draw(); ?></div>
	</p>
</div>
<br />
<div style="display:inline-block;width:100%;text-align:right"><?php
	echo tep_draw_hidden_field('reviews_id', $rInfo->reviews_id) . tep_draw_hidden_field('products_id', $rInfo->products_id) . tep_draw_hidden_field('customers_name', $rInfo->customers_name) . tep_draw_hidden_field('products_name', $rInfo->products_name) . tep_draw_hidden_field('products_image', $rInfo->products_image) . tep_draw_hidden_field('date_added', $rInfo->date_added);

	$previewButton = htmlBase::newElement('button')->setType('submit')->setText(sysLanguage::get('TEXT_BUTTON_PREVIEW'));
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $rInfo->reviews_id, null, 'default'));

	echo $previewButton->draw() . $cancelButton->draw();
?></div>
</form>