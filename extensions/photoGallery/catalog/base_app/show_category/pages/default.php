<?php
/*
	Blog Extension Version 1

	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

if ($_GET['appPage'] == 'default'){
	$app_pg = null;
}else{
	$app_pg = $_GET['appPage'];
}

if(!isset($_GET['page'])){
	$pg = 1;
}else{
	$pg = $_GET['page'];
}


$pg_limit = (int) sysConfig::get('EXTENSION_PHOTO_GALLERY_IMAGES_PER_PAGE');

$Query = Doctrine_Query::create()
	->from('PhotoGalleryCategories pgc')
	->leftJoin('pgc.PhotoGalleryCategoriesDescription pgcd')
	->where('pgcd.language_id = ?', Session::get('languages_id'));
	//->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


$listingPager = new Doctrine_Pager($Query, $pg, $pg_limit);
$pagerLink = itw_app_link(tep_get_all_get_params(array('page', 'action')) . 'page={%page_number}');

$pagerRange = new Doctrine_Pager_Range_Sliding(array(
		'chunk' => 5
	));

$pagerLayout = new PagerLayoutWithArrows($listingPager, $pagerRange, $pagerLink);
$pagerLayout->setMyType('categories');
$pagerLayout->setTemplate('<a href="{%url}" style="margin-left:5px;background-color:#ffffff;padding:3px;">{%page}</a>');
$pagerLayout->setSelectedTemplate('<span style="margin-left:5px;">{%page}</span>');

$pager = $pagerLayout->getPager();

$Result = $pager->execute()->toArray(true);

$pagerBar = $pagerLayout->display(array(), true);
ob_start();
    foreach($Result as $box){
	    ?>
	    <a class="roundbox" href="<?php echo itw_app_link('appExt=photoGallery&catId='.$box['categories_id'],'show_category','show_category');?>">
		    <div class="innerimg"><?php
			echo '<img src="'.sysConfig::getDirWsCatalog().'imagick_thumb.php?path=rel&imgSrc=' . 'images/'. $box['categories_image'] . '&width='.sysConfig::get('EXTENSION_PHOTO_GALLERY_CATEGORY_THUMBNAIL_IMAGE_WIDTH').'&height='.sysConfig::get('EXTENSION_PHOTO_GALLERY_CATEGORY_THUMBNAIL_IMAGE_HEIGHT').'" alt="' .$box['PhotoGalleryCategoriesDescription'][0]['categories_name'] .'" />';
			?></div>
		    <div class="caption">
			    <div class="name"><?php
				echo $box['PhotoGalleryCategoriesDescription'][Session::get('languages_id')]['categories_title'];
				?></div>
			    <div class="date">
				    <?php
					echo strftime(sysLanguage::getDateFormat('long') ,strtotime($box['date_added']));
				    ?>
			    </div>
			    <div class="nrphoto">
				    <?php
					$QimagesToCategories = Doctrine_Query::create()
					->from('ImagesToCategories')
					->where('categories_id = ?', $box['categories_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					    echo count($QimagesToCategories).' Photos';
				    ?>
			    </div>
		    </div>
	    </a>
	    <?php
    }
    $contentHtml = ob_get_contents();
	ob_end_clean();

	ob_start();
	?>
	<style type="text/css">
		.roundbox{
			<?php echo buildBorderRadius('6px', '6px', '6px', '6px');?>
			background-color: #cccccc;
			padding-left:5px;
			padding-top:5px;
			padding-right:5px;
			padding-bottom:10px;
			display: inline-block;
			margin-left:10px;
		}
		.roundbox:hover{
			text-decoration: none;
		}
	</style>
<?php
	$contentCss = ob_get_contents();
	ob_end_clean();

	ob_start();
	?>
<script type="text/javascript">
	$(document).ready(function(){
		$(".roundbox").hover(function() {
				$(this).stop().animate({opacity: "0.8"}, 'slow');
			},
			function() {
				$(this).stop().animate({opacity: "1"}, 'slow');
			});
	});
</script>
	<?php
	$contentJs = ob_get_contents();
	ob_end_clean();

	$pageTitle = 'Photo Gallery';
	$pageContents = $contentHtml . $contentCss. $contentJs . '<br/><br/>'.$pagerBar;

	/*$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();*/

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	//$pageContent->set('pageButtons', $pageButtons);
