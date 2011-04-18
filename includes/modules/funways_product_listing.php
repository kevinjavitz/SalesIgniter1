<?php
/*
  $Id: product_listing.php,v 1.44 2003/06/09 22:49:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

if (isset($pw_mispell)){ //added for search enhancements mod
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
<tr><td><?php echo $pw_string; ?></td></tr>
</table>
<?php
 } //end added search enhancements mod

  $listing_split = new splitPageResults($productListing->getQuery(), $products_count, 'p.products_id');

  if ( ($listing_split->number_of_rows > 0) && ( (sysConfig::get('PREV_NEXT_BAR_LOCATION') == '1') || (sysConfig::get('PREV_NEXT_BAR_LOCATION') == '3') ) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count(sysConfig::get('TEXT_DISPLAY_NUMBER_OF_PRODUCTS')); ?></td>
    <td class="smallText" align="right"><?php echo sysLanguage::get('TEXT_RESULT_PAGE') . ' ' . $listing_split->display_links($products_count, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>
<?php
  }
?>
<div id="container">
<?php
  //Following code added by Deepali on 22 Aug, 2005 to display message that the customer has not purchased any of the memebership plans-->
  if (isset($_GET['msg']) && tep_not_null($_GET['msg'])){
      switch($_GET['msg']){
          case 'membership':
              if (isset($_GET['keywords'])){
                  $errorMsg = sprintf(sysLanguage::get('TEXT_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default'), itw_app_link(null,'account','login'));
              }else{
                  $errorMsg = sprintf(sysLanguage::get('TEXT_INDEX_NOT_RENTAL_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default'), itw_app_link(null,'account','login'));
              }
          break;
          case 'inactive':
              $errorMsg = sprintf(sysLanguage::get('TEXT_NOT_ACTIVE_CUSTOMER'),itw_app_link('checkoutType=rental','checkout','default'));
          break;
          case 'duplicate':
              $errorMsg = sysLanguage::get('TEXT_DUPLICATE_RENTAL_ITEM');
          break;
      }

      echo '<table border="0" width="100%" cellspacing="0" cellpadding="2">
             <tr>
              <td colspan="2">' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td>
             </tr>
             <tr>
              <td class="messageStackError">' . $errorMsg . '</td>
             </tr>
             <tr>
              <td>' . tep_draw_separator('pixel_trans.gif', '100%', '10') . '</td>
             </tr>
            </table>';
  }


  //Code added by Deepali ends

  $list_box_contents = array();
  if ($listing_split->number_of_rows > 0){
      $list_box_contents = $productListing->buildListingBox($listing_split->sql_query);
  } else {
      $list_box_contents = array();
      $list_box_contents[0] = array('params' => 'class="productListing-odd"');
      $list_box_contents[0][] = array(
          'params' => 'class="productListing-data"',
          'text' => sysLanguage::get('TEXT_NO_PRODUCTS')
      );
  }
  new productListingBox($list_box_contents);
?>
</div>
<?php
  if ( ($listing_split->number_of_rows > 0) && ((sysConfig::get('PREV_NEXT_BAR_LOCATION') == '2') || (sysConfig::get('PREV_NEXT_BAR_LOCATION') == '3')) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count(sysConfig::get('TEXT_DISPLAY_NUMBER_OF_PRODUCTS')); ?></td>
    <td class="smallText" align="right"><?php echo sysLanguage::get('TEXT_RESULT_PAGE') . ' ' . $listing_split->display_links($products_count, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?>
    <?php echo tep_draw_hidden_field('new_mem');?>
    </td>
  </tr>
</table>
<?php
  }
?>