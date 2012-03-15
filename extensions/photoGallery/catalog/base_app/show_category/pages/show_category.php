<?php
$categoryId = $_GET['catId'];

if(!isset($_GET['page'])){
	$pg = 1;
}else{
	$pg = $_GET['page'];
}


$pg_limit = (int) sysConfig::get('EXTENSION_PHOTO_GALLERY_IMAGES_PER_PAGE');

$Qsubcategory =Doctrine_Query::create()
	->from('PhotoGalleryCategories pgc')
	->leftJoin('pgc.PhotoGalleryCategoriesDescription pgcd')
	->where('pgcd.language_id = ?', Session::get('languages_id'))
	->andWhere('pgc.parent_id = ?', $categoryId)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

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

if(sysConfig::get('EXTENSION_PHOTO_GALLERY_DISPLAY_TYPE') == 'SlideshowNivo'){
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
}elseif(sysConfig::get('EXTENSION_PHOTO_GALLERY_DISPLAY_TYPE') == 'Slideshow'){
	ob_start();
	echo '<ul class="slider">';
	foreach($Result as $box){
		echo '<li><img width="'.sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_WIDTH').'" height="'.sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_HEIGHT').'" src="'.sysConfig::getDirWsCatalog().'imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $box['file_name'] . '&width='.sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_WIDTH').'&height='.sysConfig::get('EXTENSION_PHOTO_GALLERY_BIG_IMAGE_HEIGHT').'" rel="'.sysConfig::getDirWsCatalog().'imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $box['file_name'] . '&width='.sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_WIDTH').'&height='.sysConfig::get('EXTENSION_PHOTO_GALLERY_THUMBNAIL_IMAGE_HEIGHT').'" title="' .$box['caption'] .'" /></li>';
	}
	echo '</ul>';

	if(count($Qsubcategory) > 0){
		$nrRows = 'double';
		switch(sysConfig::get('EXTENSION_PHOTO_GALLERY_NUMBER_ROWS_SUBCATEGORY')){
			case '2': $nrRows = 'double';
					  break;
			case '3': $nrRows = 'triple';
			          break;
			case '4': $nrRows = 'quad';
			          break;
			case '5': $nrRows = 'six';
			          break;
		}
		echo '<div class="subCatDiv"><ul class="'.$nrRows.'">';
		foreach($Qsubcategory as $subcat){
			?>
		<li><a class="subcatElem" href="<?php echo itw_app_link('appExt=photoGallery&catId='.$subcat['categories_id'],'show_category','show_category');?>">
			<?php
			if(sysConfig::get('EXTENSION_PHOTO_GALLERY_SHOW_SUBCATEGORY_IMAGES') == 'True'){
			?>
				<div class="innerimg">
					<?php
						echo '<img src="'.sysConfig::getDirWsCatalog().'imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $subcat['categories_image'] . '&width='.sysConfig::get('EXTENSION_PHOTO_GALLERY_CATEGORY_THUMBNAIL_IMAGE_WIDTH').'&height='.sysConfig::get('EXTENSION_PHOTO_GALLERY_CATEGORY_THUMBNAIL_IMAGE_HEIGHT').'" alt="' .$subcat['PhotoGalleryCategoriesDescription'][0]['categories_name'] .'" />';
					?>
				</div>
			<?php
			}
			?>
			<div class="name">
				<?php
					echo $subcat['PhotoGalleryCategoriesDescription'][0]['categories_title'];
				?>
			</div>
		</a></li>
		<?php
		}
		echo '</ul></div>';
	}

	echo '<div id="prevmainGallery"></div><div id="nextmainGallery"></div><br style="clear:both"/>';

	?>
<style type="text/css">

	.subCatDiv ul{
		overflow: hidden;
	}
	.subCatDiv ul li{
		float:left;
		display: inline;
	}
	.double li	{ width:50%;}
	.triple li	{ width:33.333%; }
	.quad li		{ width:25%; }
	.six li		{ width:16.666%; }
	.slider{

	}
</style>
<?php
	$categoryHtml .= ob_get_contents();
	ob_end_clean();
}
else{
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
$pageContents = $categoryHtml;
if(sysConfig::get('EXTENSION_PHOTO_GALLERY_SHOW_PAGING') == 'True'){
	$pageContents .= $pagerBar;
}
$pageButtons = '';
if(sysConfig::get('EXTENSION_PHOTO_GALLERY_SHOW_BUTTON') == 'True'){
	$pageButtons = htmlBase::newElement('button')
	->setText('Back To Photo Gallery Homepage ')
	->setHref(itw_app_link('appExt=photoGallery', 'show_category', 'default'))
	->draw();
}

$pageContent->set('pageTitle', $pageTitle);
$pageContent->set('pageContent', $pageContents);
$pageContent->set('pageButtons', $pageButtons);
?>