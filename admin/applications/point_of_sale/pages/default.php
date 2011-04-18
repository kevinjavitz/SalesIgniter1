<script type="text/javascript" src="../ext/jQuery/ui/jquery.ui.datepicker.js"></script>
<script type="text/javascript" src="../ext/jQuery/ui/jquery.ui.sortable.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/selectbox/jquery.select_box.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/ajax_manager/jquery.ajax_manager.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/autocomplete/jquery.autocomplete.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/pointOfSale.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/showProductSelectWindow.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/showProductEditWindow.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/showCustomerSelectWindow.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/showNewAddressWindow.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/showEditAddressWindow.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/showShippingMethods.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/showOrderTotals.js"></script>
<script type="text/javascript" src="<?php echo DIR_WS_APP;?>point_of_sale/javascript/showApplyPaymentWindow.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/onetime_rentals/jquery.reservation.js"></script>
<script>  
  $(document).ready(function (){
      /*
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
      */
      $(document).pointOfSale({
          urls: {
              order: '<?php echo tep_href_link('point_of_sale.php', '', 'SSL');?>'
          }
      });
      
      <?php
       /*if ($action == 'modify'){
           echo '$(document).pointOfSale(\'loadOrder\');';
       }*/
      ?>
  });
</script>
<style>
 .defaultText {
     text-align:center;
 }
</style>
<div id="posWrapper" style="width:900px;margin-left:auto;margin-right:auto;">
 <div style="display:inline-block;width:675px;vertical-align:top;">
  <div id="addressBar" style="width:662px;text-align:center;">
   <div style="width:32%;float:left" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-all"><?php echo dialogBlock('billingAddress', 'Billing Address', 'Please Select A Customer', true);?></div>
   <div style="width:31%;display:inline-block;" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-all"><?php echo dialogBlock('shippingAddress', 'Shipping Address', 'Please Select A Customer', true);?></div>
   <div style="width:32%;float:right;" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-all"><?php echo dialogBlock('pickupAddress', 'Pickup Address', 'Please Select A Customer', true);?></div>
  </div>
  <br />
  <div id="shippingMethodBar" style="width:655px;">
   <div style="width:655px;display:inline-block;" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-all"><?php echo dialogBlock('shippingMethod', 'Shipping Method', 'Please Select A Customer');?></div>
  </div>
  <br />
  <div id="productsBar" style="width:655px;" class="ui-inline-dialog ui-widget ui-widget-content ui-corner-all">
   <div id="productListing">
    <table cellpadding="3" cellspacing="0" border="0" width="100%" id="productsTable">
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
     <tbody>
     </tbody>
    </table>
   </div>
   <div id="orderTotals" style="display:inline-block;float:right;"></div>
  </div>
 </div>
 <div style="display:inline-block;width:200px;vertical-align:top;">
 <?php
  $browseCustomersButton = htmlBase::newElement('button')->setText('Browse Customers (F1)')->attr('id', 'browseCustomers');
  $browseProductsButton = htmlBase::newElement('button')->css('margin-top', '.3em')->setText('Browse Products (F3)')->attr('id', 'browseProducts');
  $customTotalButton = htmlBase::newElement('button')->css('margin-top', '.3em')->setText('Custom Order Total (F5)')->attr('id', 'orderTotalButton');
  $resetButton = htmlBase::newElement('button')->css('margin-top', '.3em')->setText('Reset Order (F10)')->attr('id', 'resetButton');
  $paymentButton = htmlBase::newElement('button')->css('margin-top', '.3em')->setText('Apply Payment (F12)')->attr('id', 'applyPayment');
  
  echo $browseCustomersButton->draw() . '<br />' . 
       $browseProductsButton->draw() . '<br />' . 
       $customTotalButton->draw() . '<br />' . 
       $resetButton->draw() . '<br />' . 
       $paymentButton->draw();
 ?>
 </div>
</div>
   <!--<tr>
    <td><br><table cellpadding="0" cellspacing="5" border="0" width="100%">
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
        </tr>
       </table></div>
      </td>
     </tr>
    </table></td>
   </tr>-->

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
   <td valign="top" class="main"><div id="productsAttribs"></div><br>Quantity: <input type="text" size="4" name="qty" id="qty" value="1"><!--<br><br>Use this button to continue adding products.<br><input id="selectProductWindow_addMultiple" type="button" value="Add And Continue">--></td>
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