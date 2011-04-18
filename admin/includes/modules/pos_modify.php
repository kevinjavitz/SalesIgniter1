<?php
  function dialogBlock($id, $headerText, $defaultText = false, $addressBox = false){
    return '<div class="ui-inline-dialog-titlebar ui-widget-header ui-corner-t-l ui-helper-clearfix main" unselectable="on" style="-moz-user-select: none;">
      <span class="ui-inline-dialog-title main" id="ui-inline-dialog-title-dialog" unselectable="on" style="-moz-user-select: none;">' . $headerText . '</span>
      ' . ($addressBox === true ? '<button id="' . $id . 'Edit" style="float:right;display:none;" type="button" class="ui-state-default ui-corner-all">Edit Address</button>' : '') . '
     </div>
     <div id="' . $id . 'Dialog" class="ui-inline-dialog-content ui-widget-content main" style="width: auto;">
      ' . $defaultText . '
     </div>';
  }
?>
<script type="text/javascript" src="../ext/jQuery/ui/ui.datepicker.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/selectbox/jquery.select_box.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/ajax_manager/jquery.ajax_manager.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/autocomplete/jquery.autocomplete.js"></script>
<script type="text/javascript" src="includes/javascript/pointOfSale/pointOfSale.js"></script>
<script type="text/javascript" src="includes/javascript/pointOfSale/showProductSelectWindow.js"></script>
<script type="text/javascript" src="includes/javascript/pointOfSale/showCustomerSelectWindow.js"></script>
<script type="text/javascript" src="includes/javascript/pointOfSale/showNewAddressWindow.js"></script>
<script type="text/javascript" src="includes/javascript/pointOfSale/showEditAddressWindow.js"></script>
<script type="text/javascript" src="includes/javascript/pointOfSale/showShippingMethods.js"></script>
<script type="text/javascript" src="includes/javascript/pointOfSale/showOrderTotals.js"></script>
<script type="text/javascript" src="includes/javascript/pointOfSale/showApplyPaymentWindow.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/onetime_rentals/jquery.reservation.js"></script>
<script>  
  $(document).ready(function (){
      $(document).keypress(function (e){
          switch(e.keyCode){
              case 112: // F1 Key
                  alert('F1');
                  e.stopPropagation();
                  return false;
              break;
              case 113: // F2 Key
                  alert('F2');
                  e.stopPropagation();
                  return false;
              break;
              case 114: // F3 Key
                  $('#productButton').click();
                  e.stopPropagation();
                  return false;
              break;
              case 115: // F4 Key
                  alert('F4');
                  e.stopPropagation();
                  return false;
              break;
              case 116: // F5 Key
                  alert('F5');
                  e.stopPropagation();
                  return false;
              break;
              case 121: // F10 Key
                  alert('F10');
                  e.stopPropagation();
                  return false;
              break;
              case 123: // F12 Key
                  alert('F12');
                  e.stopPropagation();
                  return false;
              break;
          }
      });
      
      $(document).pointOfSale({
          urls: {
              order: '<?php echo tep_href_link(FILENAME_ORDERS, '', 'SSL');?>'
          }
      });
      
      $(document).pointOfSale('showShippingMethods', $('#shippingMethodDialog'));
  });
</script>
<style>
 .defaultText {
     text-align:center;
 }
</style>
<table cellpadding="5" cellspacing="0" border="0" width="100%">
 <tr>
  <td width="80%" valign="top"><table cellpadding="0" cellspacing="5" border="0" width="100%">
   <tr>
    <td width="33%" valign="top" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-tl main"><?php echo dialogBlock('billingAddress', 'Billing Address', tep_address_format($order->billing['format_id'], $order->billing, true, '', '<br />'), false);?></td>
    <td width="33%" valign="top" class="ui-inline-dialog ui-widget ui-widget-content main"><?php echo dialogBlock('shippingAddress', 'Shipping Address', tep_address_format($order->delivery['format_id'], $order->delivery, true, '', '<br />'), false);?></td>
    <td width="33%" valign="top" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-tr main"><?php echo dialogBlock('pickupAddress', 'Pickup Address', tep_address_format($order->pickup['format_id'], $order->pickup, true, '', '<br />'), false);?></td>
   </tr>
  </table><br><table cellpadding="0" cellspacing="5" border="0" width="100%">
   <tr>
    <td width="50%" valign="top" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-top"><?php echo dialogBlock('shippingMethod', 'Shipping Method', pointOfSaleHTML::getShippingQuotesTable());?></td>
   </tr>
  </table><br><table cellpadding="0" cellspacing="5" border="0" width="100%">
   <tr>
    <td width="100%" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-all productListing">
     <div class="ui-inline-dialog-titlebar ui-widget-header ui-corner-t-l ui-helper-clearfix main" unselectable="on" style="-moz-user-select: none;">
      <span class="ui-inline-dialog-title main" id="ui-inline-dialog-title-dialog" unselectable="on" style="-moz-user-select: none;"><?php
       echo 'Products';
      ?></span>
     </div>
     <div id="productListingDialog" class="ui-inline-dialog-content ui-widget-content" style="width: auto;"><table cellpadding="3" cellspacing="0" border="0" width="100%" id="productsTable">
      <thead>
       <tr>
        <td class="main" style="text-align:left;"></td>
        <td class="main" style="text-align:left;"><b>Quantity</b></td>
        <td class="main" style="text-align:left;"><b>Products</b></td>
        <td class="main" style="text-align:left;"><b>Barcode</b></td>
        <td class="main" style="text-align:right;"><b>Price Each</b></td>
        <td class="main" style="text-align:right;"><b>Total</b></td>
       </tr>
       <tr>
        <td colspan="7"><hr></td>
       </tr>
      </thead>
      <tbody><?php
       foreach($order->products as $pInfo){
           $productName = $pInfo['name'];
           
           if (isset($pInfo['purchase_type'])){
               $productName .= '<br><nobr><small>&nbsp;<i> - Purchase Type: ' . $pInfo['purchase_type'] . '</i></small></nobr>';
           }
           
           if (isset($pInfo['download_type'])){
               $productName .= '<br><nobr><small>&nbsp;<i> - View Type: ' . ($pInfo['download_type'] == 'stream' ? 'Stream' : 'Download') . '</i></small></nobr>';
           }
           
           if (isset($pInfo['attributes']) && (sizeof($pInfo['attributes']) > 0)) {
               for ($j = 0, $k = sizeof($pInfo['attributes']); $j < $k; $j++) {
                   $productName .= '<br><nobr><small>&nbsp;<i> - ' . $pInfo['attributes'][$j]['option'] . ': ' . $pInfo['attributes'][$j]['value'];
                   if ($pInfo['attributes'][$j]['price'] != '0') $productName .= ' (' . $pInfo['attributes'][$j]['prefix'] . $currencies->format($pInfo['attributes'][$j]['price'] * $pInfo['quantity'], true, $order->info['currency'], $order->info['currency_value']) . ')';
                   $productName .= '</i></small></nobr>';
               }
           }
           
           if (isset($pInfo['reservation'])){
               $productName .= '<br><small>&nbsp;<b><i><u>' . sysLanguage::get('TEXT_INFO_RESERVATION_INFO') . '</u></i></b></small>' . 
                    '<br><small>&nbsp;<i> - ' . sysLanguage::get('TEXT_INFO_START_DATE') . ' ' . $pInfo['reservation']['start_date'] . '</i></small>' . 
                    '<br><small>&nbsp;<i> - ' . sysLanguage::get('TEXT_INFO_END_DATE') . ' ' . $pInfo['reservation']['end_date'] . '</i></small>' . 
                    '<br><small>&nbsp;<i> - ' . sysLanguage::get('TEXT_INFO_INSURED') . ' ' . ($pInfo['reservation']['insure'] == 'Y' ? TEXT_YES : TEXT_NO)  . '</i></small>' . 
                    '<br><small>&nbsp;<i> - ' . sysLanguage::get('TEXT_INFO_SHIPPING_METHOD') . ' ' . $pInfo['reservation']['shipping']['title'] . ' (' . $currencies->format($pInfo['reservation']['shipping']['cost']) . ')</i></small>';
           }
           
           echo '<tr>
            <td class="main" style="text-align:left;" valign="top"></td>
            <td class="main" style="text-align:right;" valign="top">' . $pInfo['quantity'] . ' x </td>
            <td class="main" style="text-align:left;" valign="top">' . $productName . '</td>
            <td class="main" style="text-align:left;" valign="top">' . $pInfo['barcode_id'] . '</td>
            <td class="main" style="text-align:right;" valign="top">' . $currencies->format($pInfo['price']) . '</td>
            <td class="main" style="text-align:right;" valign="top">' . $currencies->format($pInfo['final_price']) . '</td>
           </tr>';
       }
      ?></tbody>
     </table>
     <table cellpadding="3" cellspacing="0" border="0" width="100%">
      <tr>
       <td align="right"><table cellpadding="3" cellspacing="0" border="0" id="orderTotalsTable">
<?php
    for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
      echo '              <tr>' . "\n" .
           '                <td align="right" class="main">' . $order->totals[$i]['title'] . '</td>' . "\n" .
           '                <td align="right" class="main">' . $order->totals[$i]['text'] . '</td>' . "\n" .
           '              </tr>' . "\n";
    }
?>
       </table></td>
      </tr>
     </table></div>
     </td>
   </tr>
   <tr>
    <td><br><table cellpadding="0" cellspacing="5" border="0" width="100%">
     <tr>
      <td valign="top" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-all orderComments">
       <div class="ui-inline-dialog-titlebar ui-widget-header ui-corner-t-l ui-helper-clearfix main" unselectable="on" style="-moz-user-select: none;">
        <span class="ui-inline-dialog-title main" id="ui-inline-dialog-title-dialog" unselectable="on" style="-moz-user-select: none;"><?php
         echo 'Comments / Status';
        ?></span>
       </div>
       <div id="orderCommentsDialog" class="ui-inline-dialog-content ui-widget-content" style="width: auto;"><table cellpadding="3" cellspacing="0" border="0" width="100%">
        <tr>
         <td class="main" valign="top" colspan="2">Comments<br><textarea cols="45" rows="5" name="comments" id="comments"></textarea></td>
         <td class="main" valign="top">Status<br><?php
          $dropArr = array();
          $Qstatus = tep_db_query('select * from ' . TABLE_ORDERS_STATUS . ' order by orders_status_id');
          while($status = tep_db_fetch_array($Qstatus)){
              $dropArr[] = array('id' => $status['orders_status_id'], 'text' => $status['orders_status_name']);
          }
          echo tep_draw_pull_down_menu('status', $dropArr);
         ?><div style="margin-top:9px;"><input type="checkbox" value="1" name="notify">Notify Customer?</div></td>
         <td class="main"><?php
          echo itw_template_button(array(
              'text' => 'Update Comments/Status',
              'id' => 'commentsUpdate',
              'hidden' => ($_GET['action'] == 'insert')
          ));
         ?></td>
        </tr>
         </td>
        </tr>
       </table></div>
      </td>
     </tr>
     <tr>
      <td valign="top" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-all paymentLog">
       <div class="ui-inline-dialog-titlebar ui-widget-header ui-corner-t-l ui-helper-clearfix main" unselectable="on" style="-moz-user-select: none;">
        <span class="ui-inline-dialog-title main" id="ui-inline-dialog-title-dialog" unselectable="on" style="-moz-user-select: none;"><?php
         echo 'Payment Log';
        ?></span>
       </div>
       <div id="paymentLogDialog" class="ui-inline-dialog-content ui-widget-content" style="width: auto;"><table cellpadding="3" cellspacing="0" border="0" width="100%">
        <tr>
         <td class="main"><b>Date Added</b></td>
         <td class="main"><b>Payment Method</b></td>
         <td class="main"><b>Message</b></td>
         <td class="main"><b>Amount Paid</b></td>
         <td class="main"><b>Card Number</b></td>
        </tr>
          <?php
           $QpaymentHistory = tep_db_query('select * from orders_payments_history where orders_id = "' . $oID . '" order by date_added desc');
           while($paymentHistory = tep_db_fetch_array($QpaymentHistory)){
               $cardInfo = false;
               if (tep_not_null($paymentHistory['card_details'])){
                   $cardInfo = unserialize(cc_decrypt($paymentHistory['card_details']));
               }
               echo '<tr>
                <td class="main">' . tep_date_short($paymentHistory['date_added']) . '</td>
                <td class="main">' . $paymentHistory['payment_method'] . '</td>
                <td class="main">' . stripslashes($paymentHistory['gateway_message']) . '</td>
                <td class="main">' . $currencies->format($paymentHistory['payment_amount']) . '</td>
                <td class="main">' . (is_array($cardInfo) ? parseCC(cc_encrypt($cardInfo['cardNumber'])) : '') . '</td>
               </tr>';
               unset($cardInfo);
           }
          ?>
       </table></div>
      </td>
     </tr>
    </table></td>
   </tr>
  </table></td>
  <td width="20%" valign="top"><table cellpadding="3" cellspacing="0" border="0" width="100%">
   <tr>
    <td class="main">
     <div style="padding:2px;"><?php
      echo itw_template_button(array(
          'text' => 'Browse Products (F3)',
          'id'   => 'browseProducts'
      ));
     ?></div>
     <div style="padding:2px;"><?php
      echo itw_template_button(array(
          'text' => 'Custom Order Total (F5)',
          'id'   => 'orderTotalButton',
          'disabled' => true
      ));
     ?></div>
     <div style="padding:2px;"><?php
      echo itw_template_button(array(
          'text' => 'Apply Payment (F12)',
          'id'   => 'applyPayment'
      ));
     ?></div>
    </td>
   </tr>
  </table>
   
  </td>
 </tr>
</table>

<div id="selectProductWindow" title="Add Product(s) To The Order" style="display:none;">
 <table cellpadding="3" cellspacing="0" border="0" id="contentTable" width="100%">
  <tr>
   <td class="main">Search:<br><input type="text" value="" id="productSearch" name="productSearch" style="width:98%;"></td>
  </tr>
  <tr>
   <td valign="top" class="main"><select name="products_id" id="products" size="10" style="width:98%;"><?php
    $Qcategories = tep_db_query('select c.categories_id, cd.categories_name from ' . TABLE_CATEGORIES . ' c, ' . TABLE_CATEGORIES_DESCRIPTION . ' cd where c.categories_id = cd.categories_id and cd.language_id = "' . $_SESSION['languages_id'] . '"');
    while($categories = tep_db_fetch_array($Qcategories)){
        $Qproducts = tep_db_query('select p.products_id, pd.products_name from ' . TABLE_PRODUCTS . ' p, ' . TABLE_PRODUCTS_DESCRIPTION . ' pd, ' . TABLE_PRODUCTS_TO_CATEGORIES . ' p2c where p2c.products_id = p.products_id and p2c.categories_id = "' . $categories['categories_id'] . '" and p.products_id = pd.products_id and pd.language_id = "' . $_SESSION['languages_id'] . '"');
        echo '<optgroup label="' . addslashes($categories['categories_name']) . '">';
        while($products = tep_db_fetch_array($Qproducts)){
            if (!empty($products['products_name'])){
                echo '<option value="' . $products['products_id'] . '">' . $products['products_name'] . '</option>';
            }
        }
        echo '</optgroup>';
    }
   ?></select></td>
  </tr>
  <tr>
   <td valign="top" class="main"><div id="productsAttribs"></div><br>Quantity: <input type="text" size="4" name="qty" id="qty" value="1"><br><br>Use this button to continue adding products.<br><input id="selectProductWindow_addMultiple" type="button" value="Add And Continue"></td>
  </tr>
 </table>
</div>
<div id="selectCustomerWindow" title="Browse For A Customer" style="display:none">
 <table cellpadding="3" cellspacing="0" border="0" id="contentTable" width="100%">
  <tr>
   <td class="main">Search:<br><input type="text" value="" id="customerSearch" name="customerSearch" style="width:98%;"></td>
  </tr>
  <tr>
   <td valign="top" class="main"><select name="customers_id" id="customers" size="5" style="width:98%;"><?php
    echo '<option value="new">.New Customer</option>';
    $Qcustomers = tep_db_query('select concat(customers_lastname, ", ", customers_firstname) as customers_name, customers_id from ' . TABLE_CUSTOMERS . ' order by customers_lastname');
    while($customers = tep_db_fetch_array($Qcustomers)){
        echo '<option value="' . $customers['customers_id'] . '">' . $customers['customers_name'] . '</option>';
    }
   ?></select></td>
  </tr>
  <tr>
   <td valign="top" class="main" width="100%"><div id="customerInfo"></div></td>
  </tr>
 </table>
</div>
<div id="applyPaymentWindow" title="Apply Payment To Order" style="display:none;"></div>
<div id="newAddressWindow" title="Add A New Address" style="display:none;"></div>
<div id="editAddressWindow" title="Edit Address" style="display:none;"></div>