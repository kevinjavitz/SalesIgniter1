<?php
/*
  $Id: product_listing.php,v 1.44 2003/06/09 22:49:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  $listing_split = new splitPageResults($featuredProductListing->getQuery(), RENTAL_SELECT_FEATURED_TITLE, 'p.products_id');

  $list_box_contents = array();
  $list_box_contents_h = array();
  $ids=array();
  
  if ($listing_split->number_of_rows > 0) {
      $list_box_contents = $featuredProductListing->buildListingBox($listing_split->sql_query);
      $list_box_contents_h = $list_box_contents[0];
      unset($list_box_contents[0]);
      $ids = $featuredProductListing->getListedProductIDs();
  } 
  
  $featuredProductListing2 = new productListing();
  $featuredProductListing2->disableSorting();
  $select_column_list = $featuredProductListing2->getSelectColumns();
  
  $ids = implode(',',$ids);
  if ($ids == '') $ids=0;
  // We show all featured products
    $listing_sql2 = "select distinct p.products_id, coalesce(rt.top, 0) as top from " . TABLE_PRODUCTS . " p         
        INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = p.products_id
        INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id
        LEFT JOIN " . TABLE_PRODUCTS_TO_BOX . " ptb on p.products_id=ptb.products_id
        LEFT JOIN " . TABLE_SPECIALS . " s ON p.products_id = s.products_id
        LEFT JOIN ".TABLE_RENTAL_TOP." rt ON rt.products_id = p.products_id 
       where p.products_id not in (".$ids.") and ptb.products_id IS NULL and p.products_status = '1' and p.products_featured = 1 and pd.language_id = '" . (int)Session::get('languages_id') . "' and p2c.categories_id in ('" . (int)$current_category_id . "') order by rt.top desc";
  $featuredProductListing2->setQuery($listing_sql2);
    
  $listing_split2 = new splitPageResults($featuredProductListing2->getQuery(), (RENTAL_SELECT_FEATURED_TITLE-$listing_split->number_of_rows), 'p.products_id');      
    
  if ($listing_split2->number_of_rows > 0) {
      if ($listing_split->number_of_rows < RENTAL_SELECT_FEATURED_TITLE) {     
          $list_box_contents2 = $featuredProductListing2->buildListingBox($listing_split2->sql_query);
          if (!isset($list_box_contents_h)){
              $list_box_contents_h = $list_box_contents2[0];
          }
          unset($list_box_contents2[0]);
          $list_box_contents = array_merge($list_box_contents, $list_box_contents2);
      }
  }
  
  if ((!$listing_split->number_of_rows)&& (!$listing_split2->number_of_rows)){
      $list_box_contents = array();
      $list_box_contents[0] = array('params' => 'class="productListing-odd"');
      $list_box_contents[0][] = array(
          'params' => 'class="productListing-data"',
          'text' => sysLanguage::get('TEXT_NO_PRODUCTS')
      );
  }

  if (isset($list_box_contents_h)){
      srand((float)microtime()*1000000); 
      shuffle($list_box_contents);
      $list_box_contents = array_pad($list_box_contents, -(sizeof($list_box_contents)+1), $list_box_contents_h);
  }
  new productListingBox($list_box_contents);
?>