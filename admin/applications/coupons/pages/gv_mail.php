<?php

  if ( ($_GET['action'] == 'send_email_to_user') && ($_POST['customers_email_address'] || $_POST['email_to']) && (!$_POST['back_x']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS);
        $mail_sent_to = sysLanguage::get('TEXT_ALL_CUSTOMERS');
        break;
      case '**D':
        $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_newsletter = '1'");
        $mail_sent_to = sysLanguage::get('TEXT_NEWSLETTER_CUSTOMERS');
        break;
      default:
        $customers_email_address = tep_db_prepare_input($_POST['customers_email_address']);

        $mail_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($customers_email_address) . "'");
        $mail_sent_to = $_POST['customers_email_address'];
        if ($_POST['email_to']) {
          $mail_sent_to = $_POST['email_to'];
        }
        break;
    }

    $from = tep_db_prepare_input($_POST['from']);
    $subject = tep_db_prepare_input($_POST['subject']);
    while ($mail = tep_db_fetch_array($mail_query)) {
      $id1 = create_coupon_code($mail['customers_email_address']);
      $message = $_POST['message'];
      $message .= "\n\n" . sysLanguage::get('TEXT_GV_WORTH')  . $currencies->format($_POST['amount']) . "\n\n";
      $message .= sysLanguage::get('TEXT_TO_REDEEM');
      $message .= sysLanguage::get('TEXT_WHICH_IS') . $id1 . sysLanguage::get('TEXT_IN_CASE') . "\n\n";
      if (SEARCH_ENGINE_FRIENDLY_URLS == 'true') {
//        $message .= sysConfig::get('HTTP_SERVER')  . sysConfig::get('DIR_WS_CATALOG') . 'gv_redeem.php' . '/gv_no,'.$id1 . "\n\n";
        $message .= sysConfig::get('HTTP_SERVER')  . sysConfig::get('DIR_WS_CATALOG') . 'gv_redeem.php' . '/gv_no/'.$id1 . "\n\n";
      } else {
        $message .= sysConfig::get('HTTP_SERVER')  . sysConfig::get('DIR_WS_CATALOG') . 'gv_redeem.php' . '?gv_no='.$id1 . "\n\n";
      }
      $message .= sysLanguage::get('TEXT_OR_VISIT') . sysConfig::get('HTTP_SERVER')  . sysConfig::get('DIR_WS_CATALOG') . sysLanguage::get('TEXT_ENTER_CODE');

      //Let's build a message object using the email class
      $mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
      // add the message to the object
      $mimemessage->add_text($message);
      $mimemessage->build_message();

      $mimemessage->send($mail['customers_firstname'] . ' ' . $mail['customers_lastname'], $mail['customers_email_address'], '', $from, $subject);
      // Now create the coupon main and email entry
      $insert_query = tep_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $id1 . "', 'G', '" . $_POST['amount'] . "', now())");
      $insert_id = tep_db_insert_id($insert_query);
      $insert_query = tep_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id ."', '0', 'Admin', '" . $mail['customers_email_address'] . "', now() )");
    }
    if ($_POST['email_to']) {
      $id1 = create_coupon_code($_POST['email_to']);
      $message = tep_db_prepare_input($_POST['message']);
      $message .= "\n\n" . sysLanguage::get('TEXT_GV_WORTH')  . $currencies->format($_POST['amount']) . "\n\n";
      $message .= sysLanguage::get('TEXT_TO_REDEEM');
      $message .= sysLanguage::get('TEXT_WHICH_IS') . $id1 . sysLanguage::get('TEXT_IN_CASE') . "\n\n";
      $message .= sysConfig::get('HTTP_SERVER')  . sysConfig::get('DIR_WS_CATALOG') . 'gv_redeem.php' . '?gv_no='.$id1 . "\n\n";
      $message .= sysLanguage::get('TEXT_OR_VISIT') . sysConfig::get('HTTP_SERVER')  . sysConfig::get('DIR_WS_CATALOG')  . sysLanguage::get('TEXT_ENTER_CODE');

      //Let's build a message object using the email class
      $mimemessage = new email(array('X-Mailer: osCommerce bulk mailer'));
      // add the message to the object
      $mimemessage->add_text($message);
      $mimemessage->build_message();
      $mimemessage->send('Friend', $_POST['email_to'], '', $from, $subject);
      // Now create the coupon email entry
      $insert_query = tep_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $id1 . "', 'G', '" . $_POST['amount'] . "', now())");
      $insert_id = tep_db_insert_id($insert_query);
      $insert_query = tep_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id ."', '0', 'Admin', '" . $_POST['email_to'] . "', now() )");
    }
    tep_redirect(itw_app_link('mail_sent_to=' . urlencode($mail_sent_to),'coupons','gv_mail'));
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['customers_email_address']) && (!$_POST['email_to']) ) {
    $messageStack->add(sysLanguage::get('ERROR_NO_CUSTOMER_SELECTED'), 'error');
  }

  if ( ($_GET['action'] == 'preview') && (!$_POST['amount']) ) {
    $messageStack->add(sysLanguage::get('ERROR_NO_AMOUNT_SELECTED'), 'error');
  }

  if ($_GET['mail_sent_to']) {
    $messageStack->add(sprintf(sysLanguage::get('NOTICE_EMAIL_SENT_TO'), $_GET['mail_sent_to']), 'notice');
  }

?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo sysLanguage::get('HEADING_TITLE'); ?></td>
            <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if ( ($_GET['action'] == 'preview') && ($_POST['customers_email_address'] || $_POST['email_to']) ) {
    switch ($_POST['customers_email_address']) {
      case '***':
        $mail_sent_to = sysLanguage::get('TEXT_ALL_CUSTOMERS');
        break;
      case '**D':
        $mail_sent_to = sysLanguage::get('TEXT_NEWSLETTER_CUSTOMERS');
        break;
      default:
        $mail_sent_to = $_POST['customers_email_address'];
        if ($_POST['email_to']) {
          $mail_sent_to = $_POST['email_to'];
        }
        break;
    }
?>
          <tr><?php echo '<form name="mail" action="'.itw_app_link('action=send_email_to_user','coupons','gv_mail').'" method="post">'; ?>
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
                <td class="smallText"><b><?php echo sysLanguage::get('TEXT_AMOUNT'); ?></b><br><?php echo nl2br(htmlspecialchars(stripslashes($_POST['amount']))); ?></td>
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
                    <td><?php echo htmlBase::newElement('button')->setType('submit')->setName('back_x')->setText('Back')->draw() ?></td>
                    <td align="right"><?php echo htmlBase::newElement('button')->setHref(itw_app_link(null,'coupons','gv_mail'))->setText('Cancel')->draw() . htmlBase::newElement('button')->setType('submit')->setText('Send Mail')->draw() ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  } else {
?>
          <tr><?php echo '<form name="mail" action="'.itw_app_link('action=preview','coupons','gv_mail').'" method="post">'; ?>
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
                <td class="main"><?php echo sysLanguage::get('TEXT_CUSTOMER'); ?></td>
                <td><?php echo tep_draw_pull_down_menu('customers_email_address', $customers, $_GET['customer']);?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
               <tr>
                <td class="main"><?php echo sysLanguage::get('TEXT_TO'); ?></td>
                <td><?php echo tep_draw_input_field('email_to'); ?><?php echo '&nbsp;&nbsp;' . sysLanguage::get('TEXT_SINGLE_EMAIL'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
             <tr>
                <td class="main"><?php echo sysLanguage::get('TEXT_FROM'); ?></td>
                <td><?php echo tep_draw_input_field('from', EMAIL_FROM); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo sysLanguage::get('TEXT_SUBJECT'); ?></td>
                <td><?php echo tep_draw_input_field('subject'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo sysLanguage::get('TEXT_AMOUNT'); ?></td>
                <td><?php echo tep_draw_input_field('amount'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td valign="top" class="main"><?php echo sysLanguage::get('TEXT_MESSAGE'); ?></td>
                <td><?php echo tep_draw_textarea_field('message', 'soft', '60', '15'); ?></td>
              </tr>
              <tr>
                <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
              </tr>
              <tr>
                <td colspan="2" align="right"><?php echo htmlBase::newElement('button')->setType('submit')->setText('Send Mail')->draw(); ?></td>
              </tr>
            </table></td>
          </form></tr>
<?php
  }
?>
<!-- body_text_eof //-->
        </table></td>
      </tr>
    </table>