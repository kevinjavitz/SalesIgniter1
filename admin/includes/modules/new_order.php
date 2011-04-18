<?php
 if (!function_exists('tep_validate_password')){
     require('../includes/functions/password_funcs.php');
 }
 if (!isset($_SESSION['currency']) || isset($_GET['currency']) || (USE_DEFAULT_LANGUAGE_CURRENCY == 'true' && LANGUAGE_CURRENCY != $_SESSION['currency'])){
     if (!isset($_SESSIOIN['currency'])){
         $_SESSIOIN['currency'] = (USE_DEFAULT_LANGUAGE_CURRENCY == 'true' ? LANGUAGE_CURRENCY : DEFAULT_CURRENCY);
     }

     if (isset($_GET['currency']) && tep_currency_exists($_GET['currency'])){
         $_SESSION['currency'] = $_GET['currency'];
     }
 }

 if (isset($GLOBALS['shipping'])){
     unset($GLOBALS['shipping']);
 }

 if (isset($_SESSION['order'])){
     unset($_SESSION['order']);
 }

 $_SESSION['order'] = new newOrder();
 if (isset($_GET['oID'])){
     $_SESSION['order']->loadOrder($_GET['oID']);
 }
?>
<link rel="stylesheet" type="text/css" href="../ext/jQuery/themes/smoothness/stylesheet.css">
<script type="text/javascript" src="../ext/jQuery/jQuery.js"></script>
<script type="text/javascript" src="../ext/jQuery/jquery.ui.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/select_box/jquery.select_box.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/ajax_manager/jquery.ajax_manager.js"></script>
<script type="text/javascript" src="../ext/jQuery/external/form/jquery.form.js"></script>
<script language="Javascript">
 var $ajaxLoader;
 var $orderLoader;
 var ajaxManager_main;

/*
 * jQuery AjaxQ - AJAX request queueing for jQuery
 *
 * Version: 0.0.1
 * Date: July 22, 2008
 *
 * Copyright (c) 2008 Oleg Podolsky (oleg.podolsky@gmail.com)
 * Licensed under the MIT (MIT-LICENSE.txt) license.
 *
 * http://plugins.jquery.com/project/ajaxq
 * http://code.google.com/p/jquery-ajaxq/
 */

 $.ajaxq = function (queue, options){
     // Initialize storage for request queues if it's not initialized yet
     if (typeof document.ajaxq == "undefined") document.ajaxq = {q:{}, r:null};

     // Initialize current queue if it's not initialized yet
     if (typeof document.ajaxq.q[queue] == "undefined") document.ajaxq.q[queue] = [];

     // Request settings are given, enqueue the new request
     if (typeof options != "undefined"){
         // Copy the original options, because options.complete is going to be overridden
         var optionsCopy = {};
         for (var o in options) optionsCopy[o] = options[o];
         options = optionsCopy;

         // Override the original callback
         var originalCompleteCallback = options.complete;

         options.complete = function (request, status){
             // Dequeue the current request
             document.ajaxq.q[queue].shift ();
             document.ajaxq.r = null;

             // Run the original callback
             if (originalCompleteCallback) originalCompleteCallback (request, status);

             // Run the next request from the queue
             if (document.ajaxq.q[queue].length > 0) document.ajaxq.r = jQuery.ajax (document.ajaxq.q[queue][0]);
         };

         // Enqueue the request
         document.ajaxq.q[queue].push (options);

         // Also, if no request is currently running, start it
         if (document.ajaxq.q[queue].length == 1) document.ajaxq.r = jQuery.ajax (options);
     } else {
         // No request settings are given, stop current request and clear the queue
         if (document.ajaxq.r){
             document.ajaxq.r.abort ();
             document.ajaxq.r = null;
         }

         document.ajaxq.q[queue] = [];
     }
 }

 $(document).ready(function (){
     ajaxManager_main = $.manageAjax({manageType: 'queue', maxReq: 1});

     $ajaxLoader = $('#ajaxLoaderWindow').dialog({
         stack: true,
         autoOpen: false
     });

     $orderLoader = $('#orderLoaderWindow').dialog({
         stack: true,
         autoOpen: false
     });

     function createList($inputField, array){
         $('#' + $inputField.attr('id') + '_list_container').remove();
         // create the results div
         var div = $('<div />').attr('id', $inputField.attr('id') + '_list_container');

         var posLeft = $inputField.offset().left;
         var posTop = $inputField.offset().top;

         posTop -= $inputField.parents('.ui-dialog-container').offset().top;
         posLeft -= $inputField.parents('.ui-dialog-container').offset().left;

         posTop += $inputField.height() + 6;

         $(div).css({
             width: $inputField.width() + 'px',
             overflow: 'auto',
             float: 'left',
             position: 'absolute',
             top: posTop,
             left: posLeft,
             background: 'white',
             border: '1px solid black',
             'z-index': '1001',
             padding: '2px'
         });

         if (array.length > 10){
             $(div).css('height', '200');
         }

         $(document).unbind('click').click(function (){
             $('#' + $inputField.attr('id') + '_list_container').remove();
             $('#tooManyResults').hide();
             $(div).remove();
         });

         $.each(array, function (i, text){
             var listDiv = $('<div />').addClass('main').html(text[0]);
             listDiv.css('white-space', 'nowrap');
             listDiv.mouseover(function (){
                 this.style.background = 'lightblue';
                 this.style.cursor = 'pointer';
             }).mouseout(function (){
                 this.style.background = 'white';
             }).click(function (){
                 $inputField.val(this.innerHTML);
                 $('#tooManyResults').hide();
                 $('#' + $inputField.attr('id') + '_list_container').remove();
             });
             div.append(listDiv);
         });
         $inputField.parent().append(div);
         div.focus();

       return div;
     }

     function showIFrameHack($obj) {
         if (jQuery.browser.msie != true) return;
         //Hack to add an iframe under the menu which should hide the selects:
         var iframeShim = document.getElementById($obj.attr('id') + "_hvrShm");
         if (iframeShim != null) {
             iframeShim.style.visibility = 'visible';
         } else {
             jQuery('<IFRAME style="position:absolute;z-index:-1;"'+
                 ' src="javascript:\'\';" frameBorder="0" scrolling="no"' +
                 ' id="' + $obj.attr('id') + '_hvrShm" />').css({
                     border: '1px solid black',
                     top: -1,
                     left: -1,
                     width: $obj.get(0).offsetWidth,
                     height: $obj.get(0).offsetHeight
                 }).appendTo($obj);
         }
     }

     function hideIFrameHack($obj) {
         if (jQuery.browser.msie != true) return;
         //find and remove the iframe:
         $('#' + $obj.attr('id') + '_hvrShm').remove();
     }

     $('#selectCustomer').unbind('click').click(function (){
         var $selectCustomerWindow = $('#selectCustomerWindow').clone().appendTo(document.body).show().dialog({
             width: 400,
             height: ($(window).height() * .5),
             draggable: true,
             resizable: true,
             modal: true,
             position: 'center',
             buttons: {
                 /*'New Customer': function (){
                     $('#selectCustomerFieldset').hide();
                     $('#searchCustomerFieldset').hide();
                     $(this).hide();
                     $('#selectCustomerWindow_cancelButton').show();
                     $('#newCustomerTable').show();
                     $('#selectCustomerWindow_cancelButton').unbind('click').click(function (){
                         $('#selectCustomerFieldset').show();
                         $('#searchCustomerFieldset').show();
                         $('#newCustomerButton').show();
                         $('#newCustomerTable').hide();
                         $('#selectCustomerWindow_cancelButton').hide();
                     });
                 },*/
                 'Continue': function (){
                     var url = '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getFormattedAddress');?>';
                     if ($('input[id="customers_name_search"]', $selectCustomerWindow).val() != ''){
                         var inputVals = 'customers_name_search=' + $('input[id="customers_name_search"]', $selectCustomerWindow).val();
                     }else if ($('input[id="customers_email_search"]', $selectCustomerWindow).val() != ''){
                         var inputVals = 'customers_email_search=' + $('input[id="customers_email_search"]', $selectCustomerWindow).val();
                     }else{
                         var inputVals = $('#form_newCustomerTable', $selectCustomerWindow).serialize();
                     }
                     url = url + '&' + inputVals
                     ajaxManager1.add({
                         url: url,
                         cache: false,
                         dataType: 'json',
                         success: function (response){
                             if (response.customerFound == false){
                                 alert('No Customer Found, Please Refine Your Search.');
                             }else{
                                 updateSelectedCustomer(response);
                                 $selectCustomerWindow.dialog('destroy');
                             }
                         }
                     });
                 }
             }
         });

         showIFrameHack($selectCustomerWindow);

         function getCustomerSearchOptions(type, keyword){
             return {
                 url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=searchCustomer');?>&by=' + type + '&keyword=' + keyword,
                 cache: false,
                 dataType: 'json',
                 success: function (response){
                     var customers = response.customers;
                     var $inputField = $('input[id="customers_' + type + '_search"]', $selectCustomerWindow);
                     if (customers.length > 0){
                         if (customers.length == 100){
                             $('#tooManyResults').show();
                         }else{
                             $('#tooManyResults').hide();
                         }
                         createList($inputField, customers);
                     }else{
                         $('#' + $inputField.attr('id') + '_list_container').remove();
                         $('#tooManyResults').hide();
                     }
                 }
             };
         }

         var ajaxManager1 = $.manageAjax({manageType: 'abortOld', maxReq: 0});
         $('input[id="customers_email_search"]', $selectCustomerWindow).unbind('keyup').keyup(function(){
             ajaxManager1.add(getCustomerSearchOptions('email', $(this).val()));
         });

         $('input[id="customers_name_search"]', $selectCustomerWindow).unbind('keyup').keyup(function(){
             ajaxManager1.add(getCustomerSearchOptions('name', $(this).val()));
         });
     });

     $('#selectProduct').click(function (){
         var $selectProductWindow = $('#selectProductWindow').clone().appendTo(document.body).show().dialog({
             width: ($(window).width() * .75),
             height: ($(window).height() * .5),
             draggable: true,
             resizable: true,
             modal: true,
             position: 'center',
             buttons: {
                 'Add Product And Close Window': function (){
                     var urlVars = $('#form_selectProductTable', $selectProductWindow).serialize();

                     jQuery.ajax({
                         url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getProductRow');?>&' + urlVars,
                         dataType: 'html',
                         cache: false,
                         success: function (html){
                             addProductRows(html);
                             getOrderTotals();
                             $selectProductWindow.dialog('destroy');
                         }
                     });
                 }
             }
         });

         var $selectBox = $('#products', $selectProductWindow);
         $selectBox.removeOption(/./);

         $selectBox.unbind('change').change(function (){
             $('#productsAttribs', $selectProductWindow).html('Searching For Attributes....');
             $.ajax({
                 url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getProductsAttribs');?>&products_id=' + $(this).val(),
                 dataType: 'html',
                 cache: false,
                 success: function (html){
                     if (html == 'false'){
                         $('#productsAttribs', $selectProductWindow).html('No Attributes Found');
                     }else{
                         $('#productsAttribs', $selectProductWindow).html(html);
                     }
                 }
             });
         });

         $('#selectProductWindow_addMultiple', $selectProductWindow).unbind('click').click(function (){
             var urlVars = $('#form_selectProductTable', $selectProductWindow).serialize();
             urlVars = urlVars + '&country_id=' + $('#billingCountry').val();
             urlVars = urlVars + '&zone_id=' + $('#billingZone').val();

             $.ajax({
                 url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getProductRow');?>&' + urlVars,
                 dataType: 'html',
                 cache: false,
                 success: function (html){
                     addProductRows(html);
                     getOrderTotals();
                 }
             });
         });

         $.ajax({
             url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getProducts');?>',
             dataType: 'json',
             cache: false,
             success: function (data){
                 $selectBox.removeOption(/./);
                 $selectBox.html('');
                 $.each(data.data, function (i, obj){
                     var group = $('<optgroup label="' + obj.group + '"></optgroup>');
                     $.each(obj.products, function (i, product){
                         group.append('<option value="' + product[0] + '">' + product[1] + '</option>');
                     });
                     $selectBox.append(group);
                 });
                 $selectBox.trigger('change');
             }
         });
     });

     $('#newOrder').ajaxForm({
         cache: false,
         dataType: 'json',
         success: function (data){
             if (data.remotePayment == true){
                 var cssOptions = {
                     width: 750,
//                     height: 300,
                     position: 'absolute',
                     top: 200,
                     left: 200,
                     background: 'white',
                     border: '1px solid black'
                 };
                 $('#processPaymentWindow_close').unbind('mouseover').mouseover(function (){
                     this.style.cursor = 'pointer';
                 }).click(function (){
                     $('#processPaymentWindow').hide();
                     $('#processPaymentWindow_iframe').remove();
                 });
                 var iframe = jQuery('<iframe width="745" height="295" id="processPaymentWindow_iframe" name="processPaymentWindow_iframe" style="display:none" onload="submitOrder()"></iframe>');
                 $('#processPaymentWindow').append('<form action="' + data.formUrl + '" method="post" id="processPayment" target="processPaymentWindow_iframe"><br><br><input id="processPaymentWindow_continue" type="submit" value="Continue To Payment Website">' + data.html + '<br><br><br></form>');
                 $('#processPaymentWindow').css(cssOptions).append(iframe).show();
                 $('#processPaymentWindow').find('input[name="x_Relay_URL"]').val('<?php echo tep_href_link('a-net_process.php');?>');
                 $('#processPaymentWindow').find('input[name="x_Cust_ID"]').val(data.customersID);
                 $('#processPaymentWindow').find('#processPaymentWindow_continue').click(function (){
                     $('#processPayment').hide();
                     $(this).hide();
                     iframe.show();
                 });
             }else{
                 window.location = '<?php echo tep_href_link(FILENAME_ORDERS, 'action=edit');?>&oID=' + data.order_id;
             }
         }
     });

     $('#updateAndSend').click(function (){
         $('#notify').get(0).checked = true;
         $('#orderInsertButton').click();
     });

     $('#sendEmail').click(function (){
         $.ajax({
             url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=sendEmail&oID=' . $_GET['oID']);?>',
             cache: false,
             dataType: 'json',
             success: function (){
                 alert('Email Sent');
             }
         });
     });

     $('#customTotal').click(function (){
         var $totalsTable = $('#orderTotals');
         var $totalsRows = $('tr', $totalsTable);
         var nextIndex = (($totalsRows.size() - 1) + 1);
         $newRow = jQuery('<tr sort_order="' + nextIndex + '">' +
           '<td class="main"><?php echo tep_image(DIR_WS_IMAGES . 'icons/cross.gif', 'Remove Order Total', '', '', 'id="delete"') . tep_image(DIR_WS_IMAGES . 'up.gif', 'Move Up', '', '', 'id="up"') . tep_image(DIR_WS_IMAGES . 'down.gif', 'Move Down', '', '', 'id="down"');?>&nbsp;<input id="customTotalText" name="customTotal[' + nextIndex + '][text]" type="text"></td>' +
           '<td class="main">$<input size="6" id="customTotalVal" name="customTotal[' + nextIndex + '][value]" type="text"></td>' +
         '</tr>');

         setSortButtons($newRow, false);

         $('#orderTotals').append($newRow);
     });

     <?php if (isset($_GET['oID'])){ ?>
     $.ajaxq('loadingQue', {
         url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getFormattedAddress&fromOrder=true');?>',
         cache: false,
         beforeSend: function (){
             $('.ui-dialog-content', $orderLoader.element).html('Loading Customer\'s Address\'s');
             $orderLoader.dialog('open');
         },
         dataType: 'json',
         success: function (response){
             if (response.customerFound == false){
                 alert('No Customer Found, Please Refine Your Search.');
             }else{
                 updateSelectedCustomer(response);
             }
         }
     });

     $.ajaxq('loadingQue', {
         url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getProductRow&fromOrder=true');?>',
         dataType: 'html',
         cache: false,
         beforeSend: function (){
             $('.ui-dialog-content', $orderLoader.element).html('Loading Orders Products');
         },
         success: function (html){
             addProductRows(html);
         }
     });
     <?php } ?>

     $.ajaxq('loadingQue', {
         url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getMethods&method=shipping');?>',
         dataType: 'html',
         cache: false,
         beforeSend: function (){
             $('.ui-dialog-content', $orderLoader.element).html('Loading Shipping Methods');
             $orderLoader.dialog('open');
         },
         success: function (html){
             <?php
              if (isset($_GET['oID']) && tep_not_null($_SESSION['order']->info['shipping_module'])){
                  $ship = explode('_', $_SESSION['order']->info['shipping_module']);
             ?>
             $('#shippingMethods').html(html);
             setupShippingMethods();
             $('select[id="shipping_method"]').val('<?php echo $ship[0];?>').trigger('change');
             <?php } ?>
            // $('#selectCustomer').show();
         }
     });
     $.ajaxq('loadingQue', {
         url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getMethods&method=payment');?>',
         dataType: 'html',
         cache: false,
         beforeSend: function (){
             $('.ui-dialog-content', $orderLoader.element).html('Loading Payment Methods');
             $orderLoader.dialog('open');
         },
         complete: function (){
             $orderLoader.dialog('close');
         },
         success: function (html){
             $('#paymentMethods').html(html);
             setupPaymentMethods();
             <?php if (isset($_GET['oID'])){ ?>
             $('select[id="payment_method"]').val('<?php echo $_SESSION['order']->info['payment_module'];?>').trigger('change');
             $('#paymentMethods').html($('select[id="payment_method"] > option[value="<?php echo $_SESSION['order']->info['payment_module'];?>"]').html());
             <?php } ?>
             getOrderTotals();
         }
     });

 });

 function moveOrderTotal($button, direction){
     var $row = $button.parent().parent();
     var currentSort = parseInt($row.attr('sort_order'));
     if (direction == 'up'){
         var newSort = (currentSort - 1);
         var $whichRow = $row.prev();
     }else{
         var newSort = (currentSort + 1);
         var $whichRow = $row.next();
     }

     if ($whichRow.length > 0){
         if ($whichRow.attr('sort_order') == newSort){
             $whichRow.attr('sort_order', currentSort);
         }
         $row.attr('sort_order', newSort);
         $('#customTotalText', $row).attr('name', 'customTotal[' + newSort + '][text]');
         $('#customTotalVal', $row).attr('name', 'customTotal[' + newSort + '][value]');
         if (direction == 'up'){
             $row.insertBefore($whichRow);
         }else{
             $row.insertAfter($whichRow);
         }
         getOrderTotals();
     }
 }

 function setSortButtons(totalsRow, skipBlur){
     $('#up, #down, #delete', totalsRow).hover(function (){
         $(this).css('cursor', 'pointer');
     }, function (){
         $(this).css('cursor', 'default');
     }).click(function (){
         if ($(this).attr('id') == 'delete'){
             $(this).parent().parent().remove();
             getOrderTotals();
         }else{
             moveOrderTotal($(this), $(this).attr('id'));
         }
       return false;
     });

     if (typeof skipBlur == 'undefined' || skipBlur != false){
         $('#customTotalVal', totalsRow).attr('has_focus', 'false');
         $('#customTotalText', totalsRow).attr('has_focus', 'false');
         $('#customTotalVal, #customTotalText', totalsRow).unbind('focus').focus(function (){
             $(this).attr('has_focus', 'true');
         }).unbind('blur').blur(function (){
             $(this).attr('has_focus', 'false');
             setTimeout(function (){
                 if ($('#customTotalVal', totalsRow).attr('has_focus') == 'false' && $('#customTotalText', totalsRow).attr('has_focus') == 'false'){
                     getOrderTotals();
                 }
             }, 50);
         });
     }
 }

 function setupPaymentMethods(){
     $('select[id="payment_method"]').change(function (){
         if ($(this).val() != ''){
             $.ajaxq('loadingQue', {
                 url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getPaymentFields');?>&module=' + $('select[id="payment_method"]').val(),
                 dataType: 'html',
                 cache: false,
                 success: function (html){
                     $('#payment_method_fields').html(html);
                 }
             });
         }else{
             $('#payment_method_fields').html('');
         }
     });
 }

 function getOrderTotals(){
     var linkParams = new Array();
     var $customTotals = $('input[type="text"]', $('#orderTotals')).each(function (){
         linkParams.push($(this).attr('name') + '=' + $(this).val());
     });
     var urlGetVars = linkParams.join('&');
     $.ajaxq('loadingQue', {
         url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getOrderTotals');?>' + (urlGetVars.length > 0 ? '&' + urlGetVars : ''),
         dataType: 'html',
         cache: false,
         beforeSend: function (){
             $('.ui-dialog-content', $orderLoader).html('Loading Order Totals');
             $orderLoader.dialog('open');
         },
         complete: function (){
             $orderLoader.dialog('close');
         },
         success: function (html){
             $('#orderTotals').html(html);
             $('tr', $('#orderTotals')).each(function (){
                 setSortButtons($(this));
             });
             $('#customTotal').show();
         }
     });
 }

 function setupShippingMethods(){
     $('select[id="shipping_method"]').change(function (){
         if ($(this).val() != ''){
             if ($('#customerInfo:visible').length > 0){
                 $('#selectProduct').show();
             }
             $.ajaxq('loadingQue', {
                 url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getShippingQuotes');?>&module=' + $('select[id="shipping_method"]').val(),
                 dataType: 'html',
                 cache: false,
                 success: function (html){
                     $('#shipping_method_fields').html(html);
                     $(':radio', $('#shipping_method_fields')).each(function (){
                         $(this).click(function (){
                             ajaxManager_main.add({
                                 url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=setShippingMethod');?>&method=' + $(this).val(),
                                 cache: false,
                                 dataType: 'json',
                                 success: function (data){
                                     if (data.success == true){
                                         getOrderTotals();
                                     }
                                 }
                             });
                         });
                     });

                     var clicked = false;
                     $(':radio', $('#shipping_method_fields')).each(function (){
                         if ($(this).val() == '<?php echo $_SESSION['order']->info['shipping_module'];?>'){
                             $(this).trigger('click');
                             $('#shippingMethods').html($('td:eq(1)', $(this).parent().parent()).html());
                             clicked = true;
                         }
                     });

                     if (clicked == false){
                         getOrderTotals();
                     }
                 }
             });
         }else{
            // getOrderTotals();
             $('#shipping_method_fields').html('');
         }
     });
 }

 function getIframeBody(frameID){
     var doc = null;
     try {
         if (document.getElementById(frameID).contentDocument) { // For NS6
             doc = document.getElementById(frameID).contentDocument.body;
         } else if (document.getElementById(frameID).contentWindow) { // For IE5.5 and IE6
             doc = document.getElementById(frameID).contentWindow.document.body;
         } else if (document.getElementById(frameID).document) { // For IE5
             doc = document.getElementById(frameID).document.body;
         }
     }
     catch (err) {
     }
   return doc;
 }

 function submitOrder(){
     var doc = getIframeBody('processPaymentWindow_iframe');
     if (doc){
         var response = '';
         try {
             response = eval('(' + doc.innerHTML + ')');
         }
         catch (err) {}

         if (typeof response == 'object'){
             $('#processPaymentWindow').hide();
             if (response.success == false){
                 jQuery.ajax({
                     url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=addOrderComments');?>&comment=' + response.error_msg,
                     dataType: 'json',
                     cache: false,
                     success: function (res){
                         $('#newOrder').attr('action', '<?php echo tep_href_link(FILENAME_ORDERS, 'action=saveNewOrder&completed=true');?>');
                         $('#newOrder').submit();
                     }
                 });
             }else{
                 $('#newOrder').attr('action', '<?php echo tep_href_link(FILENAME_ORDERS, 'action=saveNewOrder&completed=true');?>');
                 $('#newOrder').submit();
             }
         }
     }
 }

 function buildAddressEditButton(id){
     var $addressEditButton = $('<input type="button" />').attr('value', 'Edit').attr('id', id).click(function (){
         var $this = $(this);
         $.ajaxq('loadingQue', {
             url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getAddressEdit');?>&address=' + $this.attr('id'),
             cache: false,
             dataType: 'html',
             success: function (html){
                 var $addressEditWindow = $('#addressEditWindow').clone().html(html).appendTo(document.body).show();
                 $addressEditWindow.dialog({
                     width: 500,
                     minHeight: 300,
                     buttons: {
                         'Continue': function (){
                             var getVars = $('input[type="text"], select', $addressEditWindow).serialize();
                             $.ajaxq('loadingQue', {
                                 url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=updateAddress');?>&address=' + $this.attr('id') + '&' + getVars,
                                 cache: false,
                                 dataType: 'json',
                                 success: function (data){
                                     $addressEditWindow.dialog('destroy');
                                     $.ajaxq('loadingQue', {
                                         url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getFormattedAddress&fromOrder=true');?>',
                                         cache: false,
                                         beforeSend: function (){
                                             $('.ui-dialog-content', $orderLoader).html('Loading Customer\'s Address\'s');
                                             $orderLoader.dialog('open');
                                         },
                                         complete: function (){
                                             $orderLoader.dialog('close');
                                         },
                                         dataType: 'json',
                                         success: function (response){
                                             if (response.customerFound == false){
                                                 alert('No Customer Found, Please Refine Your Search.');
                                             }else{
                                                 updateSelectedCustomer(response);
                                             }
                                         }
                                     });
                                 }
                             });
                         }
                     }
                 });
             }
         });
     });
   return $addressEditButton;
 }

 function updateSelectedCustomer(data){
     var $customerAddressEditButton = buildAddressEditButton('cAddress');
     var $deliveryAddressEditButton = buildAddressEditButton('dAddress');
     var $billingAddressEditButton = buildAddressEditButton('bAddress');

     $('#customerDefaultAddress')
     .html(data.customer_address)
     .append($customerAddressEditButton)
     .append('<input type="hidden" name="customersCountry" id="customersCountry" value="' + data.customer_country_id + '"><input type="hidden" name="customersZone" id="customersZone" value="' + data.customer_zone_id + '">');

     $('#customerShippingAddress')
     .html(data.delivery_address)
     .append($deliveryAddressEditButton)
     .append('<input type="hidden" name="shippingCountry" id="shippingCountry" value="' + data.delivery_country_id + '"><input type="hidden" name="shippingZone" id="shippingZone" value="' + data.delivery_zone_id + '">');

     $('#customerBillingAddress')
     .html(data.billing_address)
     .append($billingAddressEditButton)
     .append('<input type="hidden" name="billingCountry" id="billingCountry" value="' + data.billing_country_id + '"><input type="hidden" name="billingZone" id="billingZone" value="' + data.billing_zone_id + '">');

     $('#customerEmailAddress').html(data.email_address);
     $('#customerTelephone').html(data.telephone);
     $('#customerInfo').show();
     $('#selectProduct').show();
 }

 function addProductRows(newRows){
     $('#productTable > tbody').html(newRows);
     $('tr', $('#productTable > tbody')).each(function (){
         var $curRow = $(this);
         $('td:eq(0)', this).each(function (){
             $('#removeProduct', this).hover(function (){
                 $(this).css('cursor', 'pointer');
             }, function (){
                 $(this).css('cursor', 'default');
             }).click(function (){
                 $.ajaxq('loadingQue', {
                     url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=removeProduct');?>&pID=' + $curRow.attr('id'),
                     beforeSend: function (){
                         $('.ui-dialog-content', $ajaxLoader).html('Removing Product From Order');
                         $ajaxLoader.dialog('open');
                     },
                     complete: function (){
                         $ajaxLoader.dialog('close');
                     },
                     cache: false,
                     dataType: 'html',
                     success: function (html){
                         addProductRows(html);
                        // getOrderTotals();
                     }
                 });
             });

             $('#editProduct', this).hover(function (){
                 $(this).css('cursor', 'pointer');
             }, function (){
                 $(this).css('cursor', 'default');
             }).click(function (){
                 $.ajaxq('loadingQue', {
                     url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=editProduct');?>&pID=' + $curRow.attr('id'),
                     cache: false,
                     dataType: 'html',
                     success: function (html){
                         var $productEditWindow = $('<div>').attr('title', 'Edit Order Product').html(html);
                         $productEditWindow.dialog({
                             width: ($(window).width() * .75),
                             height: ($(window).height() * .5),
                             buttons: {
                                 'Update Product': function (){
                                     var urlParams = $('input[type="text"], input[type="hidden"], input[type="radio"]:checked, input[type="checkbox"]:checked, select', $productEditWindow).serialize();
                                     $.ajaxq('loadingQue', {
                                         url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=updateProduct');?>&' + urlParams,
                                         cache: false,
                                         dataType: 'json',
                                         success: function (data){
                                             $.ajaxq('loadingQue', {
                                                 url: '<?php echo tep_href_link(FILENAME_ORDERS, 'action=getProductRow&fromOrder=true');?>',
                                                 dataType: 'html',
                                                 cache: false,
                                                 beforeSend: function (){
                                                     $('.ui-dialog-content', $orderLoader).html('Loading Orders Products');
                                                 },
                                                 complete: function (){
                                                     $orderLoader.dialog('close');
                                                 },
                                                 success: function (html){
                                                     addProductRows(html);
                                                     if ($('select[id="shipping_method"]').size() <= 0){
                                                         //getOrderTotals();
                                                     }else{
                                                         $('select[id="shipping_method"]').trigger('change');
                                                     }
                                                     $productEditWindow.dialog('destroy');
                                                 }
                                             });
                                         }
                                     });
                                 },
                                 'Cancel': function (){
                                     $productEditWindow.dialog('destroy');
                                 }
                             }
                         });
                     }
                 });
             });
         });
     });
 }
</script>
<form id="newOrder" action="<?php echo tep_href_link(FILENAME_ORDERS, 'action=saveNewOrder');?>" method="POST">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
 <tr>
  <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
   <tr>
    <td class="pageHeading"><?php echo (isset($_GET['oID']) ? 'Edit Order' : 'New Order');?></td>
    <td class="pageHeading" align="right"><?php echo tep_draw_separator('pixel_trans.gif', 1, HEADING_IMAGE_HEIGHT); ?></td>
    <td class="pageHeading" align="right"><?php echo htmlBase::newElement('button')->usePreset('back')->setHref(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))))->draw(); ?></td>
   </tr>
  </table></td>
 </tr>
 <tr>
  <td><table width="100%" border="0" cellspacing="0" cellpadding="2" id="customerInfo" style="display:none">
   <tr>
    <td colspan="3"><?php echo tep_draw_separator(); ?></td>
   </tr>
   <tr>
    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
     <tr>
      <td class="main" valign="top"><b><?php echo sysLanguage::get('ENTRY_CUSTOMER'); ?></b></td>
      <td class="main"><div id="customerDefaultAddress"></div></td>
     </tr>
     <tr>
      <td class="main"><b><?php echo sysLanguage::get('ENTRY_TELEPHONE_NUMBER'); ?></b></td>
      <td class="main"><div id="customerTelephone"></div></td>
     </tr>
     <tr>
      <td class="main"><b><?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS'); ?></b></td>
      <td class="main"><div id="customerEmailAddress"></div></td>
     </tr>
    </table></td>
    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
     <tr>
      <td class="main" valign="top"><b><?php echo sysLanguage::get('ENTRY_SHIPPING_ADDRESS'); ?></b></td>
      <td class="main"><div id="customerShippingAddress"></div></td>
     </tr>
    </table></td>
    <td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="2">
     <tr>
      <td class="main" valign="top"><b><?php echo sysLanguage::get('ENTRY_BILLING_ADDRESS'); ?></b></td>
      <td class="main"><div id="customerBillingAddress"></div></td>
     </tr>
    </table></td>
   </tr>
  </table></td>
 </tr>
 <tr>
  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
 </tr><!--//
 <tr>
  <td colspan="2"><input id="selectCustomer" type="button" value="Select Customer" style="display:none"></td>
 </tr>
 <tr>
  <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
 </tr>//-->
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>
 <!--//<tr>
  <td><table border="0" cellspacing="0" cellpadding="2">
   <tr>
    <td class="main" valign="top"><b><?php echo sysLanguage::get('ENTRY_PAYMENT_METHOD'); ?></b></td>
    <td class="main" id="paymentMethods">Please Select A Store</td>
   </tr>
  </table></td>
 </tr>//-->
      <tr>
        <td><table border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td class="main"><b><?php echo sysLanguage::get('ENTRY_PAYMENT_METHOD'); ?></b></td>
            <td class="main"><?php echo $_SESSION['order']->info['payment_method']; ?></td>
          </tr>
          <tr>
            <td class="main" id="paymentMethods" style="display:none">Please Select A Store</td>
          </tr>
<?php
    if (tep_not_null($_SESSION['order']->info['cc_type']) || tep_not_null($_SESSION['order']->info['cc_owner']) || tep_not_null($_SESSION['order']->info['cc_number'])) {
?>
          <tr>
            <td colspan="2"><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
          </tr>
          <tr>
            <td class="main"><?php echo sysLanguage::get('ENTRY_CREDIT_CARD_TYPE'); ?></td>
            <td class="main"><input type="text" name="cc_type" value="<?php echo $_SESSION['order']->info['cc_type'];?>"></td>
          </tr>
          <tr>
            <td class="main"><?php echo sysLanguage::get('ENTRY_CREDIT_CARD_OWNER'); ?></td>
            <td class="main"><input type="text" name="cc_owner" value="<?php echo $_SESSION['order']->info['cc_owner']; ?>"></td>
          </tr>
          <tr>
            <td class="main"><?php echo sysLanguage::get('ENTRY_CREDIT_CARD_NUMBER'); ?></td>
            <td class="main"><input type="text" name="cc_number" value="<?php echo $_SESSION['order']->info['cc_number']; ?>"></td>
          </tr>
          <tr>
            <td class="main"><?php echo sysLanguage::get('ENTRY_CREDIT_CARD_EXPIRES'); ?></td>
            <td class="main"><input type="text" name="cc_expires" value="<?php echo $_SESSION['order']->info['cc_expires']; ?>"></td>
          </tr>
<?php
    }
?>
        </table></td>
      </tr>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>
 <tr>
  <td><table border="0" cellspacing="0" cellpadding="2">
   <tr>
    <td class="main" valign="top"><b><?php echo sysLanguage::get('ENTRY_SHIPPING_METHOD'); ?></b></td>
    <td class="main" id="shippingMethods">No Shipping Method Selected</td>
   </tr>
  </table></td>
 </tr>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>
 <tr>
  <td><input id="selectProduct" type="button" value="Add Product" style="display:none"></td>
 </tr>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>
 <tr>
  <td><table border="0" width="100%" cellspacing="0" cellpadding="2" id="productTable">
   <thead>
    <tr class="dataTableHeadingRow">
     <th class="dataTableHeadingContent"></th>
     <th class="dataTableHeadingContent" colspan="2"><?php echo sysLanguage::get('TABLE_HEADING_PRODUCTS'); ?></th>
     <th class="dataTableHeadingContent"><?php echo sysLanguage::get('TABLE_HEADING_PRODUCTS_MODEL'); ?></th>
     <th class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_TAX'); ?></th>
     <th class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_PRICE_EXCLUDING_TAX'); ?></th>
     <th class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_PRICE_INCLUDING_TAX'); ?></th>
     <th class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_TOTAL_EXCLUDING_TAX'); ?></th>
     <th class="dataTableHeadingContent" align="right"><?php echo sysLanguage::get('TABLE_HEADING_TOTAL_INCLUDING_TAX'); ?></th>
    </tr>
   </thead>
   <tfoot>
    <tr>
     <td align="right" colspan="9"><table border="0" cellspacing="0" cellpadding="2" id="orderTotals">
     </table></td>
    </tr>
    <tr>
     <td align="right" colspan="9"><input id="customTotal" type="button" value="Custom Order Total" style="display:none"></td>
    </tr>
   </tfoot>
   <tbody>
   </tbody>
  </table></td>
 </tr>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>
 <tr>
  <td class="main"><br><b><?php echo sysLanguage::get('TABLE_HEADING_COMMENTS'); ?></b></td>
 </tr>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '5'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_textarea_field('comments', 'soft', '60', '5'); ?></td>
 </tr>
 <tr>
  <td><?php echo tep_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
 </tr>
 <tr>
  <td><table border="0" cellspacing="0" cellpadding="2">
   <tr>
    <td><table border="0" cellspacing="0" cellpadding="2">
     <tr>
      <td class="main"><b><?php echo sysLanguage::get('ENTRY_STATUS'); ?></b> <?php echo tep_draw_pull_down_menu('status', $orders_statuses, $_SESSION['order']->info['orders_status']); ?></td>
     </tr>
     <tr>
      <td class="main"><b><?php echo sysLanguage::get('ENTRY_NOTIFY_CUSTOMER'); ?></b> <?php echo tep_draw_checkbox_field('notify', '', true, 'id="notify"'); ?></td>
     </tr>
    </table></td>
   </tr>
  </table></td>
 </tr>
 <tr>
  <td colspan="2" align="right"><input type="button" value="Send Order Email" id="sendEmail">&nbsp;<input type="button" value="Update Order And Send Email" id="updateAndSend">&nbsp;<?php echo htmlBase::newElement('button')->usePreset('save')->setId('orderInsertButton')->setType('submit')->draw(); ?>&nbsp;<?php echo htmlBase::newElement('button')->usePreset('back')->setHref(tep_href_link(FILENAME_ORDERS, tep_get_all_get_params(array('action'))))->draw(); ?></td>
 </tr>
</table>
</form>
<?php
 $Qcustomers = tep_db_query('select * from ' . TABLE_CUSTOMERS . ' order by customers_lastname');
 $customersArray = array(
     array(
         'id' => 'none',
         'text' => 'Please Select A Customer'
     )
 );
?>
<div id="selectCustomerWindow" title="Select A Customer" style="display:none">
 <div style="position:absolute;float:left;top:33px;left:225px;height:20px;font-size:10px;color:red;font-family:Tahoma;display:none;" id="tooManyResults">Over 100 Results</div>
 <table cellpadding="3" cellspacing="0" border="0" id="contentTable">
  <tr>
   <td><fieldset id="searchCustomerFieldset">
    <legend>Search For Customer</legend>
    <table cellpadding="3" cellspacing="0" border="0" id="searchCustomerTable">
     <tr>
      <td class="main">By Name:</td>
      <td><?php echo tep_draw_input_field('customers_name_search', '', 'id="customers_name_search"');?></td>
     </tr>
     <tr>
      <td class="main">By Email:</td>
      <td><?php echo tep_draw_input_field('customers_email_search', '', 'id="customers_email_search"');?></td>
     </tr>
    </table>
   </fieldset></td>
  </tr>
  <tr style="display:none;">
   <td align="center"><fieldset id="newCustomerFieldset">
    <legend>Create New Customer</legend>
    <input type="button" value="Create New Customer" id="newCustomerButton">
    <form id="form_newCustomerTable"><table cellpadding="0" cellspacing="3" border="0" id="newCustomerTable" style="display:none">
     <tr>
      <td class="main">First Name:</td>
      <td><?php echo tep_draw_input_field('firstname', '', 'id="firstname"');?></td>
     </tr>
     <tr>
      <td class="main">Last Name:</td>
      <td><?php echo tep_draw_input_field('lastname', '', 'id="lastname"');?></td>
     </tr>
     <tr>
      <td class="main">Email Address:</td>
      <td><?php echo tep_draw_input_field('email_address', '', 'id="email_address"');?></td>
     </tr>
     <tr>
      <td class="main">Company:</td>
      <td><?php echo tep_draw_input_field('company', '', 'id="company"');?></td>
     </tr>
     <tr>
      <td class="main">Street Address:</td>
      <td><?php echo tep_draw_input_field('street_address', '', 'id="street_address"');?></td>
     </tr>
     <tr>
      <td class="main">Postcode:</td>
      <td><?php echo tep_draw_input_field('postcode', '', 'id="postcode"');?></td>
     </tr>
     <tr>
      <td class="main">City:</td>
      <td><?php echo tep_draw_input_field('city', '', 'id="city"');?></td>
     </tr>
     <tr>
      <td class="main">State:</td>
      <td><?php echo tep_draw_input_field('state', '', 'id="state"');?></td>
     </tr>
     <tr>
      <td class="main">Country:</td>
      <td><?php echo tep_get_country_list('country', '', 'id="country"');?></td>
     </tr>
     <tr>
      <td class="main">Telephone:</td>
      <td><?php echo tep_draw_input_field('telephone', '', 'id="telephone"');?></td>
     </tr>
    </table></form>
   </fieldset></td>
  </tr>
 </table>
</div>
<div id="selectProductWindow" title="Add Product(s) To The Order" style="display:none">
 <form id="form_selectProductTable">
 <table cellpadding="3" cellspacing="0" border="0" id="contentTable">
  <tr>
   <td colspan="2" class="main"><u><b>Select Product</b></u></td>
  </tr>
  <tr>
   <td valign="top"><select name="products_id" id="products" size="20"></select></td>
   <td valign="top" class="main"><div id="productsAttribs"></div><br>Quantity: <input type="text" size="4" name="qty" id="qty" value="1"><br><br>Use this button to continue adding products.<br><input id="selectProductWindow_addMultiple" type="button" value="Add And Continue"></td>
  </tr>
 </table>
 </form>
</div>
<div id="processPaymentWindow" style="display:none" align="center">
 <table cellpadding="3" cellspacing="0" border="0" width="100%" id="processPaymentWindow_header">
  <tr>
   <td class="infoBoxHeading"><b>Process Payment</b></td>
   <td class="infoBoxHeading" align="right"><label id="processPaymentWindow_close">[X]</label></td>
  </tr>
 </table>
</div>
<div id="ajaxLoaderWindow" title="Ajax Operation"></div>
<div id="orderLoaderWindow" title="Loading Order"></div>
<div id="addressEditWindow" title="Edit Address"></div>