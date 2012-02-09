<?php
$categoryId = $_GET['catId'];

if(!isset($_GET['page'])){
	$pg = 1;
}else{
	$pg = $_GET['page'];
}


$pg_limit = (int) sysConfig::get('EXTENSION_PHOTO_GALLERY_IMAGES_PER_PAGE');

$Qcategory =Doctrine_Query::create()
	->from('PhotoGalleryCategories pgc')
	->leftJoin('pgc.PhotoGalleryCategoriesDescription pgcd')
	->where('pgcd.language_id = ?', Session::get('languages_id'))
	->andWhere('pgc.categories_id = ?', $categoryId)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

$Query = Doctrine_Query::create()

	->leftJoin('ImagesToCategories itc')
	->where('itc.categories_id = ?', $categoryId);

$listingPager = new Doctrine_Pager($Query, $pg, $pg_limit);
$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');

$pagerRange = new Doctrine_Pager_Range_Sliding(array(
		'chunk' => 5
	));

$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
$pagerLayout->setMyType('images');
$pagerLayout->setTemplate('<a href="{%url}" style="margin-left:5px;background-color:#ffffff;padding:3px;">{%page}</a>');
$pagerLayout->setSelectedTemplate('<span style="margin-left:5px;">{%page}</span>');

$pager = $pagerLayout->getPager();

$Result = $pager->execute()->toArray(true);

$pagerBar = $pagerLayout->display(array(), true);

$categoryTitle = $Qcategory[0]['PhotoGalleryCategoriesDescription'][0]['categories_title'];

$categoryHtml = '<div class="catDesc">'.$Qcategory[0]['PhotoGalleryCategoriesDescription'][0]['categories_description_text'].'</div>';

if(sysConfig::get('EXTENSION_PHOTO_GALLERY_DISPLAY_TYPE') == 'Slideshow'){
	ob_start();
	echo '<div class="slider-wrapper theme-default"> ';//<div class="ribbon"></div>
	echo '<div class="slider">';
	foreach($Result as $box){
		echo '<img src="'.sysConfig::getDirWsCatalog().'imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $box['file_name'] . '&width='.sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_WIDTH').'&height='.sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_HEIGHT').'" rel="'.sysConfig::getDirWsCatalog().'imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $box['file_name'] . '&width='.sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_WIDTH').'&height='.sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_HEIGHT').'" title="' .$box['caption'] .'" />';
	}
	echo '</div>';
	echo '</div><br style="clear:both"/>';
	?>
	<style type="text/css">

		.theme-default .nivo-controlNav {
			position:absolute;
			bottom:-<?php echo ((int)sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_HEIGHT')+15);?>px; /* Put the nav below the slider */
		}
		.theme-default .nivoSlider {
			width: <?php echo sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_WIDTH');?>px;
			height: <?php echo sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_HEIGHT');?>px;
		}
		.theme-default .nivo-controlNav a{
			width: <?php echo sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_WIDTH');?>px;
			height: <?php echo sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_HEIGHT');?>px;
			display: inline-block;
			margin-right:10px;
		}

		.theme-default .nivoSlider .nivo-controlNav img {
			display:inline-block;
			position:relative;
			margin-right:10px;
		}
		.slider-wrapper{
			height:<?php echo ((int)sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_HEIGHT') + 15 +(int)sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_HEIGHT'));?>px;
		}
	</style>
	<?php
	$categoryHtml .= ob_get_contents();
	ob_end_clean();
}else{
	ob_start();

	echo '<div class="fancyList">';
	foreach($Result as $box){
		echo '<a class="fancybox-button" rel="group" title="' .$box['caption'] .'" href="'.sysConfig::getDirWsCatalog().'imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $box['file_name'] . '&width='.sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_WIDTH').'&height='.sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_HEIGHT').'">'.'<img src="'.sysConfig::getDirWsCatalog().'imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $box['file_name'] . '&width='.sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_WIDTH').'&height='.sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_HEIGHT').'" /></a>';
	}
	echo '</div>';
	?>
	<style type="text/css">

		.fancybox-button {
			margin-left:10px;
			margin-bottom:10px;
			display: inline-block;
		}
	</style>
<?php
	$categoryHtml .= ob_get_contents();
	ob_end_clean();
}


$pageTitle = $categoryTitle;
$pageContents = $categoryHtml. $pagerBar;

$pageButtons = htmlBase::newElement('button')
	->setText('Back To Photo Gallery Homepage ')
	->setHref(itw_app_link('appExt=photoGallery', 'show_category', 'default'))
	->draw();

$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
?>