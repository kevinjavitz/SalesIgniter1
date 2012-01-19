<?php
  $productListing = new productListing();
  $column_list = $productListing->getColumnList();
  $select_column_list = $productListing->getSelectColumns();

// show the products of a specified manufacturer
  if ($current_category_id){
      $listing_sql = "select distinct " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price,p.products_type, p.products_ptype, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_only_rental, coalesce(rt.top, 0) as top, p.products_price_daily, p.products_price_monthly, p.products_price_weekly from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p         
        INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = p.products_id
        INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id
        LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id
        LEFT JOIN " . TABLE_SPECIALS . " s ON p.products_id = s.products_id
        LEFT JOIN ".TABLE_RENTAL_TOP." rt ON rt.products_id = p.products_id 
       where p.products_status = '1' and pd.language_id = '" . (int)Session::get('languages_id') . "' and p2c.categories_id = '" . (int)$current_category_id . "' order by rt.top desc";
  } else {
// We show them all
      $listing_sql = "select distinct " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price,p.products_type, p.products_ptype, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price, p.products_only_rental, coalesce(rt.top, 0) as top, p.products_price_daily, p.products_price_monthly, p.products_price_weekly from " . TABLE_PRODUCTS . " p 
        INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON pd.products_id = p.products_id
        INNER JOIN " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c ON p2c.products_id = p.products_id
        LEFT JOIN " . TABLE_MANUFACTURERS . " m ON p.manufacturers_id = m.manufacturers_id
        LEFT JOIN " . TABLE_SPECIALS . " s ON p.products_id = s.products_id
        LEFT JOIN ".TABLE_RENTAL_TOP." rt ON rt.products_id = p.products_id
     where p.products_status = '1' and pd.language_id = '" . (int)Session::get('languages_id') . "' order by rt.top desc";
  }
  $productListing->setQuery($listing_sql);
  
  ob_start();
        $products_count = MAX_DISPLAY_SEARCH_RESULTS;
        include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING);
  $pageContents = ob_get_contents();
  ob_end_clean();
  
	$pageContent->set('pageTitle', sysLanguage::get('HEADING_TITLE'));
	$pageContent->set('pageForm', array(
		'name' => 'frm',
		'action' => itw_app_link(null, 'index', 'default'),
		'method' => 'post'
	));
	$pageContent->set('pageContent', $pageContents);
