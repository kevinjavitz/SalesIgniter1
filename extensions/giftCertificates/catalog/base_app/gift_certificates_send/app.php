<?php
    if ($userAccount->isLoggedIn() === false) {
        $navigation->set_snapshot();
        tep_redirect(itw_app_link(null, 'account', 'login', 'SSL'));
    }
    $appContent = $App->getAppContentFile();
    $purchaseTypeNames = $typeNames;
    $purchaseTypeNames['global'] = 'All Products';

// check for a voucher number in the url
  if (isset($_GET['gc_no'])) {
      $error = true;
      $voucher_number=tep_db_prepare_input($_GET['gc_no']);
      $gv_query = tep_db_query("select c.coupon_id, c.coupon_amount from " . TABLE_COUPONS . " c, " . TABLE_COUPON_EMAIL_TRACK . " et where coupon_code = '" . addslashes($voucher_number) . "' and c.coupon_id = et.coupon_id");
      if (tep_db_num_rows($gv_query) >0) {
          $coupon = tep_db_fetch_array($gv_query);
          $redeem_query = tep_db_query("select coupon_id from ". TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $coupon['coupon_id'] . "'");
          if (tep_db_num_rows($redeem_query) == 0 ) {
              // check for required session variables
              $gv_query = tep_db_query("insert into  " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, customer_id, redeem_date, redeem_ip) values ('" . $coupon['coupon_id'] . "', '" . $userAccount->getCustomerId() . "', now(),'" . $REMOTE_ADDR . "')");
              $gv_update = tep_db_query("update " . TABLE_COUPONS . " set coupon_active = 'N' where coupon_id = '" . $coupon['coupon_id'] . "'");
              tep_gv_account_update($userAccount->getCustomerId(), Session::get('gv_id'));

          } else {
              $error = true;
          }
      }
  }

?>