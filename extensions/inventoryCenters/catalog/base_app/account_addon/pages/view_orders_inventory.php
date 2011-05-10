
<?php
	$QcustomerCenter = Doctrine_Query::create()
	->select('inventory_center_id')
	->from('ProductsInventoryCenters')
	->where('inventory_center_customer = ?', (int)$userAccount->getCustomerId())
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);


$Qorders = Doctrine_Query::create()
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsReservation ops')
	->leftJoin('o.OrdersTotal ot')
	->leftJoin('o.OrdersStatus s')
	->leftJoin('s.OrdersStatusDescription sd')
	->where('ops.inventory_center_pickup = ?', $QcustomerCenter[0]['inventory_center_id'])
	->andWhereIn('ot.module_type', array('total', 'ot_total'))
	->andWhere('sd.language_id = ?', Session::get('languages_id'))
	->execute(array(),Doctrine_Core::HYDRATE_ARRAY);

	//echo "<pre>". print_r($Qorders);
	ob_start();
	if ($Qorders){
		foreach($Qorders as $order){
			$orderId = $order['orders_id'];
?>
<div class="main" style="position:relative;">
 <b><?php echo sysLanguage::get('TEXT_ORDER_NUMBER');?></b> <?php echo $orderId;?>
 <div class="main" style="position:absolute;right:.3em;top:-.2em">
  <b><?php echo sysLanguage::get('TEXT_ORDER_STATUS');?></b> <?php echo $order['OrdersStatus']['OrdersStatusDescription'][0]['orders_status_name'];?>
 </div>
</div>
<div class="ui-widget ui-widget-content ui-corner-all">
 <table border="0" width="100%" cellspacing="2" cellpadding="4">
  <tr>
   <td class="main" width="50%" valign="top"><?php echo '<b>' . sysLanguage::get('TEXT_ORDER_DATE') . '</b> ' . tep_date_long($order['date_purchased']); ?></td>
   <td class="main" width="30%" valign="top"><?php echo '<b>' . sysLanguage::get('TEXT_ORDER_PRODUCTS') . '</b> ' . sizeof($order['OrdersProducts']) . '<br><b>' . sysLanguage::get('TEXT_ORDER_COST') . '</b> ' . strip_tags($order['OrdersTotal'][0]['text']); ?></td>
	<?php
	if(strpos(strtolower($order['OrdersStatus']['OrdersStatusDescription'][0]['orders_status_name']),'aiting') !== false){
    ?>
   <td class="main" width="20%" style="font-size:.75em;"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_CHANGE_STATUS_APPROVED'))->setHref(itw_app_link('appExt=inventoryCenters&action=order_action&type=approve&order_id=' . $orderId,'account_addon','default'))->draw(); ?></td>
   <td class="main" width="20%" style="font-size:.75em;"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_CHANGE_STATUS_CANCELLED'))->setHref(itw_app_link('appExt=inventoryCenters&action=order_action&type=cancel&order_id=' . $orderId,'account_addon','default'))->draw(); ?></td>
   <?php
    }
    ?>
	  <td class="main" width="20%" style="font-size:.75em;"><?php echo htmlBase::newElement('button')->setText(sysLanguage::get('TEXT_BUTTON_VIEW'))->setHref(itw_app_link('appExt=inventoryCenters&order_id=' . $orderId, 'account_addon', 'history_inventory_info', 'SSL'))->draw(); ?></td>
  </tr>
 </table>
</div>
<br />
<?php
	}
} else {
?>
<div class="ui-widget ui-widget-content ui-corner-all"><?php echo sysLanguage::get('TEXT_NO_RECEIVED_ORDERS');?></div>
<?php
}
?>
<div class="ui-widget ui-widget-content ui-corner-all pageButtonBar"><?php
 echo htmlBase::newElement('button')->usePreset('back')->setHref(itw_app_link(null, 'account', 'default', 'SSL'))->draw();
 ?></div>

<?php
    $pageContents = ob_get_contents();
	ob_end_clean();
	$contentHeading = 'My Received Orders';
	$pageTitle = stripslashes($contentHeading);

	$pageButtons = htmlBase::newElement('button')
	->usePreset('continue')
	->setHref(itw_app_link(null, 'index', 'default'))
	->draw();

	$pageContent->set('pageTitle', $pageTitle);
	$pageContent->set('pageContent', $pageContents);
	$pageContent->set('pageButtons', $pageButtons);
?>