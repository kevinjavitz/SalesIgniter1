<?php
	echo '<ul>';
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
		$lID = $languages[$i]['id'];
		echo '<li class="ui-tabs-nav-item"><a href="#langTab_' . $lID . '"><span>' . $languages[$i]['name'] . '</span></a></li>';
	}
	echo '</ul>';
	
	for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
		$langImage = tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']);
		$lID = $languages[$i]['id'];
		$name = ''; $description = '';$seo_url = ''; $htc_title = ''; $htc_desc = ''; $htc_keywords = ''; $htc_descrip = '';
		if (isset($_GET['cID'])){
			$name = $Category->BlogCategoriesDescription[$lID]->blog_categories_title;
			$description = $Category->BlogCategoriesDescription[$lID]->blog_categories_description_text;
			$seo_url = $Category->BlogCategoriesDescription[$lID]->blog_categories_seo_url;
			$htc_title = $Category->BlogCategoriesDescription[$lID]->blog_categories_htc_title;
			$htc_desc = $Category->BlogCategoriesDescription[$lID]->blog_categories_htc_desc;
			$htc_keywords = $Category->BlogCategoriesDescription[$lID]->blog_categories_htc_keywords;
		}
?>
<div id="langTab_<?php echo $lID;?>">
 <table cellpadding="3" cellspacing="0" border="0">
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_CATEGORIES_NAME'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('blog_categories_title[' . $lID . ']', $name); ?></td>
  </tr>
  <tr>
   <td class="main" valign="top"><?php echo sysLanguage::get('TEXT_CATEGORIES_DESCRIPTION'); ?></td>
   <td class="main"><?php echo tep_draw_textarea_field('blog_categories_description_text[' . $lID . ']', 'hard', 30, 5, $description, 'class="makeFCK"'); ?></td>
  </tr>
     <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
       <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_SORT_ORDER'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('sort_order', (isset($_GET['cID']) ? $Category->sort_order : ''), 'size="2"');?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo sysLanguage::get('TEXT_EDIT_CATEGORIES_SEO_URL'); ?></td>
   <td class="main"><?php echo tep_draw_input_field('blog_categories_seo_url[' . $lID . ']', $seo_url);?></td>
  </tr>
  <tr>
   <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
  </tr>
  <tr>
   <td class="main"><?php echo 'Header Tags Category Title:'; ?></td>
   <td class="main"><?php echo tep_draw_input_field('blog_categories_htc_title[' . $lID . ']', $htc_title);?></td>
  </tr>
  <tr>
   <td class="main"><?php echo 'Header Tags Category Description:'; ?></td>
   <td class="main"><?php echo tep_draw_input_field('blog_categories_htc_desc[' . $lID . ']', $htc_desc);?></td>
  </tr>
  <tr>
   <td class="main"><?php echo 'Header Tags Category Keywords:'; ?></td>
   <td class="main"><?php echo tep_draw_input_field('blog_categories_htc_keywords[' . $lID . ']', $htc_keywords);?></td>
  </tr>

 </table>
</div>
<?php
    }
?>