<?php
  if ($userAccount->isLoggedIn() === false && (sysConfig::get('ALLOW_GUEST_TO_TELL_A_FRIEND') == 'false')) {
    $navigation->set_snapshot();
    tep_redirect(itw_app_link(null, 'account', 'login', 'SSL'));
  }

  $valid_product = false;
  if (isset($_GET['products_id'])) {
    $product_info_query = tep_db_query("select pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$_GET['products_id'] . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)Session::get('languages_id') . "'");
    if (tep_db_num_rows($product_info_query)) {
      $valid_product = true;

      $product_info = tep_db_fetch_array($product_info_query);
    }
  }

  if ($valid_product == false) {
      $error = true;
      $messageStack->add('pageStack', 'Only products can be sent to a friend', 'error');
  }

  if (isset($_GET['action']) && ($_GET['action'] == 'process')) {
    $error = false;

    $to_email_address = tep_db_prepare_input($_POST['to_email_address']);
    $to_name = tep_db_prepare_input($_POST['to_name']);
    $from_email_address = tep_db_prepare_input($_POST['from_email_address']);
    $from_name = tep_db_prepare_input($_POST['from_name']);
    $message = tep_db_prepare_input($_POST['message']);

    if (empty($from_name)) {
      $error = true;

      $messageStack->add('pageStack', sysLanguage::get('ERROR_FROM_NAME'), 'error');
    }

    if (!tep_validate_email($from_email_address)) {
      $error = true;

      $messageStack->add('pageStack', sysLanguage::get('ERROR_FROM_ADDRESS'), 'error');
    }

    if (empty($to_name)) {
      $error = true;

      $messageStack->add('pageStack', sysLanguage::get('ERROR_TO_NAME'), 'error');
    }

    if (!tep_validate_email($to_email_address)) {
      $error = true;

      $messageStack->add('pageStack', sysLanguage::get('ERROR_TO_ADDRESS'), 'error');
    }

    if ($error == false) {
        $emailEvent = new emailEvent('tell_a_friend');
		$emailEvent->setVars(array(
			'fromName' => $from_name,
		    'toName' => $to_name,
		    'productsName' => $product_info['products_name'],
		    'productsLink' => itw_app_link('products_id=' . $_GET['products_id'], 'product', 'info', 'NONSSL'),
		    'catalogLink' => sysConfig::get('HTTP_SERVER') . sysConfig::get('DIR_WS_CATALOG')
		));

        $emailEvent->sendEmail(array(
            'from_email' => $from_email_address,
            'from_name' => $from_name,
            'email' => $to_email_address,
            'name' => $to_name
        ));

      $messageStack->add_session('pageStack', sprintf(sysLanguage::get('TEXT_EMAIL_SUCCESSFUL_SENT'), $product_info['products_name'], tep_output_string_protected($to_name)), 'success');

      tep_redirect(itw_app_link('products_id=' . $_GET['products_id'], 'product', 'info'));
    }
  } elseif ($userAccount->isLoggedIn() === true) {
    $account_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$userAccount->getCustomerId() . "'");
    $account = tep_db_fetch_array($account_query);

    $from_name = $account['customers_firstname'] . ' ' . $account['customers_lastname'];
    $from_email_address = $account['customers_email_address'];
  }  

?>
    <?php
      // Modify form processing depending on whether product or article
      if ($_GET['products_id']) {
        echo tep_draw_form('email_friend', itw_app_link('action=process&products_id=' . $_GET['products_id'], 'tell_a_friend', 'default'));
        }
    ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading">
            <?php
              // Modify heading depending on whether product or article
             /* if ($_GET['products_id']) {
                 $title = $product_info['products_name'];
                 }
              echo sprintf(sysLanguage::get('HEADING_TITLE'), $title);*/
            ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo sysLanguage::get('FORM_TITLE_CUSTOMER_DETAILS'); ?></b></td>
                <td class="inputRequirement" align="right"><?php echo sysLanguage::get('FORM_REQUIRED_INFORMATION'); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
              <tr class="infoBoxContents">
                <td><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main"><?php echo sysLanguage::get('FORM_FIELD_CUSTOMER_NAME'); ?></td>
                    <td class="main"><?php echo tep_draw_input_field('from_name'); ?></td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo sysLanguage::get('FORM_FIELD_CUSTOMER_EMAIL'); ?></td>
                    <td class="main"><?php echo tep_draw_input_field('from_email_address'); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><b><?php echo sysLanguage::get('FORM_TITLE_FRIEND_DETAILS'); ?></b></td>
          </tr>
          <tr>
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
              <tr class="infoBoxContents">
                <td><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main"><?php echo sysLanguage::get('FORM_FIELD_FRIEND_NAME'); ?></td>
                    <td class="main"><?php echo tep_draw_input_field('to_name') . '&nbsp;<span class="inputRequirement">' . sysLanguage::get('ENTRY_FIRST_NAME_TEXT') . '</span>'; ?></td>
                  </tr>
                  <tr>
                    <td class="main"><?php echo sysLanguage::get('FORM_FIELD_FRIEND_EMAIL'); ?></td>
                    <td class="main"><?php echo tep_draw_input_field('to_email_address', (isset($_GET['to_email_address']) && !empty($_GET['to_email_address']))?$_GET['to_email_address']:'') . '&nbsp;<span class="inputRequirement">' . sysLanguage::get('ENTRY_EMAIL_ADDRESS_TEXT') . '</span>'; ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><b><?php echo sysLanguage::get('FORM_TITLE_FRIEND_MESSAGE'); ?></b></td>
          </tr>
          <tr>
            <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
              <tr class="infoBoxContents">
                <td><?php echo tep_draw_textarea_field('message', 'soft', 40, 8); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td><?php
                    // Modify back button depending on whether product or article
                    if ($_GET['products_id']) {
                      echo htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link('products_id=' . $_GET['products_id'], 'product', 'info'))->draw();
                    }
                       ?></td>
                <td align="right"><?php echo htmlBase::newElement('button')->usePreset('continue')->setType('submit')->draw(); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></form>