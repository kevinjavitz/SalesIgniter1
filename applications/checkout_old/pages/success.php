<div align="center" class="pageHeading"><?php
	if (isset($message) && $message != ""){
		echo $message;
	}elseif (Session::exists('payment_rental') === true){
		echo '<h3>'.sysLanguage::get('HEADING_TITLE').'</h3>';
	}else{
		echo sysLanguage::get('HEADING_TITLE_SUCCESS');
	}
?></div>
<?php
	if (!isset($message) && Session::exists('payment_rental') === false){
		echo '<h3>' . sysLanguage::get('TEXT_THANKS_FOR_SHOPPING_SUCCESS') . '</h3>';
	}else{
		echo sysLanguage::get('TEXT_THANKS_FOR_SHOPPING_SUCCESS');
	}

	// ###### Added CCGV Contribution #########
  $gv_query=tep_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id='".$userAccount->getCustomerId()."'");
  if ($gv_result=tep_db_fetch_array($gv_query)) {
    if ($gv_result['amount'] > 0) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td align="center" class="main"><?php echo sysLanguage::get('GV_HAS_VOUCHERA'); echo itw_app_link(null, 'gv_send', 'default'); echo GV_HAS_VOUCHERB; ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
}}
	// ###### Added CCGV Contribution #########

	$QlastOrder = Doctrine_Query::create()
	->select('orders_id')
	->from('Orders')
	->where('customers_id = ?', (int)$userAccount->getCustomerId())
	->orderBy('orders_id desc')
	->limit(1)
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	
	$Qdownloads = Doctrine_Query::create()
	->select('count(orders_id) as total')
	->from('Orders o')
	->leftJoin('o.OrdersProducts op')
	->leftJoin('op.OrdersProductsDownload opd')
	->where('o.customers_id = ?', (int)$userAccount->getCustomerId())
	->andWhere('o.orders_id = ?', $QlastOrder[0]['orders_id'])
	->andWhere('opd.orders_products_filename != ?', '')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	if ($Qdownloads && $Qdownloads[0]['total'] > 0){
		echo '<br /><b><a href="' . itw_app_link(null, 'account', 'downloads.php') . '">' . sysLanguage::get('TEXT_ORDER_HAS_DOWNLOADS') . '</a></b>';
	}
    echo '<br />' . htmlBase::newElement('button')->usePreset('continue')->setHref(itw_app_link(null, 'index', 'default'))->draw();
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
 <tr>
  <td width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
   <tr>
    <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
    <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
   </tr>
  </table></td>
  <td width="50%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
   <tr>
    <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
    <td width="50%"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
   </tr>
  </table></td>
 </tr>
 <tr>
  <td align="center" width="50%" class="checkoutBarFrom"><?php echo sysLanguage::get('CHECKOUT_BAR_CONFIRMATION'); ?></td>
  <td align="center" width="50%" class="checkoutBarCurrent"><?php echo sysLanguage::get('CHECKOUT_BAR_FINISHED'); ?></td>
 </tr>
</table>