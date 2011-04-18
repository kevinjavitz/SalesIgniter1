<div id="tabAlsoPurchased"><?php

  if (isset($_GET['products_id'])) {
    $orders_query = tep_db_query("select p.products_id, p.products_image from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p where opa.products_id = '" . (int)$_GET['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$_GET['products_id'] . "' and opb.products_id = p.products_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . MAX_DISPLAY_ALSO_PURCHASED);
    $num_products_ordered = tep_db_num_rows($orders_query);
    if ($num_products_ordered >= MIN_DISPLAY_ALSO_PURCHASED) {
?>
<!-- also_purchased_products //-->
<?php
      $info_box_contents = array();
      $info_box_contents[] = array('text' => sysLanguage::get('TEXT_ALSO_PURCHASED_PRODUCTS'));

      new contentBoxHeading($info_box_contents);

      $row = 0;
      $col = 0;
      $info_box_contents = array();
      while ($orders = tep_db_fetch_array($orders_query)) {

//------------------------- BOX set begin block -----------------------------//
      $box_query = tep_db_query('select ptb.box_id, ptb.disc, pd.products_name as box_name from ' . TABLE_PRODUCTS_TO_BOX . ' ptb
      inner join ' . TABLE_PRODUCTS_DESCRIPTION . ' pd on pd.products_id = ptb.box_id
      where ptb.products_id='.(int)$orders['products_id']);
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

        $orders['products_name'] = tep_get_products_name($orders['products_id']);
        $link = itw_app_link('products_id=' . $orders['products_id'], 'product', 'info');
		$imageLink = '<img src="' . 'imagick_thumb.php?path=rel&width=150&height=150&imgSrc='. sysConfig::getDirWsCatalog() .'images/'.$orders['products_image'] .'"/>';
        $info_box_contents[$row][$col] = array('align' => 'center',
                                               'params' => 'class="smallText" width="33%" valign="top"',
                                               'text' => '<a href="' . $link . '">' . $imageLink . '</a><br><a href="' . $link . '">' . $orders['products_name'] . '</a>'.$products_series);

        $col ++;
        if ($col > 2) {
          $col = 0;
          $row ++;
        }
      }

      new contentBox($info_box_contents);
?>
<!-- also_purchased_products_eof //-->
<?php
    }
  }
?>


</div>