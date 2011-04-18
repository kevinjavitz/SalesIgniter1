<script language="javascript"><!--
function CVVPopUpWindow(url) {
	window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,width=600,height=233,screenX=150,screenY=150,top=150,left=150')
}

function CVVPopUpWindowEx(url) {
	window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,copyhistory=no,width=600,height=510,screenX=150,screenY=150,top=150,left=150')
}

var onePage = checkout;
onePage.initializing = true;
onePage.ajaxCharset = '<?php echo sysLanguage::getCharset();?>';
onePage.loggedIn = <?php echo ($userAccount->isLoggedIn() === true ? 'true' : 'false');?>;
onePage.ccgvInstalled = <?php echo (MODULE_ORDER_TOTAL_COUPON_STATUS == 'True' ? 'true' : 'false');?>;
onePage.onlyReservations = <?php echo (Session::get('onlyReservations') === true ? 'true' : 'false');?>;
onePage.newRentAccount = <?php echo ($onePageCheckout->isMembershipCheckout() === true ? 'true' : 'false');?>;
onePage.shippingEnabled = <?php echo ($onePageCheckout->onePage['shippingEnabled'] === true ? 'true' : 'false');?>;
onePage.pickupEnabled = <?php echo ($onePageCheckout->onePage['pickupEnabled'] === true ? 'true' : 'false');?>;
onePage.loadingMessageMethod = '<?php echo ONEPAGE_CHECKOUT_LOADING_MESSAGE_METHOD;?>';
onePage.pageLinks = {
	checkout: '<?php echo fixSeoLink(itw_app_link(($onePageCheckout->isMembershipCheckout() === true ? 'checkoutType=rental&' : '') . 'rType=ajax', 'checkout', 'default', $request_type));?>',
	shoppingCart: '<?php echo fixSeoLink(itw_app_link(null, 'shoppingCart', 'default'));?>'
}

function getFieldErrorCheck($element){
	var rObj = {};
	switch($element.attr('name')){
		case 'billing_firstname':
		case 'shipping_firstname':
		case 'pickup_firstname':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_FIRST_NAME_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_FIRST_NAME_ERROR');?>';
		break;
		case 'billing_lastname':
		case 'shipping_lastname':
		case 'pickup_lastname':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_LAST_NAME_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_LAST_NAME_ERROR');?>';
		break;
		case 'billing_email_address':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_EMAIL_ADDRESS_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_EMAIL_ADDRESS_ERROR');?>';
		break;
		case 'billing_street_address':
		case 'shipping_street_address':
		case 'pickup_street_address':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_STREET_ADDRESS_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_STREET_ADDRESS_ERROR');?>';
		break;
		case 'billing_postcode':
		case 'shipping_postcode':
		case 'pickup_postcode':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_POSTCODE_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_POST_CODE_ERROR');?>';
		break;
		case 'billing_city':
		case 'shipping_city':
		case 'pickup_city':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_CITY_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_CITY_ERROR');?>';
		break;
		case 'billing_dob':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_DOB_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_DATE_OF_BIRTH_ERROR');?>';
		break;
		case 'billing_telephone':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_TELEPHONE_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_TELEPHONE_NUMBER_ERROR');?>';
		break;
		case 'billing_country':
		case 'shipping_country':
		case 'pickup_country':
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_COUNTRY_ERROR');?>';
		break;
		case 'billing_state':
		case 'shipping_state':
		case 'pickup_state':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_STATE_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_STATE_ERROR');?>';
		break;
		case 'password':
		case 'confirmation':
		rObj.minLength = <?php echo sysConfig::get('ENTRY_PASSWORD_MIN_LENGTH');?>;
		rObj.errMsg = '<?php echo sysLanguage::get('ENTRY_PASSWORD_ERROR');?>';
		break;
	}
	return rObj;
}

$(document).ready(function (){
	$('#pageContentContainer').show();
	var loginBoxOpened = false;
	$('#loginButton').click(function (){
		if (loginBoxOpened){
			$('#loginBox').dialog('open');
			return false;
		}
		$('#loginBox').dialog({
			resizable: false,
			shadow: false,
			open: function (){
				var $dialog = this;
				$('input', $dialog).keypress(function (e){
					if (e.which == 13){
						$('#loginWindowSubmit', $dialog).click();
					}
				});

				$('#loginWindowSubmit', $dialog).hover(function (){
					this.style.cursor = 'pointer';
				}, function (){
					this.style.cursor = 'default';
				}).click(function (){
					var $this = $(this);
					$this.hide();
					var email = $('input[name="email_address"]', $dialog).val();
					var pass = $('input[name="password"]', $dialog).val();
					onePage.queueAjaxRequest({
						url: onePage.pageLinks.checkout,
						data: 'action=processLogin&email=' + email + '&pass=' + pass,
						dataType: 'json',
						type: 'post',
						beforeSend: function (){
							onePage.showAjaxMessage('Refreshing Shopping Cart');
							if ($('#loginStatus', $this.parent()).size() <= 0){
								$('<div>')
								.attr('id', 'loginStatus')
								.html('Processing Login')
								.attr('align', 'center')
								.insertAfter($this);
							}
						},
						success: function (data){
							if (data.success == true){
								$('#loginStatus', $dialog).html(data.msg);
								$('#logInRow').hide();

								$('#changeBillingAddressTable').show();
								$('#changeShippingAddressTable').show();
								$('#changePickupAddressTable').show();
								$('#newAccountEmail').remove();
								$('#diffShipping').parent().parent().parent().remove();

								onePage.updateAddressHTML('billing');
								onePage.updateAddressHTML('shipping');
								onePage.updateAddressHTML('pickup');
								if (onePage.shippingEnabled){
									$('#shippingAddress').show();
								}
								if (onePage.pickupEnabled){
									$('#pickupAddress').show();
								}
								var updateTotals = true;
								onePage.updateCartView();

								if (onePage.newRentAccount == false){
									onePage.updateFinalProductListing();
								}

								onePage.updatePaymentMethods();
								if ($(':radio[name="payment"]:checked').size() > 0){
									onePage.setPaymentMethod($(':radio[name="payment"]:checked'));
									updateTotals = false;
								}
								onePage.updateShippingMethods();
								if ($(':radio[name="shipping"]:checked').size() > 0){
									onePage.setShippingMethod($(':radio[name="shipping"]:checked').val());
									updateTotals = false;
								}

								if (updateTotals == true){
									onePage.updateOrderTotals();
								}

								$('#loginBox').dialog('destroy');
							}else{
								$('#logInRow').show();
								$('#loggedInRow').hide();

								$('#loginStatus', $dialog).html(data.msg);
								setTimeout(function (){
									$('#loginStatus').remove();
									$('#loginWindowSubmit').show();
								}, 6000);
								setTimeout(function (){
									$('#loginStatus').html('Try again in 3');
								}, 3000);
								setTimeout(function (){
									$('#loginStatus').html('Try again in 2');
								}, 4000);
								setTimeout(function (){
									$('#loginStatus').html('Try again in 1');
								}, 5000);
							}
						},
						errorMsg: 'There was an error logging in, please inform IT Web Experts about this error.'
					});
				});
			}
		});
		loginBoxOpened = true;
		return false;
	});

	$('#changeBillingAddress, #changeShippingAddress, #changePickupAddress').click(function (){
		var addressType = 'billing';
		if ($(this).attr('id') == 'changeShippingAddress'){
			addressType = 'shipping';
		}
		if ($(this).attr('id') == 'changePickupAddress'){
			addressType = 'pickup';
		}
		$('#addressBook').clone().show().appendTo(document.body).dialog({
			shadow: false,
			width: 550,
			// height: 450,
			minWidth: 550,
			//minHeight: 500,
			open: function (e, ui){
				onePage.loadAddressBook($(this), addressType);
			},
			buttons: {
				'<?php echo addslashes(sysLanguage::get('WINDOW_BUTTON_CANCEL'));?>': {
					id: 'addressCancelButton',
					click: function (){
						$('#addressNewButton, #addressEditButton').show();
						var self = $(this);
						var action = $('input[name="action"]', self).val();
						//alert($(':input, :select, :radio, :checkbox', this).serialize());
						if (action == 'selectAddress'){
							self.dialog('close');
						}else if (action == 'addNewAddress' || action == 'saveAddress'){
							onePage.loadAddressBook(self, addressType);
						}
					}
				},
				'<?php echo addslashes(sysLanguage::get('WINDOW_BUTTON_CONTINUE'));?>': {
					id: 'addressContinueButton',
					click: function (){
						var $this = $(this);
						var action = $('input[name="action"]', $this).val();
						//alert($(':input, :select, :radio, :checkbox', this).serialize());
						if (action == 'selectAddress'){
							onePage.queueAjaxRequest({
								url: onePage.pageLinks.checkout,
								beforeSendMsg: 'Setting Address',
								dataType: 'json',
								data: $(':input, :radio', this).serialize(),
								type: 'post',
								success: function (data){
									$this.dialog('close');
									if (addressType == 'shipping'){
										onePage.updateAddressHTML('shipping');
										onePage.updateShippingMethods();
									}else if (addressType == 'billing'){
										onePage.updateAddressHTML('billing');
										onePage.updatePaymentMethods();
									}else{
										onePage.updateAddressHTML('pickup');
									}
								},
								errorMsg: 'There was an error saving your address, please inform IT Web Experts about this error.'
							});
						}else if (action == 'addNewAddress'){
							onePage.queueAjaxRequest({
								url: onePage.pageLinks.checkout,
								beforeSendMsg: 'Saving New Address',
								dataType: 'json',
								data: $(':input, select, :radio, :checkbox', this).serialize(),
								type: 'post',
								success: function (data){
									onePage.loadAddressBook($this, addressType);
									$('#addressNewButton, #addressEditButton').show();
								},
								errorMsg: 'There was an error saving your address, please inform IT Web Experts about this error.'
							});
						}else if (action == 'saveAddress'){
							onePage.queueAjaxRequest({
								url: onePage.pageLinks.checkout,
								beforeSendMsg: 'Updating Address',
								dataType: 'json',
								data: $(':input, select, :radio, :checkbox', this).serialize(),
								type: 'post',
								success: function (data){
									onePage.loadAddressBook($this, addressType);
									$('#addressNewButton, #addressEditButton').show();
								},
								errorMsg: 'There was an error saving your address, please inform IT Web Experts about this error.'
							});
						}
					}
				},
				'<?php echo addslashes(sysLanguage::get('WINDOW_BUTTON_NEW_ADDRESS'));?>': {
					id: 'addressNewButton',
					click: function (){
						$('#addressNewButton, #addressEditButton').hide();
						var self = $(this);
						showAjaxLoader(self.parent(), 'xlarge');

						onePage.queueAjaxRequest({
							url: onePage.pageLinks.checkout,
							data: 'action=getNewAddressForm',
							type: 'post',
							beforeSendMsg: 'Loading New Address Form',
							success: function (data){
								hideAjaxLoader(self.parent());
								self.html(data);
								onePage.addCountryAjax($('select[name="country"]', self), 'state', 'stateCol')
							},
							errorMsg: 'There was an error loading new address form, please inform IT Web Experts about this error.'
						});
					}
				},
				'<?php echo addslashes(sysLanguage::get('WINDOW_BUTTON_EDIT_ADDRESS'));?>': {
					id: 'addressEditButton',
					click: function (){
						$('#addressNewButton, #addressEditButton').hide();
						var self = $(this);
						showAjaxLoader($('.ui-dialog-content', self.element).parent(), 'xlarge');
						onePage.queueAjaxRequest({
							url: onePage.pageLinks.checkout,
							data: 'action=getEditAddressForm&addressID=' + $(':radio[name="address"]:checked', self).val(),
							type: 'post',
							beforeSendMsg: 'Loading Edit Address Form',
							success: function (data){
								$('.ui-dialog-content', self.element).html(data);
								hideAjaxLoader($('.ui-dialog-content', self.element).parent());
							},
							errorMsg: 'There was an error loading edit address form, please inform IT Web Experts about this error.'
						});
					}
				}
			}
		});
		return false;
	});

	onePage.initCheckout();
});
//-->
</script>