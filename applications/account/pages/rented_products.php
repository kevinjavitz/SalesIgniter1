<?php
//here i check for children too
$listing_sql = "select rp.rented_products_id, pd.products_name, p.products_image, p.products_id, rp.date_added  from " . TABLE_PRODUCTS . " p
        INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = p.products_id
        INNER JOIN ".TABLE_RENTED_PRODUCTS." rp ON rp.products_id = p.products_id and customers_id = '".$cid ."'
       where p.products_status = '1' and pd.language_id = '" . (int)Session::get('languages_id') . "' order by rp.date_added desc";
        ob_start();
 ?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
        <?php
        $products_count = MAX_DISPLAY_SEARCH_RESULTS;
       // include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING);


       $listing_split = new splitPageResults($listing_sql, $products_count);

      if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
    ?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText"><?php echo $listing_split->display_count(sysLanguage::get('TEXT_DISPLAY_NUMBER_OF_PRODUCTS')); ?></td>
        <td class="smallText" align="right"><?php echo sysLanguage::get('TEXT_RESULT_PAGE') . ' ' . $listing_split->display_links($products_count, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
      </tr>
    </table>
    <?php
      }

  $list_box_contents = array();

    $list_box_contents[0][] = array('align' => 'center',
                                    'params' => 'class="productListing-heading"',
                                    'text' => '&nbsp;' . sysLanguage::get('TABLE_HEADING_PRODUCTS') . '&nbsp;');

    $list_box_contents[0][] = array('align' => 'center',
                                    'params' => 'class="productListing-heading" width="100"',
                                    'text' => '&nbsp;');

    $list_box_contents[0][] = array('align' => 'center',
                                    'params' => 'class="productListing-heading"',
                                    'text' => '&nbsp;' . sysLanguage::get('TABLE_HEADING_RENTAL_DATE') . '&nbsp;');

              if ($listing_split->number_of_rows > 0) {
                $rows = 0;
                $listing_query = tep_db_query($listing_split->sql_query);
                while ($listing = tep_db_fetch_array($listing_query)) {
                  $rows++;

            //------------------------- BOX set begin block -----------------------------//
                  $box_query = tep_db_query('select ptb.box_id, ptb.disc, pd.products_name as box_name from ' . TABLE_PRODUCTS_TO_BOX . ' ptb
                  inner join ' . TABLE_PRODUCTS_DESCRIPTION . ' pd on pd.products_id = ptb.box_id
                  where ptb.products_id='.(int)$listing['products_id']);
                  $box_array = tep_db_fetch_array($box_query);
                  $box_id = $box_array['box_id'];

                  if ($box_id)
                  {
                          $discs_count_query1 = tep_db_query('select max(disc) as count from ' . TABLE_PRODUCTS_TO_BOX . ' where box_id='.(int)$box_id);
                          $discs_count_array1 = tep_db_fetch_array($discs_count_query1);
                          $discs_count1 = $discs_count_array1['count'];

                          $discs_count_query2 = tep_db_query('select count(products_id) as count from ' . TABLE_PRODUCTS_TO_BOX . ' where box_id='.(int)$box_id.' group by box_id');
                          $discs_count_array2 = tep_db_fetch_array($discs_count_query2);
                          $discs_count2 = $discs_count_array2['count'];

                          if ($discs_count2 > $discs_count1) $discs_count = $discs_count2;
                          else $discs_count = $discs_count1;
                  }

                  if ($box_id)
                    $products_series ='<br><small><i>'.sprintf(sysLanguage::get('TEXT_BS_SERIES'),$box_array['disc'], $discs_count, $box_array['box_name']) . '</i></small>';
                  else
                    $products_series = '';
            //------------------------- BOX set end block -----------------------------//


                  if (($rows/2) == floor($rows/2)) {
                    $list_box_contents[] = array('params' => 'class="productListing-even"');
                  } else {
                    $list_box_contents[] = array('params' => 'class="productListing-odd"');
                  }

                  $cur_row = sizeof($list_box_contents) - 1;

                    $list_box_contents[$cur_row][] = array('align' => 'left',
                                                           'params' => 'class="productListing-data"',
                                                           'text'  => '&nbsp;<a href="' . itw_app_link('products_id=' . $listing['products_id'], 'product', 'info') . '">' . $listing['products_name'] . '</a>&nbsp;');
                    $list_box_contents[$cur_row][] = array('align' => 'left',
                                                           'params' => 'class="productListing-data" width="100"',
                                                           'text'  => '&nbsp;<a href="' . itw_app_link('products_id=' . $listing['products_id'], 'product', 'info') . '">' . tep_image(DIR_WS_IMAGES . $listing['products_image'], $listing['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>&nbsp;');
                    $list_box_contents[$cur_row][] = array('align' => 'center',
                                                           'params' => 'class="productListing-data"',
                                                           'text'  => date('m/d/y h:i:s', strtotime($listing['date_added'])));
                  }

                new productListingBox($list_box_contents);
              } else {
                $list_box_contents = array();

                $list_box_contents[0] = array('params' => 'class="productListing-odd"');
                $list_box_contents[0][] = array('params' => 'class="productListing-data"',
                                               'text' => sysLanguage::get('TEXT_NO_PRODUCTS'));

                new productListingBox($list_box_contents);
              }

              if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
            ?>
            <table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText"><?php echo $listing_split->display_count(sysLanguage::get('TEXT_DISPLAY_NUMBER_OF_PRODUCTS')); ?></td>
                <td class="smallText" align="right"><?php echo sysLanguage::get('TEXT_RESULT_PAGE') . ' ' . $listing_split->display_links($products_count, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>
                <?php echo tep_draw_hidden_field('new_mem');?>
                </td>
              </tr>
            </table>
            <?php
              }
        ?>
        </td>
      </tr>
    </table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE');
	
	$pageContent->set('pageForm', array(
		'name' => 'frm',
		'action' => itw_app_link(null, 'index', 'default', 'SSL'),
		'method' => 'post'
	));
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
?>