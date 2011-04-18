<?php
	ob_start();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  $reviews_query_raw = "select r.reviews_id, left(rd.reviews_text, 100) as reviews_text, r.reviews_rating, r.date_added, r.customers_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd where r.products_id = '" . (int)$product->getID() . "' and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)Session::get('languages_id') . "' order by r.reviews_id desc";
  $reviews_split = new splitPageResults($reviews_query_raw, MAX_DISPLAY_NEW_REVIEWS);

  if ($reviews_split->number_of_rows > 0) {
    if ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3')) {
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $reviews_split->display_count(sysLanguage::get('TEXT_DISPLAY_NUMBER_OF_REVIEWS')); ?></td>
                    <td align="right" class="smallText"><?php echo sysLanguage::get('TEXT_RESULT_PAGE') . ' ' . $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
    }

    $reviews_query = tep_db_query($reviews_split->sql_query);
    while ($reviews = tep_db_fetch_array($reviews_query)) {
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main"><?php echo '<a href="' . itw_app_link('products_id=' . (int)$product->getID() . '&reviews_id=' . $reviews['reviews_id'], 'reviews', 'info_product') . '"><u><b>' . sprintf(sysLanguage::get('TEXT_REVIEW_BY'), tep_output_string_protected($reviews['customers_name'])) . '</b></u></a>'; ?></td>
                    <td class="smallText" align="right"><?php echo sprintf(sysLanguage::get('TEXT_REVIEW_DATE_ADDED'), tep_date_long($reviews['date_added'])); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                  <tr class="infoBoxContents">
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                        <td valign="top" class="main"><?php echo tep_break_string(tep_output_string_protected($reviews['reviews_text']), 60, '-<br>') . ((strlen($reviews['reviews_text']) >= 100) ? '..' : '') . '<br><br><i>' . sprintf(TEXT_REVIEW_RATING, tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif', sprintf(sysLanguage::get('TEXT_OF_5_STARS'), $reviews['reviews_rating'])), sprintf(sysLanguage::get('TEXT_OF_5_STARS'), $reviews['reviews_rating'])) . '</i>'; ?></td>
                        <td width="10" align="right"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
    }
?>
<?php
  } else {
?>
              <tr>
                <td><?php new infoBox(array(array('text' => sysLanguage::get('TEXT_NO_REVIEWS')))); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
  }

  if (($reviews_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $reviews_split->display_count(sysLanguage::get('TEXT_DISPLAY_NUMBER_OF_REVIEWS')); ?></td>
                    <td align="right" class="smallText"><?php echo sysLanguage::get('TEXT_RESULT_PAGE') . ' ' . $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
  }
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                  <tr class="infoBoxContents">
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                        <td class="main"><?php echo htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link(tep_get_all_get_params(), 'product', 'info'))->draw(); ?></td>
                        <td class="main" align="right"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_WRITE_REVIEW'))->setHref(itw_app_link(tep_get_all_get_params(), 'reviews', 'write_product'))->draw(); ?></td>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
            <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" align="right" valign="top"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td align="center" class="smallText">
<?php
	echo '<img class="jqzoom" src="imagick_thumb.php?path=rel&imgSrc=' . $product->getImage() . '&width=250&height=250" alt="' . $image . '" />';
  if (in_array('B', $productsType)) {
	  echo '<p>' . htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_IN_CART'))->setHref(itw_app_link(tep_get_all_get_params(array('action')) . 'action=buy_now', 'product', 'reviews'))->draw() . '</p>';
  }
  if (in_array('R', $productsType))
  {
	  $inventory_query = tep_db_query("select * from ".TABLE_PRODUCTS_BARCODE." where products_id='".$_GET['products_id']."'");
	  if(tep_db_num_rows($inventory_query)>0) {
	  	  echo '<p>' . htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_IN_QUEUE'))->setHref(itw_app_link(tep_get_all_get_params(array('action')) . 'action=rent_now', 'product', 'reviews'))->draw() . '</p>';
	  }
  }
?>
                </td>
              </tr>
            </table>
          </td>
        </table></td>
      </tr>
    </table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageContent->set('pageTitle', $product->getName());
	$pageContent->set('pageContent', $pageContents);
