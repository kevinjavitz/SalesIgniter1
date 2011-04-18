<?php
	ob_start();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
<?php
  $orders_total = tep_count_customer_orders();

  if ($orders_total > 0) {
  	$Qorders = Doctrine_Query::create()
  	->from('Orders o')
  	->leftJoin('o.OrdersProducts op')
  	->leftJoin('o.OrdersAddresses oa')
  	->leftJoin('o.OrdersTotal ot')
  	->leftJoin('o.OrdersStatus os')
  	->leftJoin('os.OrdersStatusDescription osd')
  	->where('o.customers_id = ?', $userAccount->getCustomerId())
  	->andWhereIn('ot.module_type', array('ot_total', 'total'))
  	->andWhere('osd.language_id = ?', Session::get('languages_id'))
  	->andWhere('oa.address_type = ?', 'billing')
  	->orderBy('orders_id DESC')
  	->limit(MAX_DISPLAY_ORDER_HISTORY)
  	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    foreach($Qorders as $Order){
?>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td class="main"><?php echo '<b>' . sysLanguage::get('TEXT_ORDER_NUMBER') . '</b> ' . $Order['orders_id']; ?></td>
              <td class="main" align="right"><?php echo '<b>' . sysLanguage::get('TEXT_ORDER_STATUS') . '</b> ' . $Order['OrdersStatus']['OrdersStatusDescription'][0]['orders_status_name']; ?></td>
            </tr>
          </table>
          <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
            <tr class="infoBoxContents">
              <td><table border="0" width="100%" cellspacing="2" cellpadding="4">
                <tr>
                  <td class="main" width="50%" valign="top"><?php echo '<b>' . sysLanguage::get('TEXT_ORDER_DATE') . '</b> ' . tep_date_long($Order['date_purchased']) . '<br><b>' . sysLanguage::get('TEXT_ORDER_BILLED_TO') . '</b> ' . $Order['OrdersAddresses'][0]['entry_name']; ?></td>
                  <td class="main" width="30%" valign="top"><?php echo '<b>' . sysLanguage::get('TEXT_ORDER_PRODUCTS') . '</b> ' . sizeof($Order['OrdersProducts']) . '<br><b>' . sysLanguage::get('TEXT_ORDER_COST') . '</b> ' . strip_tags($Order['OrdersTotal'][0]['text']); ?></td>
                  <td class="main" width="20%" style="font-size:.75em;"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_VIEW'))->setHref(itw_app_link((isset($_GET['page']) ? 'page=' . $_GET['page'] . '&' : '') . 'order_id=' . $Order['orders_id'], 'account', 'history_info', 'SSL'))->draw(); ?></td>
                </tr>
              </table></td>
            </tr>
          </table>
          <table border="0" width="100%" cellspacing="0" cellpadding="2">
            <tr>
              <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
            </tr>
          </table>
<?php
    }
  } else {
?>
          <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
            <tr class="infoBoxContents">
              <td><table border="0" width="100%" cellspacing="2" cellpadding="4">
                <tr>
                  <td class="main"><?php echo sysLanguage::get('TEXT_NO_PURCHASES'); ?></td>
                </tr>
              </table></td>
            </tr>
          </table>
<?php
  }
?>
        </td>
      </tr>
<?php
  /*if ($orders_total > 0) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="smallText" valign="top"><?php echo $Qorders->showPageCount(); ?></td>
            <td class="smallText" align="right"><?php echo $Qorders->showPageLinks(tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
          </tr>
        </table></td>
      </tr>
<?php
  }*/
?>
    </table>
<?php
	$pageContents = ob_get_contents();
	ob_end_clean();
	
	$pageTitle = sysLanguage::get('HEADING_TITLE_HISTORY');
	
	$pageButtons = htmlBase::newElement('button')
	->usePreset('back')
	->setHref(itw_app_link(null, 'account', 'default', 'SSL'))
	->draw();
	
	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
