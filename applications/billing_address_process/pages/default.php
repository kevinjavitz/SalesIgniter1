<?php
	ob_start();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<?php
  if (isset($_GET['delete'])) {
?>
      <tr>
        <td class="main"><b><?php echo sysLanguage::get('DELETE_ADDRESS_TITLE'); ?></b></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="50%" valign="top"><?php echo sysLanguage::get('DELETE_ADDRESS_DESCRIPTION'); ?></td>
                <td align="right" width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main" align="center" valign="top"><b><?php echo sysLanguage::get('SELECTED_ADDRESS'); ?></b><br><?php echo tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" valign="top"><?php echo tep_address_label($userAccount->getCustomerId(), $_GET['delete'], true, ' ', '<br>'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw();
	
	$pageButtons .= htmlBase::newElement('button')
	->usePreset('delete')
	->setHref(itw_app_link('delete=' . $_GET['delete'] . '&action=deleteconfirm', 'account', 'billing_address_process', 'SSL'))
	->draw();
  } else {
?>
      <tr>
        <td><?php include('address_book_details.php'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($_GET['edit'] == $membership->getRentalAddressId() && basename($_SERVER['PHP_SELF']) == 'billing_address_process.php'){
?>      
      <tr>
        <td><?php include('credit_card_details.php'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
    if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
		$pageButtons = htmlBase::newElement('button')
		->usePreset('back')
		->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
		->draw();
	
		$pageButtons .= tep_draw_hidden_field('action', 'update') . htmlBase::newElement('button')
		->setText(sysLanguage::get('TEXT_BUTTON_UPDATE'))
		->setType('submit')
		->draw();
    } else {
      if (sizeof($navigation->snapshot) > 0) {
        $back_link = tep_href_link($navigation->snapshot['page'], tep_array_to_string($navigation->snapshot['get'], array(tep_session_name())), $navigation->snapshot['mode']);
      } else {
        $back_link = itw_app_link(null, 'account', 'default', 'SSL');
      }
		$pageButtons = htmlBase::newElement('button')
		->usePreset('back')
		->setHref($back_link)
		->draw();
	
		$pageButtons .= tep_draw_hidden_field('action', 'process') . htmlBase::newElement('button')
		->usePreset('continue')
		->setType('submit')
		->draw();
    }
  }
?>
    </table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	if (isset($_GET['edit'])){
		$pageTitle = sysLanguage::get('HEADING_TITLE_MODIFY_ENTRY');
	}elseif (isset($_GET['delete'])){
		$pageTitle = sysLanguage::get('HEADING_TITLE_DELETE_ENTRY');
	}else{
		$pageTitle = sysLanguage::get('HEADING_TITLE_ADD_ENTRY');
	}
	
	if (!isset($_GET['delete'])){
		$pageContent->set('pageForm', array(
			'name' => 'addressbook',
			'action' => itw_app_link((isset($_GET['edit']) ? 'edit=' . $_GET['edit'] : ''), 'billing_address_process', 'account', 'SSL'),
			'method' => 'post'
		));
	}
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
