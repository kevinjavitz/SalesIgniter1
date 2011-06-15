<?php
	$Qreview = Doctrine_Query::create()
	->select('r.reviews_id, r.products_id, r.customers_name, r.date_added, r.last_modified, r.reviews_read, rd.reviews_text, r.reviews_rating, p.products_image, pd.products_name')
	->from('Reviews r')
	->leftJoin('r.ReviewsDescription rd')
	->leftJoin('r.Products p')
	->leftJoin('p.ProductsDescription pd')
	->where('r.reviews_id = ?', (int) $_GET['rID'])
	->andWhere('pd.language_id = ?', Session::get('languages_id'))
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	$ratingBar = htmlBase::newElement('ratingbar')
	->setId('reviewRating')
	->setName('reviews_rating')
	->setStars(5)
	->setValue($Qreview[0]['reviews_rating']);
?>
<div class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE_REVIEWS');?></div>
<br />
<form name="update" action="<?php echo itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . (int)$_GET['rID'], null, 'preview');?>" method="post">
<div style="width:100%;display:inline-block;">
	<p>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_PRODUCT'); ?></b> <?php echo $Qreview[0]['Products'][0]['ProductsDescription'][0]['products_name']; ?></div>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_FROM'); ?></b> <?php echo $Qreview[0]['customers_name']; ?></div>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_DATE'); ?></b> <?php echo tep_date_short($Qreview[0]['date_added']); ?></div>
	</p>
	<p>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_REVIEW'); ?></b></div>
		<div class="main"><?php echo tep_draw_textarea_field('reviews_text', 'soft', '60', '15', $Qreview[0]['ReviewsDescription'][0]['reviews_text']); ?></div>
		<div class="main"><?php echo sysLanguage::get('ENTRY_REVIEW_TEXT');?></div>
	</p>
	<p>
		<div class="main"><b><?php echo sysLanguage::get('ENTRY_RATING'); ?></b></div>
		<div class="main"><?php echo $ratingBar->draw(); ?></div>
	</p>
</div>
<br />
<div style="display:inline-block;width:100%;text-align:right"><?php
	echo tep_draw_hidden_field('reviews_id', $Qreview[0]['reviews_id']) . tep_draw_hidden_field('products_id', $Qreview[0]['Products'][0]['products_id']) . tep_draw_hidden_field('customers_name', $Qreview[0]['customers_name']) . tep_draw_hidden_field('products_name', $Qreview[0]['Products'][0]['ProductsDescription'][0]['products_name']) . tep_draw_hidden_field('products_image', $Qreview[0]['Products'][0]['products_image']) . tep_draw_hidden_field('date_added', $Qreview[0]['date_added']);

	$previewButton = htmlBase::newElement('button')->setType('submit')->setText(sysLanguage::get('TEXT_BUTTON_PREVIEW'));
	$cancelButton = htmlBase::newElement('button')->usePreset('cancel')
	->setHref(itw_app_link(tep_get_all_get_params(array('action', 'rID')) . 'rID=' . $Qreview[0]['reviews_id'], null, 'default'));

	echo $previewButton->draw() . $cancelButton->draw();
?></div>
</form>