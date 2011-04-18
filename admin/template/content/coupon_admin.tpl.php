<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<script language="JavaScript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script language="javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
</script>
<?php
  switch ($_GET['action']) {
  case 'voucherreport':
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('CUSTOMER_ID'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('CUSTOMER_NAME'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('IP_ADDRESS'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('REDEEM_DATE'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_ACTION'); ?>&nbsp;</td>
              </tr>
<?php
    $cc_query_raw = "select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . (int)$coupon_id . "'";
    $cc_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $cc_query_raw, $cc_query_numrows);
    $cc_query = tep_db_query($cc_query_raw);
    while ($cc_list = tep_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['uid']) || (@$_GET['uid'] == $cc_list['unique_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
      if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link('coupon_admin.php', tep_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cInfo->coupon_id . '&action=voucherreport&uid=' . $cinfo->unique_id) . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link('coupon_admin.php', tep_get_all_get_params(array('cid', 'action', 'uid')) . 'cid=' . $cc_list['coupon_id'] . '&action=voucherreport&uid=' . $cc_list['unique_id']) . '\'">' . "\n";
      }
$customer_query = tep_db_query("select customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " where customers_id = '" . $cc_list['customer_id'] . "'");
$customer = tep_db_fetch_array($customer_query);

?>
                <td class="dataTableContent"><?php echo $cc_list['customer_id']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $customer['customers_firstname'] . ' ' . $customer['customers_lastname']; ?></td>
                <td class="dataTableContent" align="center"><?php echo $cc_list['redeem_ip']; ?></td>
                <td class="dataTableContent" align="center"><?php echo tep_date_short($cc_list['redeem_date']); ?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['unique_id'] == $cInfo->unique_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>



             </table></td>
<?php
    $heading = array();
    $contents = array();
      $coupon_description_query = tep_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . (int)$coupon_id . "' and language_id = '" . Session::get('languages_id') . "'");
      $coupon_desc = tep_db_fetch_array($coupon_description_query);
      $count_customers = tep_db_query("select * from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . (int)$coupon_id . "' and customer_id = '" . $cInfo->customer_id . "'");

      $heading[] = array('text' => '<b>[' . $_GET['cid'] . ']' . sysLanguage::get('COUPON_NAME') . ' ' . $coupon_desc['coupon_name'] . '</b>');
      $contents[] = array('text' => '<b>' . sysLanguage::get('TEXT_REDEMPTIONS') . '</b>');
      $contents[] = array('text' => sysLanguage::get('TEXT_REDEMPTIONS_TOTAL') . ':' . tep_db_num_rows($cc_query));
      $contents[] = array('text' => sysLanguage::get('TEXT_REDEMPTIONS_CUSTOMER') . ':' . tep_db_num_rows($count_customers));
      $contents[] = array('text' => '');
?>
    <td width="25%" valign="top">
<?php
      $box = new box;
      echo $box->infoBox($heading, $contents);
      echo '            </td>' . "\n";
?>
<?php
    break;
  case 'preview_email':
    $coupon_query = tep_db_query("select coupon_code from " .TABLE_COUPONS . " where coupon_id = '" . (int)$coupon_id . "'");
    $coupon_result = tep_db_fetch_array($coupon_query);
    $coupon_name_query = tep_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . (int)$coupon_id . "' and language_id = '" . Session::get('languages_id') . "'");
    $coupon_name = tep_db_fetch_array($coupon_name_query);
    switch ($_POST['customers_email_address']) {
    case '***':
      $mail_sent_to = sysLanguage::get('TEXT_ALL_CUSTOMERS');
      break;
    case '**D':
      $mail_sent_to = sysLanguage::get('TEXT_NEWSLETTER_CUSTOMERS');
      break;
    default:
      $mail_sent_to = $_POST['customers_email_address'];
      break;
    }
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
          <tr><?php echo tep_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=send_email_to_user&cid=' . $_GET['cid']); ?>
            <td><table border="0" width="100%" cellpadding="0" cellspacing="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo sysLanguage::get('TEXT_CUSTOMER'); ?></b><br><?php echo $mail_sent_to; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo sysLanguage::get('TEXT_COUPON'); ?></b><br><?php echo $coupon_name['coupon_name']; ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo sysLanguage::get('TEXT_FROM'); ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['from'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo sysLanguage::get('TEXT_SUBJECT'); ?></b><br><?php echo htmlspecialchars(stripslashes($_POST['subject'])); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="smallText"><b><?php echo sysLanguage::get('TEXT_MESSAGE'); ?></b><br><?php echo nl2br(htmlspecialchars(stripslashes($_POST['message']))); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td>
<?php
/* Re-Post all POST'ed variables */
    reset($_POST);
    while (list($key, $value) = each($_POST)) {
      if (!is_array($_POST[$key])) {
        echo tep_draw_hidden_field($key, htmlspecialchars(stripslashes($value)));
      }
    }
?>
                <table border="0" width="100%" cellpadding="0" cellspacing="2">
                  <tr>
                    <td><?php ?>&nbsp;</td>
                    <td align="right"><?php echo htmlBase::newElement('button')->usePreset('cancel')->setHref(tep_href_link(FILENAME_COUPON_ADMIN))->draw() . ' ' . htmlBase::newElement('button')->usePreset('email')->setType('submit')->draw(); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
    break;
  case 'email':
    $coupon_query = tep_db_query("select coupon_code from " . TABLE_COUPONS . " where coupon_id = '" . (int)$coupon_id . "'");
    $coupon_result = tep_db_fetch_array($coupon_query);
    $coupon_name_query = tep_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . (int)$coupon_id . "' and language_id = '" . Session::get('languages_id') . "'");
    $coupon_name = tep_db_fetch_array($coupon_name_query);
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>

          <tr><?php echo tep_draw_form('mail', FILENAME_COUPON_ADMIN, 'action=preview_email&cid='. $_GET['cid']); ?>
            <td><table border="0" cellpadding="0" cellspacing="2">
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
    $customers = array();
    $customers[] = array('id' => '', 'text' => sysLanguage::get('TEXT_SELECT_CUSTOMER'));
    $customers[] = array('id' => '***', 'text' => sysLanguage::get('TEXT_ALL_CUSTOMERS'));
    $customers[] = array('id' => '**D', 'text' => sysLanguage::get('TEXT_NEWSLETTER_CUSTOMERS'));
    $mail_query = tep_db_query("select customers_email_address, customers_firstname, customers_lastname from " . TABLE_CUSTOMERS . " order by customers_lastname");
    while($customers_values = tep_db_fetch_array($mail_query)) {
      $customers[] = array('id' => $customers_values['customers_email_address'],
                           'text' => $customers_values['customers_lastname'] . ', ' . $customers_values['customers_firstname'] . ' (' . $customers_values['customers_email_address'] . ')');
    }
?>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo sysLanguage::get('TEXT_COUPON'); ?>&nbsp;&nbsp;</td>
                <td><?php echo $coupon_name['coupon_name']; ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo sysLanguage::get('TEXT_CUSTOMER'); ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo sysLanguage::get('TEXT_FROM'); ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
<?php
/*
              <tr>
                <td class="main"><?php echo TEXT_RESTRICT; ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_checkbox_field('customers_restrict', $customers_restrict);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
*/
?>
              <tr>
                <td class="main"><?php echo sysLanguage::get('TEXT_SUBJECT'); ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo sysLanguage::get('TEXT_MESSAGE'); ?>&nbsp;&nbsp;</td>
                <td><?php echo tep_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo htmlBase::newElement('button')->usePreset('email')->setType('submit')->draw(); ?></td>
              </tr>
            </table></td>
          </form></tr>

      </tr>
      </td>
<?php
    break;
  case 'update_preview':

  $coupon_min_order = (($_POST['coupon_min_order'] == round($_POST['coupon_min_order'])) ? number_format($_POST['coupon_min_order'], 2) : number_format($_POST['coupon_min_order'], 2));
  $coupon_amount = (($_POST['coupon_amount'] == round($_POST['coupon_amount'])) ? number_format($_POST['coupon_amount'], 2) : number_format($_POST['coupon_amount'], 2));
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
      <td>
<?php echo tep_draw_form('coupon', 'coupon_admin.php', 'action=update_confirm&oldaction=' . $_GET['oldaction'] . '&cid=' . $_GET['cid']); ?>
      <table border="0" width="100%" cellspacing="0" cellpadding="6">
      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_STATUS'); ?></td>
        <td align="left"><?php echo (($_POST['coupon_status'] == 'Y') ? IMAGE_ICON_STATUS_GREEN : IMAGE_ICON_STATUS_RED); ?></td>
      </tr>
<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_NAME'); ?></td>
        <td align="left"><?php echo $_POST['coupon_name'][$language_id]; ?></td>
      </tr>
<?php
}
?>
<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
            $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_DESC'); ?></td>
        <td align="left"><?php echo $_POST['coupon_desc'][$language_id]; ?></td>
      </tr>
<?php
}
?>
      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_AMOUNT'); ?></td>
        <td align="left"><?php if (!$_POST['coupon_free_ship']) echo $coupon_amount; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_MIN_ORDER'); ?></td>
        <td align="left"><?php echo $coupon_min_order; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_FREE_SHIP'); ?></td>
<?php
    if ($_POST['coupon_free_ship']) {
?>
        <td align="left"><?php echo sysLanguage::get('TEXT_FREE_SHIPPING'); ?></td>
<?php
    } else {
?>
        <td align="left"><?php echo sysLanguage::get('TEXT_NO_FREE_SHIPPING'); ?></td>
<?php
    }
?>
      </tr>
      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_CODE'); ?></td>
<?php
    if ($_POST['coupon_code']) {
      $c_code = $_POST['coupon_code'];
    } else {
      $c_code = $coupon_code;
    }
?>
        <td align="left"><?php echo $coupon_code; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_USES_COUPON'); ?></td>
        <td align="left"><?php echo $_POST['coupon_uses_coupon']; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_USES_USER'); ?></td>
        <td align="left"><?php echo $_POST['coupon_uses_user']; ?></td>
      </tr>

       <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_PRODUCTS'); ?></td>
        <td align="left"><?php echo $_POST['coupon_products']; ?></td>
      </tr>


      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_CATEGORIES'); ?></td>
        <td align="left"><?php echo $_POST['coupon_categories']; ?></td>
      </tr>
      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_STARTDATE'); ?></td>
<?php
    $start_date = date(sysLanguage::getDateFormat(), mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] ));
?>
        <td align="left"><?php echo $start_date; ?></td>
      </tr>

      <tr>
        <td align="left"><?php echo sysLanguage::get('COUPON_FINISHDATE'); ?></td>
<?php
    $finish_date = date(sysLanguage::getDateFormat(), mktime(0, 0, 0, $_POST['coupon_finishdate_month'],$_POST['coupon_finishdate_day'] ,$_POST['coupon_finishdate_year'] ));
    echo date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] ));
?>
        <td align="left"><?php echo $finish_date; ?></td>
      </tr>
<?php
    echo tep_draw_hidden_field('coupon_status', $_POST['coupon_status']);
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
          $language_id = $languages[$i]['id'];
          echo tep_draw_hidden_field('coupon_name[' . $languages[$i]['id'] . ']', $_POST['coupon_name'][$language_id]);
          echo tep_draw_hidden_field('coupon_desc[' . $languages[$i]['id'] . ']', $_POST['coupon_desc'][$language_id]);
       }
    echo tep_draw_hidden_field('coupon_amount', $_POST['coupon_amount']);
    echo tep_draw_hidden_field('coupon_min_order', $_POST['coupon_min_order']);
    echo tep_draw_hidden_field('coupon_free_ship', $_POST['coupon_free_ship']);
    echo tep_draw_hidden_field('coupon_code', $c_code);
    echo tep_draw_hidden_field('coupon_uses_coupon', $_POST['coupon_uses_coupon']);
    echo tep_draw_hidden_field('coupon_uses_user', $_POST['coupon_uses_user']);
    echo tep_draw_hidden_field('coupon_products', $_POST['coupon_products']);
    echo tep_draw_hidden_field('coupon_categories', $_POST['coupon_categories']);
    echo tep_draw_hidden_field('coupon_startdate', date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] )));
    echo tep_draw_hidden_field('coupon_finishdate', date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_finishdate_month'],$_POST['coupon_finishdate_day'] ,$_POST['coupon_finishdate_year'] )));
    echo tep_draw_hidden_field('date_created', ((tep_not_null($_POST['date_created'])) ? date('Y-m-d', strtotime($_POST['date_created'])) : '0'));
?>
     <tr>
        <td align="left"><?php echo htmlBase::newElement('button')->usePreset('save')->setType('submit')->draw(); ?></td>
        <td align="left"><?php echo htmlBase::newElement('button')->usePreset('back')->setType('submit')->setName('back')->draw(); ?></td>
      </td>
      </tr>

      </td></table></form>
      </tr>

      </table></td>
<?php

    break;
  case 'voucheredit':
    $languages = tep_get_languages();
    for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
      $language_id = $languages[$i]['id'];
      $coupon_query = tep_db_query("select coupon_name,coupon_description from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" .  (int)$coupon_id . "' and language_id = '" . $language_id . "'");
      $coupon = tep_db_fetch_array($coupon_query);
      $coupon_name[$language_id] = $coupon['coupon_name'];
      $coupon_desc[$language_id] = $coupon['coupon_description'];
    }
    $coupon_query=tep_db_query("select coupon_active, coupon_code, coupon_amount, coupon_type, coupon_minimum_order, coupon_start_date, coupon_expire_date, date_created, uses_per_coupon, uses_per_user, restrict_to_products, restrict_to_categories from " . TABLE_COUPONS . " where coupon_id = '" . (int)$coupon_id . "'");
    $coupon=tep_db_fetch_array($coupon_query);
    $coupon_amount = (($coupon['coupon_amount'] == round($coupon['coupon_amount'])) ? number_format($coupon['coupon_amount'], 2) : number_format($coupon['coupon_amount'], 2));
    if ($coupon['coupon_type']=='P') {
      // not floating point value, don't display decimal info
      $coupon_amount = (($coupon_amount == round($coupon_amount)) ? number_format($coupon_amount) : number_format($coupon_amount, 2)) . '%';
    }
    if ($coupon['coupon_type']=='S') {
      $coupon_free_ship .= true;
    }
    $coupon_min_order = (($coupon['coupon_minimum_order'] == round($coupon['coupon_minimum_order'])) ? number_format($coupon['coupon_minimum_order'], 2) : number_format($coupon['coupon_minimum_order'], 2));
    $coupon_code = $coupon['coupon_code'];
    $coupon_uses_coupon = $coupon['uses_per_coupon'];
    $coupon_uses_user = $coupon['uses_per_user'];
    $coupon_products = $coupon['restrict_to_products'];
    $coupon_categories = $coupon['restrict_to_categories'];
    $date_created = $coupon['date_created'];
    $coupon_status = $coupon['coupon_active'];
  case 'new':
// molafish: set default if not editing an existing coupon or showing an error
    if ($_GET['action'] == 'new' && !$_GET['oldaction'] == 'new') {
      if (!$coupon_uses_user) {
        $coupon_uses_user=1;
      }
      if (!$date_created) {
        $date_created = '0';
      }
    }
    if (!isset($coupon_status)) $coupon_status = 'Y';
    switch ($coupon_status) {
      case 'N': $in_status = false; $out_status = true; break;
      case 'Y':
      default: $in_status = true; $out_status = false;
    }
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
      <td>
<?php
    echo tep_draw_form('coupon', 'coupon_admin.php', 'action=update&oldaction='. (($_GET['oldaction'] == 'voucheredit') ? $_GET['oldaction'] : $_GET['action']) . '&cid=' . $_GET['cid']);
?>
      <table border="0" width="100%" cellspacing="0" cellpadding="6">

      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_STATUS'); ?></td>
        <td align="left"><?php echo tep_draw_radio_field('coupon_status', 'Y', $in_status) . '&nbsp;' . IMAGE_ICON_STATUS_GREEN . '&nbsp;' . tep_draw_radio_field('coupon_status', 'N', $out_status) . '&nbsp;' . IMAGE_ICON_STATUS_RED; ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_STATUS_HELP'); ?></td>
      </tr>

<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
?>
      <tr>
        <td align="left" class="main"><?php if ($i==0) echo sysLanguage::get('COUPON_NAME'); ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_name[' . $languages[$i]['id'] . ']', $coupon_name[$language_id]) . '&nbsp;' . tep_image(DIR_WS_CATALOG_LANGUAGES . $languages[$i]['directory'] . '/images/' . $languages[$i]['image'], $languages[$i]['name']); ?></td>
        <td align="left" class="main" width="40%"><?php if ($i==0) echo sysLanguage::get('COUPON_NAME_HELP'); ?></td>
      </tr>
<?php
}
?>
<?php
        $languages = tep_get_languages();
        for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
        $language_id = $languages[$i]['id'];
?>

      <tr>
        <td align="left" valign="top" class="main"><?php if ($i==0) echo sysLanguage::get('COUPON_DESC'); ?></td>
        <td align="left" valign="top"><?php echo tep_draw_textarea_field('coupon_desc[' . $languages[$i]['id'] . ']','physical','24','3', $coupon_desc[$language_id]) . '&nbsp;' . $languages[$i]['name']; ?></td>
        <td align="left" valign="top" class="main"><?php if ($i==0) echo sysLanguage::get('COUPON_DESC_HELP'); ?></td>
      </tr>
<?php
}
?>
      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_AMOUNT'); ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_amount', $coupon_amount); ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_AMOUNT_HELP'); ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_MIN_ORDER'); ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_min_order', $coupon_min_order); ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_MIN_ORDER_HELP'); ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_FREE_SHIP'); ?></td>
        <td align="left"><?php echo tep_draw_checkbox_field('coupon_free_ship', $coupon_free_ship); ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_FREE_SHIP_HELP'); ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_CODE'); ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_code', $coupon_code); ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_CODE_HELP'); ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_USES_COUPON'); ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_uses_coupon', $coupon_uses_coupon); ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_USES_COUPON_HELP'); ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_USES_USER'); ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_uses_user', $coupon_uses_user); ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_USES_USER_HELP'); ?></td>
      </tr>
       <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_PRODUCTS'); ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_products', $coupon_products); ?> <A HREF="validproducts.php" TARGET="_blank" ONCLICK="window.open('validproducts.php', 'Valid_Products', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false">View</A></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_PRODUCTS_HELP'); ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_CATEGORIES'); ?></td>
        <td align="left"><?php echo tep_draw_input_field('coupon_categories', $coupon_categories); ?> <A HREF="validcategories.php" TARGET="_blank" ONCLICK="window.open('validcategories.php', 'Valid_Categories', 'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600'); return false">View</A></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_CATEGORIES_HELP'); ?></td>
      </tr>
      <tr>
<?php
// molafish: fixed reset to default of dates when editing an existing coupon or showing an error message
    if ($_GET['action'] == 'new' && !$_POST['coupon_startdate'] && !$_GET['oldaction'] == 'new') {
      $coupon_startdate = split("[-]", date('Y-m-d'));
    } elseif (tep_not_null($_POST['coupon_startdate'])) {
      $coupon_startdate = split("[-]", $_POST['coupon_startdate']);
    } elseif (!$_GET['oldaction'] == 'new') {   // for action=voucheredit
      $coupon_startdate = split("[-]", date('Y-m-d', strtotime($coupon['coupon_start_date'])));
    } else {   // error is being displayed
      $coupon_startdate = split("[-]", date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_startdate_month'],$_POST['coupon_startdate_day'] ,$_POST['coupon_startdate_year'] )));
    }
    if ($_GET['action'] == 'new' && !$_POST['coupon_finishdate'] && !$_GET['oldaction'] == 'new') {
      $coupon_finishdate = split("[-]", date('Y-m-d'));
      $coupon_finishdate[0] = $coupon_finishdate[0] + 1;
    } elseif (tep_not_null($_POST['coupon_finishdate'])) {
      $coupon_finishdate = split("[-]", $_POST['coupon_finishdate']);
    } elseif (!$_GET['oldaction'] == 'new') {   // for action=voucheredit
      $coupon_finishdate = split("[-]", date('Y-m-d', strtotime($coupon['coupon_expire_date'])));
    } else {   // error is being displayed
      $coupon_finishdate = split("[-]", date('Y-m-d', mktime(0, 0, 0, $_POST['coupon_finishdate_month'],$_POST['coupon_finishdate_day'] ,$_POST['coupon_finishdate_year'] )));
    }
?>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_STARTDATE'); ?></td>
        <td align="left"><?php echo tep_draw_date_selector('coupon_startdate', mktime(0,0,0, $coupon_startdate[1], $coupon_startdate[2], $coupon_startdate[0], 0)); ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_STARTDATE_HELP'); ?></td>
      </tr>
      <tr>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_FINISHDATE'); ?></td>
        <td align="left"><?php echo tep_draw_date_selector('coupon_finishdate', mktime(0,0,0, $coupon_finishdate[1], $coupon_finishdate[2], $coupon_finishdate[0], 0)); ?></td>
        <td align="left" class="main"><?php echo sysLanguage::get('COUPON_FINISHDATE_HELP'); ?></td>
      </tr>
<?php
      echo tep_draw_hidden_field('date_created', $date_created);
?>
      <tr>
        <td align="left"><?php echo htmlBase::newElement('button')->usePreset('preview')->setType('submit')->draw(); ?></td>
        <td align="left">&nbsp;&nbsp;<?php echo htmlBase::newElement('button')->usePreset('cancel')->setHref(tep_href_link('coupon_admin.php', ''))->draw(); ?>
      </td>
      </tr>
      </td></table></form>
      </tr>

      </table></td>
<?php
    break;
  default:
?>
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="main"><?php echo tep_draw_form('status', FILENAME_COUPON_ADMIN, '', 'get'); ?>
<?php
    $status_array[] = array('id' => 'Y', 'text' => sysLanguage::get('TEXT_COUPON_ACTIVE'));
    $status_array[] = array('id' => 'N', 'text' => sysLanguage::get('TEXT_COUPON_INACTIVE'));
    $status_array[] = array('id' => '*', 'text' => sysLanguage::get('TEXT_COUPON_ALL'));

    if ($_GET['status']) {
      $status = tep_db_prepare_input($_GET['status']);
    } else {
      $status = 'Y';
    }
    echo sysLanguage::get('HEADING_TITLE_STATUS') . ' ' . tep_draw_pull_down_menu('status', $status_array, $status, 'onChange="this.form.submit();"');
?>
              </form>
           </td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo sysLanguage::get('COUPON_NAME'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('COUPON_AMOUNT'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('COUPON_CODE'); ?></td>
                <td class="dataTableHeadingContent" align="center"><?php echo sysLanguage::get('COUPON_STATUS'); ?></td>
                <td class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_ACTION'); ?>&nbsp;</td>
              </tr>
<?php
    if ($_GET['page'] > 1) $rows = $_GET['page'] * 20 - 20;
    if ($status != '*') {
      $cc_query_raw = "select coupon_active, coupon_id, coupon_code, coupon_amount, coupon_minimum_order, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, date_created,date_modified from " . TABLE_COUPONS ." where coupon_active='" . tep_db_input($status) . "' and coupon_type != 'G'";
    } else {
      $cc_query_raw = "select coupon_active, coupon_id, coupon_code, coupon_amount, coupon_minimum_order, coupon_type, coupon_start_date,coupon_expire_date,uses_per_user,uses_per_coupon,restrict_to_products, restrict_to_categories, date_created,date_modified from " . TABLE_COUPONS . " where coupon_type != 'G'";
    }
    $cc_split = new splitPageResults($_GET['page'], MAX_DISPLAY_SEARCH_RESULTS, $cc_query_raw, $cc_query_numrows);
    $cc_query = tep_db_query($cc_query_raw);
    while ($cc_list = tep_db_fetch_array($cc_query)) {
      $rows++;
      if (strlen($rows) < 2) {
        $rows = '0' . $rows;
      }
      if (((!$_GET['cid']) || (@$_GET['cid'] == $cc_list['coupon_id'])) && (!$cInfo)) {
        $cInfo = new objectInfo($cc_list);
      }
      if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) {
        echo '          <tr class="dataTableRowSelected" onmouseover="this.style.cursor=\'hand\'" onclick="document.location.href=\'' . tep_href_link('coupon_admin.php', tep_get_all_get_params(array('cid', 'action')) . 'cid=' . $cInfo->coupon_id . '&action=edit') . '\'">' . "\n";
      } else {
        echo '          <tr class="dataTableRow" onmouseover="this.className=\'dataTableRowOver\';this.style.cursor=\'hand\'" onmouseout="this.className=\'dataTableRow\'" onclick="document.location.href=\'' . tep_href_link('coupon_admin.php', tep_get_all_get_params(array('cid', 'action')) . 'cid=' . $cc_list['coupon_id']) . '\'">' . "\n";
      }
      $coupon_description_query = tep_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cc_list['coupon_id'] . "' and language_id = '" . Session::get('languages_id') . "'");
      $coupon_desc = tep_db_fetch_array($coupon_description_query);
?>
                <td class="dataTableContent"><?php echo $coupon_desc['coupon_name']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($cc_list['coupon_type'] == 'P') {
        // not floating point value, don't display decimal info
        echo (($cc_list['coupon_amount'] == round($cc_list['coupon_amount'])) ? number_format($cc_list['coupon_amount']) : number_format($cc_list['coupon_amount'], 2)) . '%';
      } elseif ($cc_list['coupon_type'] == 'S') {
        echo sysLanguage::get('TEXT_FREE_SHIPPING');
      } else {
        echo $currencies->format($cc_list['coupon_amount']);
      }
?>
            &nbsp;</td>
                <td class="dataTableContent" align="center"><?php echo $cc_list['coupon_code']; ?></td>
                <td class="dataTableContent" align="center">
<?php
      if ($cc_list['coupon_active'] == 'Y') {
        echo tep_image(DIR_WS_IMAGES . 'icon_status_green.gif', IMAGE_ICON_STATUS_GREEN, 10, 10) . '&nbsp;&nbsp;<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'action=setflag&flag=N&cid=' . $cc_list['coupon_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_red_light.gif', IMAGE_ICON_STATUS_RED_LIGHT, 10, 10) . '</a>';
      } else {
        echo '<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'action=setflag&flag=Y&cid=' . $cc_list['coupon_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_status_green_light.gif', IMAGE_ICON_STATUS_GREEN_LIGHT, 10, 10) . '</a>&nbsp;&nbsp;' . tep_image(DIR_WS_IMAGES . 'icon_status_red.gif', IMAGE_ICON_STATUS_RED, 10, 10);
      }
?></td>
                <td class="dataTableContent" align="right"><?php if ( (is_object($cInfo)) && ($cc_list['coupon_id'] == $cInfo->coupon_id) ) { echo tep_image(DIR_WS_IMAGES . 'icon_arrow_right.gif'); } else { echo '<a href="' . tep_href_link(FILENAME_COUPON_ADMIN, 'page=' . $_GET['page'] . '&cid=' . $cc_list['coupon_id']) . '">' . tep_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>'; } ?>&nbsp;</td>
              </tr>
<?php
    }
?>
          <tr>
            <td colspan="5"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="smallText">&nbsp;<?php echo $cc_split->display_count($cc_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, $_GET['page'], sysLanguage::get('TEXT_DISPLAY_NUMBER_OF_COUPONS')); ?>&nbsp;</td>
                <td align="right" class="smallText">&nbsp;<?php echo $cc_split->display_links($cc_query_numrows, MAX_DISPLAY_SEARCH_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page'], 'status=' . $status); ?>&nbsp;</td>
              </tr>

              <tr>
                <td align="right" colspan="2" class="smallText"><?php echo htmlBase::newElement('button')->usePreset('insert')->setHref(tep_href_link('coupon_admin.php', 'page=' . $_GET['page'] . '&cID=' . $cInfo->coupon_id . '&action=new'))->draw(); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>

<?php

    $heading = array();
    $contents = array();

    switch ($_GET['action']) {
    case 'release':
      break;
    case 'voucherreport':
      $heading[] = array('text' => '<b>' . TEXT_HEADING_COUPON_REPORT . '</b>');
      $contents[] = array('text' => sysLanguage::get('TEXT_NEW_INTRO'));
      break;
    default:
      $heading[] = array('text'=>'['.$cInfo->coupon_id.']  '.$cInfo->coupon_code);
      $amount = $cInfo->coupon_amount;
      if ($cInfo->coupon_type == 'P') {
        // not floating point value, don't display decimal info
        $amount = (($amount == round($amount)) ? number_format($amount) : number_format($amount, 2)) . '%';
      } else {
        $amount = $currencies->format($amount);
      }
      $coupon_min_order = $currencies->format($cInfo->coupon_minimum_order);
      if ($_GET['action'] == 'voucherdelete') {
        $contents[] = array('text'=> sysLanguage::get('TEXT_CONFIRM_DELETE') . '</br></br>' .
                htmlBase::newElement('button')->usePreset('delete')->setHref(tep_href_link('coupon_admin.php','action=confirmdelete&status=' . $status . (($_GET['page'] > 1) ? '&page=' . $_GET['page']: '') . '&cid='.$_GET['cid'],'NONSSL'))->draw() .
                htmlBase::newElement('button')->usePreset('cancel')->setHref(tep_href_link('coupon_admin.php','cid='.$cInfo->coupon_id,'NONSSL'))->draw()
                );
      } else {
        $prod_details = sysLanguage::get('NONE');
        if ($cInfo->restrict_to_products) {
          $prod_details = '<A HREF="listproducts.php?cid=' . $cInfo->coupon_id . '" TARGET="_blank" ONCLICK="window.open(\'listproducts.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</A>';
        }
        $cat_details = sysLanguage::get('NONE');
        if ($cInfo->restrict_to_categories) {
          $cat_details = '<A HREF="listcategories.php?cid=' . $cInfo->coupon_id . '" TARGET="_blank" ONCLICK="window.open(\'listcategories.php?cid=' . $cInfo->coupon_id . '\', \'Valid_Categories\', \'scrollbars=yes,resizable=yes,menubar=yes,width=600,height=600\'); return false">View</A>';
        }
        $coupon_name_query = tep_db_query("select coupon_name from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $cInfo->coupon_id . "' and language_id = '" . Session::get('languages_id') . "'");
        $coupon_name = tep_db_fetch_array($coupon_name_query);
        $contents[] = array('text'=>sysLanguage::get('COUPON_NAME') . '&nbsp;:&nbsp;' . $coupon_name['coupon_name'] . '<br>' .
                     sysLanguage::get('COUPON_AMOUNT') . '&nbsp;:&nbsp;' . $amount . '<br>' .
                     sysLanguage::get('COUPON_MIN_ORDER') . '&nbsp;:&nbsp;' . $coupon_min_order . '<br>' .
                     sysLanguage::get('COUPON_STARTDATE') . '&nbsp;:&nbsp;' . tep_date_short($cInfo->coupon_start_date) . '<br>' .
                     sysLanguage::get('COUPON_FINISHDATE') . '&nbsp;:&nbsp;' . tep_date_short($cInfo->coupon_expire_date) . '<br>' .
                     sysLanguage::get('COUPON_USES_COUPON') . '&nbsp;:&nbsp;' . $cInfo->uses_per_coupon . '<br>' .
                     sysLanguage::get('COUPON_USES_USER') . '&nbsp;:&nbsp;' . $cInfo->uses_per_user . '<br>' .
                     sysLanguage::get('COUPON_PRODUCTS') . '&nbsp;:&nbsp;' . $prod_details . '<br>' .
                     sysLanguage::get('COUPON_CATEGORIES') . '&nbsp;:&nbsp;' . $cat_details . '<br>' .
                     sysLanguage::get('DATE_CREATED') . '&nbsp;:&nbsp;' . tep_date_short($cInfo->date_created) . '<br>' .
                     sysLanguage::get('DATE_MODIFIED') . '&nbsp;:&nbsp;' . tep_date_short($cInfo->date_modified) . '<br><br>' .
                     '<center>'.htmlBase::newElement('button')->usePreset('email')->setHref(tep_href_link('coupon_admin.php','action=email&cid='.$cInfo->coupon_id,'NONSSL'))->draw() .
                     htmlBase::newElement('button')->usePreset('edit')->setHref(tep_href_link('coupon_admin.php','action=voucheredit&cid='.$cInfo->coupon_id,'NONSSL'))->draw() .
                     htmlBase::newElement('button')->usePreset('delete')->setHref(tep_href_link('coupon_admin.php','action=voucherdelete&status=' . $status . (($_GET['page'] > 1) ? '&page=' . $_GET['page']: '') . '&cid='.$cInfo->coupon_id,'NONSSL'))->draw() .
                     '<br>'.htmlBase::newElement('button')->setText(sysLanguage::get('COUPON_BUTTON_VOUCHER_REPORT'))->setHref(tep_href_link('coupon_admin.php','action=voucherreport&cid='.$cInfo->coupon_id,'NONSSL'))->draw().'</center>');
        }
        break;
      }
?>
    <td width="25%" valign="top">
<?php
      $box = new box;
      echo $box->infoBox($heading, $contents);
    echo '            </td>' . "\n";
    }
?>
      </tr>
    </table>