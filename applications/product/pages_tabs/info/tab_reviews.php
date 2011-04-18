<div id="tabReviews"><?php
         $reviews_query = tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . (int)$_GET['products_id'] . "'");
         $reviews = tep_db_fetch_array($reviews_query);
         if ($reviews['count'] > 0) {
             echo sysLanguage::get('TEXT_CURRENT_REVIEWS') . ' ' . $reviews['count'];
         }
         
         echo htmlBase::newElement('button')->setText(sysLanguage::get('IMAGE_BUTTON_REVIEWS'))->setHref(itw_app_link(tep_get_all_get_params(array('appPage')), 'product', 'reviews'))->draw();
?></div>