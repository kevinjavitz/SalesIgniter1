<?php

$PostStatusEnabled = htmlBase::newElement('radio')
	->setName('post_status')
	->setLabel('Published')
	->setValue('1');

$PostStatusDisabled = htmlBase::newElement('radio')
	->setName('post_status')
	->setLabel('Not Published')
	->setValue('0');

	$PostDate = htmlBase::newElement('input')
	->setName('post_date')
	->addClass('useDatepicker');
        if (isset($Post)){

            if ($Post['post_status'] == '1'){
			$PostStatusEnabled->setChecked(true);
		}else{
             if ($Post['post_status'] == '0')
			    $PostStatusDisabled->setChecked(true);
		}
            $PostDate->setValue($Post['post_date']);
    }
$PostFeaturedImage = htmlBase::newElement('uploadManagerInput')
	->setName('post_featured_image')
	->setFileType('image')
	->autoUpload(true)
	->showPreview(true)
	->showMaxUploadSize(true);

$PostFeaturedImage->setPreviewFile($Post['post_featured_image']);

$PostFullFeaturedImage = htmlBase::newElement('uploadManagerInput')
	->setName('post_full_featured_image')
	->setFileType('image')
	->autoUpload(true)
	->showPreview(true)
	->showMaxUploadSize(true);

$PostFullFeaturedImage->setPreviewFile($Post['post_full_featured_image']);

$PostFeaturedVideo = htmlBase::newElement('input')
->setName('post_featured_media')
->setValue($Post['post_featured_media']);

$PostRedirectUrl = htmlBase::newElement('input')
	->setName('post_redirect_url')
	->setValue($Post['post_redirect_url']);



?>
<table cellpadding="0" cellspacing="0" border="0">
      <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
 <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_POSTS_STATUS'); ?></td>
   <td class="main"><?php echo $PostStatusEnabled->draw() . $PostStatusDisabled->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_POSTS_DATE_AVAILABLE'); ?><br><small>(YYYY-MM-DD)</small></td>
   <td class="main"><?php echo $PostDate->draw(); ?></td>
  </tr>
      <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_POSTS_FEATURED_IMAGE'); ?><br></td>
		<td class="main"><?php echo $PostFeaturedImage->draw(); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_POSTS_FULL_FEATURED_IMAGE'); ?><br></td>
		<td class="main"><?php echo $PostFullFeaturedImage->draw(); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_POSTS_FEATURED_VIDEO'); ?><br><small>(youtube-vimeo only)</small></td>
		<td class="main"><?php echo $PostFeaturedVideo->draw(); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
	<tr>
		<td class="main"><?php echo sysLanguage::get('TEXT_POSTS_REDIRECT_URL'); ?><br><small>(youtube-vimeo only)</small></td>
		<td class="main"><?php echo $PostRedirectUrl->draw(); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
	</tr>
</table>
 <?php echo '<ul>';
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
		$lID = $languages[$i]['id'];
		echo '<li class="ui-tabs-nav-item"><a href="#langTab_' . $lID . '"><span>' . $languages[$i]['name'] . '</span></a></li>';
	}
	echo '</ul>';
	
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
		$lID = $languages[$i]['id'];
		
		$PostTitle = htmlBase::newElement('input')
		->setName('blog_post_title[' . $lID . ']');
		
		
		$PostDescription = htmlBase::newElement('ck_editor')
		->setName('blog_post_text[' . $lID . ']');
		
		$PostSeoUrl = htmlBase::newElement('input')
		->setName('blog_post_seo_url[' . $lID . ']');
		
		$PostPageTitle = htmlBase::newElement('input')
		->setName('blog_post_head_title[' . $lID . ']');
		
		$PostPageDescription = htmlBase::newElement('textarea')->attr('cols', '60')->attr('rows', '5')
		->setName('blog_post_head_desc[' . $lID . ']');
		
		$PostPageKeywords = htmlBase::newElement('textarea')->attr('cols', '60')->attr('rows', '5')
		->setName('blog_post_head_keywords[' . $lID . ']');
		
		if (isset($Post)){
			$PostTitle->setValue(stripslashes($Post->BlogPostsDescription[$lID]['blog_post_title']));
			$PostDescription->html(stripslashes($Post->BlogPostsDescription[$lID]['blog_post_text']));
			$PostSeoUrl->setValue($Post->BlogPostsDescription[$lID]['blog_post_seo_url']);
			$PostPageTitle->setValue(stripslashes($Post->BlogPostsDescription[$lID]['blog_post_head_title']));
			$PostPageDescription->html(stripslashes($Post->BlogPostsDescription[$lID]['blog_post_head_desc']));
			$PostPageKeywords->html(stripslashes($Post->BlogPostsDescription[$lID]['blog_post_head_keywords']));
		}
?>

  <div id="langTab_<?php echo $lID;?>">
 <table cellpadding="0" cellspacing="0" border="0">

  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_POSTS_NAME'); ?></td>
   <td class="main"><?php echo $PostTitle->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_POSTS_DESCRIPTION'); ?></td>
   <td class="main"><?php echo $PostDescription->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td colspan="2" class="main"><hr><?php echo sysLanguage::get('TEXT_PRODUCT_META_INFO'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_POSTS_SEO_URL'); ?></td>
   <td class="main"><?php echo $PostSeoUrl->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_POSTS_PAGE_TITLE'); ?></td>
   <td class="main"><?php echo $PostPageTitle->draw();?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_POSTS_HEADER_DESCRIPTION'); ?></td>
   <td class="main"><?php echo $PostPageDescription->draw(); ?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_POSTS_KEYWORDS'); ?></td>
   <td class="main"><?php echo $PostPageKeywords->draw();?></td>
  </tr>
 </table>
</div>
<?php
    }
?>