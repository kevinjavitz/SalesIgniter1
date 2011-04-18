var submitter = null;
function submitFunction() {
	submitter = 1;
}

function parseDataToTable(data, $table){
	var $tBody = $table.find('tbody');
	$tBody.empty();
	for(var i=0; i<data.length; i++){
		var $newTr = $('<tr></tr>');
		for(var j=0; j<data[i].length; j++){
			if (data[i][j].text){
				$newTr.append($('<td class="main" align="' + data[i][j].align + '"></td>').append(data[i][j].text));
			}else{
				$newTr.append($('<td class="main"></td>').append(data[i][j]));
			}
		}
		$tBody.append($newTr);
	}
}

var errCSS = {
	'border-color': 'red',
	'border-style': 'solid'
};

var checkout = {
	charset: 'UTF-8',
	pageLinks: {},
	paymentSelected: false,
	fieldSuccessHTML: '<div style="margin-left:3px;margin-top:1px;float:left;" class="ui-icon ui-icon-circle-check success_icon"></div>',
	fieldErrorHTML: '<div style="margin-left:3px;margin-top:1px;float:left;" class="ui-icon ui-icon-circle-close error_icon"></div>',
	fieldRequiredHTML: '<div style="margin-left:3px;margin-top:1px;float:left;" class="ui-icon ui-icon-gear required_icon"></div>',
	showAjaxLoader: function (){
		$('#ajaxLoader').show();
	},
	hideAjaxLoader: function (){
		$('#ajaxLoader').hide();
	},
	showAjaxMessage: function (message){
		if (this.loadingMessageMethod == 'Dialog'){
			$('#ajaxMessages').dialog({
				autoOpen: true,
				allowClose: false,
				modal: true,
				open: function (){
					$('.message', this).html(message);
				}
			});
		}else{
			$('#checkoutButtonContainer').hide();
			$('#ajaxMessages').show().html(message);
		}
	},
	hideAjaxMessage: function (){
		if (this.loadingMessageMethod == 'Dialog'){
			$('#ajaxMessages').dialog('close');
		}else{
			$('#checkoutButtonContainer').show();
			$('#ajaxMessages').hide();
		}
	},
	fieldErrorCheck: function ($element, forceCheck, hideIcon){
		forceCheck = forceCheck || false;
		hideIcon = hideIcon || false;
		var errMsg = this.checkFieldForErrors($element, forceCheck);
		if (hideIcon == false){
			if (errMsg != false){
				this.addIcon($element, 'error', errMsg);
				return true;
			}else{
				this.addIcon($element, 'success', errMsg);
			}
		}else{
			if (errMsg != false){
				return true;
			}
		}
		return false;
	},
	checkFieldForErrors: function ($element, forceCheck){
		var hasError = false;
		if ($element.is(':visible') && ($element.hasClass('required') || forceCheck == true)){
			var errCheck = getFieldErrorCheck($element);
			if (!errCheck.errMsg){
				return false;
			}

			switch($element.attr('type')){
				case 'password':
				if ($element.attr('name') == 'password'){
					if ($element.val().length < errCheck.minLength){
						hasError = true;
					}
				}else{
					if ($element.val() != $(':password[name="password"]').val() || $element.val().length <= 0){
						hasError = true;
					}
				}
				break;
				case 'radio':
				if ($(':radio[name="' + $element.attr('name') + '"]:checked').size() <= 0){
					hasError = true;
				}
				break;
				case 'checkbox':
				if ($(':checkbox[name="' + $element.attr('name') + '"]:checked').size() <= 0){
					hasError = true;
				}
				break;
				case 'select-one':
				if ($element.val() == ''){
					hasError = true;
				}
				break;
				default:
				if ($element.val().length < errCheck.minLength){
					hasError = true;
				}
				break;
			}
			if (hasError == true){
				return errCheck.errMsg;
			}
		}
		return hasError;
	},
	addIcon: function ($curField, iconType, title){
		title = title || false;
		$('.success_icon, .error_icon, .required_icon', $curField.parent()).hide();
		switch(iconType){
			case 'error':
			if (this.initializing == true){
				this.addRequiredIcon($curField, 'Required');
			}else{
				this.addErrorIcon($curField, title);
			}
			break;
			case 'success':
			this.addSuccessIcon($curField, title);
			break;
			case 'required':
			this.addRequiredIcon($curField, 'Required');
			break;
		}
	},
	addSuccessIcon: function ($curField, title){
		if ($('.success_icon', $curField.parent()).size() <= 0){
			$curField.parent().append(this.fieldSuccessHTML);
		}
		$('.success_icon', $curField.parent()).attr('title', title).show();
	},
	addErrorIcon: function ($curField, title){
		if ($('.error_icon', $curField.parent()).size() <= 0){
			$curField.parent().append(this.fieldErrorHTML);
		}
		$('.error_icon', $curField.parent()).attr('title', title).show();
	},
	addRequiredIcon: function ($curField, title){
		if ($curField.hasClass('required')){
			if ($('.required_icon', $curField.parent()).size() <= 0){
				$curField.parent().append(this.fieldRequiredHTML);
			}
			$('.required_icon', $curField.parent()).attr('title', title).show();
		}
	},
	clickButton: function (elementName){
		if ($(':radio[name="' + elementName + '"]').size() <= 0){
			var $el = $('input[name="' + elementName + '"]');
		}else{
			var $el = $(':radio[name="' + elementName + '"]:checked');
		}
		$el.trigger('click', true);
	},
	queueAjaxRequest: function (options){
		var checkoutClass = this;
		var o = {
			url: options.url,
			cache: options.cache || false,
			dataType: options.dataType || 'html',
			type: options.type || 'GET',
			contentType: options.contentType || 'application/x-www-form-urlencoded; charset=' + this.ajaxCharset,
			data: options.data || false,
			beforeSend: options.beforeSend || function (){
				checkoutClass.showAjaxMessage(options.beforeSendMsg || 'Ajax Operation, Please Wait...');
				checkoutClass.showAjaxLoader();
			},
			complete: function (){
				checkoutClass.hideAjaxMessage();
				if (document.ajaxq.q['orderUpdate'].length <= 0){
					checkoutClass.hideAjaxLoader();
				}
			},
			success: options.success,
			error: function (XMLHttpRequest, textStatus, errorThrown){
				if (XMLHttpRequest.responseText == 'session_expired') document.location = this.pageLinks.shoppingCart;
				alert((options.errorMsg || 'There was an ajax error, please contact IT Web Experts for support.') + "\n" + textStatus + "\n" + errorThrown);
			}
		};
		$.ajaxq('orderUpdate', o);
	},
	updateAddressHTML: function (type){
		var checkoutClass = this;
		var strType;

		if (type == 'shipping'){
			strType = 'getShippingAddress';
		}else if (type == 'billing'){
			strType = 'getBillingAddress';
		}else{
			strType = 'getPickupAddress';
		}

		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			data: 'action=' + strType,
			type: 'post',
			beforeSendMsg: 'Updating ' + strType + ' Address',
			success: function (data){
				$('#' + type + 'Address').html(data);
			},
			errorMsg: 'There was an error loading your ' + type + ' address, please inform IT Web Experts about this error.'
		});
	},
	updateCartView: function (){
		if (this.onlyReservations == true || this.newRentAccount == true){
			return false;
		}

		var checkoutClass = this;
		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			data: 'action=updateCartView',
			type: 'post',
			dataType: 'json',
			beforeSendMsg: 'Refreshing Shopping Cart',
			success: function (data){
				if (data == 'none'){
					document.location = checkoutClass.pageLinks.shoppingCart;
				}else{
					parseDataToTable(data.productRows, $('#shoppingCart table:eq(0)'));
				}
			},
			errorMsg: 'There was an error refreshing the shopping cart, please inform IT Web Experts about this error.'
		});
	},
	updateFinalProductListing: function (){
		if (this.newRentAccount == true){
			return false;
		}

		var checkoutClass = this;
		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			data: 'action=getProductsFinal',
			dataType: 'json',
			beforeSendMsg: 'Refreshing Final Product Listing',
			success: function (data){
				if (data){
					parseDataToTable(data.productRows, $('.finalProducts table:eq(0)'));
				}
			},
			errorMsg: 'There was an error refreshing the final products listing, please inform IT Web Experts about this error.'
		});
	},
	updateOrderTotals: function (){
		var checkoutClass = this;
		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			cache: false,
			data: 'action=getOrderTotals',
			type: 'post',
			beforeSendMsg: 'Updating Order Totals',
			dataType: 'json',
			success: function (data){
				if (data.totals.length > 0){
					parseDataToTable(data.totals, $('.orderTotals'));
				}
				checkoutClass.hideAjaxLoader();
			},
			errorMsg: 'There was an error refreshing the shopping cart, please inform IT Web Experts about this error.'
		});
	},
	setVoucherPayment: function (tORf, covers){
		var checkoutClass = this;
		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			cache: false,
			data: 'action=setVoucherPayment&flag=' + (tORf === true ? 'true' : 'false') + '&covers=' + (covers === true ? 'true' : 'false'),
			type: 'post',
			beforeSendMsg: 'Setting Voucher Payment',
			success: function (data){
				checkoutClass.hideAjaxLoader();
			},
			errorMsg: 'There was an error refreshing the shopping cart, please inform IT Web Experts about this error.'
		});
	},
	updateModuleMethods: function (action, noOrdertotalUpdate){
		var checkoutClass = this;
		var actionText = (action == 'shipping' ? 'Shipping' : 'Payment');
		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			data: 'action=update' + actionText + 'Methods',
			type: 'post',
			beforeSendMsg: 'Updating ' + actionText + ' Methods',
			success: function (data){
				$('#no' + actionText + 'Address').hide();
				$('#' + action + 'Methods').html(data);
				$('#' + action + 'Methods').show();

				checkoutClass.clickButton(action);
				if (action == 'payment'){
					$('#voucherPayment').click(function (){
						if ($(this).hasClass('coversAll') && this.checked === true){
							$('input[name="payment"]').each(function (){
								this.checked = false;
							});
						}
						checkoutClass.setVoucherPayment(this.checked, $(this).hasClass('coversAll'));
						checkoutClass.updateOrderTotals();
					});
				}
			},
			errorMsg: 'There was an error updating ' + action + ' methods, please inform IT Web Experts about this error.'
		});
	},
	updateShippingMethods: function (noOrdertotalUpdate){
		if (this.shippingEnabled == false){
			return false;
		}

		this.updateModuleMethods('shipping', noOrdertotalUpdate);
	},
	updatePaymentMethods: function (noOrdertotalUpdate){
		if (this.paymentSelected) return;
		this.updateModuleMethods('payment', noOrdertotalUpdate);
	},
	setModuleMethod: function (type, method, successFunction){
		var checkoutClass = this;
		var actionText = (type == 'shipping' ? 'Shipping' : 'Payment');
		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			data: 'action=set' + actionText + 'Method&method=' + method,
			type: 'post',
			beforeSendMsg: 'Setting ' + actionText + ' Method',
			dataType: 'json',
			success: successFunction,
			errorMsg: 'There was an error setting ' + type + ' method, please inform IT Web Experts about this error.'
		});
	},
	setShippingMethod: function (method){
		if (this.shippingEnabled == false){
			return false;
		}

		var checkoutClass = this;
		this.setModuleMethod('shipping', method, null);
	},
	setPaymentMethod: function ($button){
		var checkoutClass = this;
		this.setModuleMethod('payment', $button.val(), function (data){
			$('.paymentFields').remove();
			
			var $newTr = $('<tr></tr>').addClass('paymentFields');
			$('<td width="10">&nbsp;</td>').appendTo($newTr)
			$('<td width="10">&nbsp;</td>').appendTo($newTr)
			var $newTd = $('<td colspan="4"></td>').appendTo($newTr);
			if (data.inputFields.length > 0){
				var $table = $('<table></table>')
				.attr('cellpadding', 3)
				.attr('cellspacing', 0)
				.css('width', '500px')
				.append($('<tbody></tbody>'));
				parseDataToTable(data.inputFields, $table);
				
				$table.appendTo($newTd);
				$newTr.insertAfter($button.parent().parent());
			}
		
			checkoutClass.paymentSelected = true;
		});
	},
	loadAddressBook: function ($dialog, type){
		showAjaxLoader($dialog.parent(), 'xlarge');
		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			data: 'action=getAddressBook&addressType=' + type,
			type: 'post',
			beforeSendMsg: 'Loading Address Book',
			success: function (data){
				$dialog.html(data);
				hideAjaxLoader($dialog.parent(), 'xlarge');
			},
			errorMsg: 'There was an error loading your address book, please inform IT Web Experts about this error.'
		});
	},
	addCountryAjax: function ($input, fieldName, stateCol){
		var checkoutClass = this;
		$input.change(function (event, callBack){
			if ($(this).hasClass('required')){
				if ($(this).val() != '' && $(this).val() > 0){
					checkoutClass.addIcon($(this), 'success');
				}
			}
			var thisName = $(this).attr('name');
			var $origStateField = $('*[name="' + fieldName + '"]', $('#' + stateCol));
			checkoutClass.queueAjaxRequest({
				url: checkoutClass.pageLinks.checkout,
				data: 'action=countrySelect&fieldName=' + fieldName + '&cID=' + $(this).val() + '&curValue=' + $origStateField.val(),
				type: 'post',
				beforeSendMsg: 'Getting Country\'s Zones',
				success: function (data){
					$('#' + stateCol).html(data);
					var $curField = $('*[name="' + fieldName + '"]', $('#' + stateCol));

					if ($curField.hasClass('required')){
						if (checkoutClass.fieldErrorCheck($curField, true, true) == false){
							checkoutClass.addIcon($curField, 'success');
						}else{
							checkoutClass.addIcon($curField, 'required');
						}
					}

					if ($curField.attr('type') == 'select-one'){
						$curField.change(function (){
							if ($(this).hasClass('required')){
								if (checkoutClass.fieldErrorCheck($(this)) == false){
									if (thisName == 'shipping_country'){
										checkoutClass.processShippingAddress();
									}else if (thisName == 'billing_country'){
										checkoutClass.processBillingAddress();
									}else if (thisName == 'pickup_country'){
										checkoutClass.processPickupAddress();
									}
								}
							}else{
								if (thisName == 'shipping_country'){
									checkoutClass.processShippingAddress();
								}else if (thisName == 'billing_country'){
									checkoutClass.processBillingAddress();
								}else if (thisName == 'pickup_country'){
									checkoutClass.processPickupAddress();
								}
							}
						});
					}else{
						$curField.blur(function (){
							if ($(this).hasClass('required')){
								if (checkoutClass.fieldErrorCheck($(this)) == false){
									if (thisName == 'shipping_country'){
										checkoutClass.processShippingAddress();
									}else if (thisName == 'billing_country'){
										checkoutClass.processBillingAddress();
									}else if (thisName == 'pickup_country'){
										checkoutClass.processPickupAddress();
									}
								}
							}else{
								if (thisName == 'shipping_country'){
									checkoutClass.processShippingAddress();
								}else if (thisName == 'billing_country'){
									checkoutClass.processBillingAddress();
								}else if (thisName == 'pickup_country'){
									checkoutClass.processPickupAddress();
								}
							}
						});
					}

					if (callBack){
						callBack.call();
					}
				},
				errorMsg: 'There was an error getting states, please inform IT Web Experts about this error.'
			});
		});
	},
	processBillingAddress: function (){
		var hasError = false;
		var checkoutClass = this;
		$('select[name="billing_country"], input[name="billing_street_address"], input[name="billing_postcode"], input[name="billing_city"]', $('#billingAddress')).each(function (){
			if (checkoutClass.fieldErrorCheck($(this), false, true) == true){
				hasError = true;
			}
		});
		if (hasError == true){
			return;
		}

		this.setBillTo();

		if ($('#diffShipping:checked').size() <= 0){
			this.setSendTo(false);
		}else{
			this.setSendTo(true);
		}

		this.updateCartView();
		this.updateFinalProductListing();
		this.updatePaymentMethods(true);
		this.updateShippingMethods(true);
		this.updateOrderTotals();
	},
	processShippingAddress: function (){
		var hasError = false;
		var checkoutClass = this;
		$('select[name="delivery_country"], input[name="delivery_street_address"], input[name="delivery_postcode"], input[name="delivery_city"]', $('#deliveryAddress')).each(function (){
			if (checkoutClass.fieldErrorCheck($(this), false, true) == true){
				hasError = true;
			}
		});
		if (hasError == true){
			return;
		}

		this.setSendTo(true);
		if (this.shippingEnabled == true){
			this.updateShippingMethods(true);
		}
		this.updateOrderTotals();
	},
	processPickupAddress: function (){
		var hasError = false;
		var checkoutClass = this;
		$('select[name="pickup_country"], input[name="pickup_street_address"], input[name="pickup_postcode"], input[name="pickup_city"]', $('#pickupAddress')).each(function (){
			if (checkoutClass.fieldErrorCheck($(this), false, true) == true){
				hasError = true;
			}
		});
		if (hasError == true){
			return;
		}
		this.setPickUp();
		if (this.pickupEnabled == true){

		}
		this.updateOrderTotals();
	},
	setCheckoutAddress: function (type, useShipping){
		var selector = '#' + type + 'Address';
		var strType;
		if (type == 'shipping' ){
			strType = 'Shipping';
		}else if (type == 'billing'){
			strType =  'Billing';
		}else{
			strType =  'Pickup';
		}


		var sendMsg = 'Setting ' + strType + ' Address';
		var errMsg = type + ' address';
		if (type == 'shipping' && useShipping == false){
			selector = '#billingAddress';
			sendMsg = 'Setting Shipping Address';
			errMsg = 'billing address';
		}

		action = 'setBillTo';
		if (type == 'shipping'){
			action = 'setSendTo';
		}
		if (type == 'pickup'){
			action = 'setPickUp';
		}

		this.queueAjaxRequest({
			url: this.pageLinks.checkout,
			beforeSendMsg: sendMsg,
			dataType: 'json',
			data: 'action=' + action + '&' + $('*', $(selector)).serialize(),
			type: 'post',
			success: function (){
			},
			errorMsg: 'There was an error updating your ' + errMsg + ', please inform IT Web Experts about this error.'
		});
	},
	setBillTo: function (){
		this.setCheckoutAddress('billing', false);
	},
	setSendTo: function (useShipping){
		this.setCheckoutAddress('shipping', useShipping);
	},
	setPickUp: function (){
		this.setCheckoutAddress('pickup', false);
	},
	initCheckout: function (){
		var checkoutClass = this;

		if (this.loggedIn == false){
			$('#shippingAddress').hide();
			$('#pickupAddress').hide();
			$('#shippingMethods').html('');
		}

		$('#checkoutNoScript').remove();
		$('#checkoutYesScript').show();

		if (this.newRentAccount == false){
			this.updateFinalProductListing();
		}
		this.updateOrderTotals();

		$('#diffShipping').click(function (){
			if (this.checked){
				$('#shippingAddress').show();
				$('#shippingMethods').html('');
				$('#noShippingAddress').show();
				$('select[name="shipping_country"]').trigger('change');
			}else{
				$('#shippingAddress').hide();
				var errCheck = checkoutClass.processShippingAddress();
				if (errCheck == ''){
					$('#noShippingAddress').hide();
				}else{
					$('#noShippingAddress').show();
				}
			}
		});

		$('#diffPickup').click(function (){
			if (this.checked){
				$('#pickupAddress').show();
				$('select[name="pickup_country"]').trigger('change');
			}else{
				$('#pickupAddress').hide();
			}
		});

		if (this.loggedIn == true){
			$('#voucherPayment').click(function (){
				if ($(this).hasClass('coversAll') && this.checked === true){
					$('input[name="payment"]').each(function (){
						this.checked = false;
					});
				}
				checkoutClass.setVoucherPayment(this.checked, $(this).hasClass('coversAll'));
				checkoutClass.updateOrderTotals();
			});
		}

		if ($('#paymentMethods').is(':visible')){
			this.clickButton('payment');
		}

		if (this.shippingEnabled == true){
			if ($('#shippingMethods').is(':visible')){
				this.clickButton('shipping');
			}
		}

		$('input, :password', $('#billingAddress')).each(function (){
			if ($(this).attr('name') != undefined && $(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio'){
				if ($(this).attr('type') == 'password'){
					$(this).blur(function (){
						if ($(this).hasClass('required')){
							checkoutClass.fieldErrorCheck($(this));
						}
					});
					/* Used to combat firefox 3 and it's auto-populate junk */
					//$(this).val('');

					if ($(this).attr('name') == 'password'){
						$(this).focus(function (){
							$(':password[name="confirmation"]').val('');
						});

						var rObj = getFieldErrorCheck($(this));
						$(this).pstrength({
							addTo: '#pstrength_password',
							minchar: rObj.minLength
						});
					}
				}else{
					$(this).blur(function (){
						if ($(this).hasClass('required')){
							checkoutClass.fieldErrorCheck($(this));
						}
					});
				}

				if ($(this).hasClass('required')){
					if (checkoutClass.fieldErrorCheck($(this), true, true) == false){
						checkoutClass.addIcon($(this), 'success');
					}else{
						checkoutClass.addIcon($(this), 'required');
					}
				}
			}
		});
		
		$('select, input', $('#shippingAddress')).each(function (){
			if ($(this).attr('name') != undefined && $(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio'){
				$(this).blur(function (){
					if (checkoutClass.fieldErrorCheck($(this)) == false){
						if ($('.error_icon, .required_icon', $('#shippingAddress')).size() <= 0){
							checkoutClass.processShippingAddress();
						}
					}else{
						$('#noShippingAddress').show();
						$('#shippingMethods').hide();
					}
				});

				if ($(this).hasClass('required')){
					if ($(this).val() != '' && checkoutClass.fieldErrorCheck($(this), true, true) == false){
						checkoutClass.addIcon($(this), 'success');
					}else{
						checkoutClass.addIcon($(this), 'required');
					}
				}
			}
		});

		$('select, input', $('#pickupAddress')).each(function (){
			if ($(this).attr('name') != undefined && $(this).attr('type') != 'checkbox' && $(this).attr('type') != 'radio'){
				$(this).blur(function (){
					checkoutClass.fieldErrorCheck($(this));
				});

				if ($(this).hasClass('required')){
					if ($(this).val() != '' && checkoutClass.fieldErrorCheck($(this), true, true) == false){
						checkoutClass.addIcon($(this), 'success');
					}else{
						checkoutClass.addIcon($(this), 'required');
					}
				}
			}
		});

		$('select[name="billing_country"], input[name="billing_street_address"], input[name="billing_postcode"], input[name="billing_city"]', $('#billingAddress')).each(function (){
			if ($(this).attr('type') == 'select-one'){
				$(this).change(function (){
					if (checkoutClass.fieldErrorCheck($(this)) == false){
						checkoutClass.processBillingAddress();
					}
				});
			}else{
				$(this).blur(function (){
					if ($(this).hasClass('required')){
						if (checkoutClass.fieldErrorCheck($(this)) == false){
							checkoutClass.processBillingAddress();
						}
					}else{
						checkoutClass.processBillingAddress();
					}
				});
			}
		});

		$('select[name="shipping_country"], select[name="billing_country"], select[name="pickup_country"]').each(function (){
			var $thisName = $(this).attr('name');
			if ($thisName == 'shipping_country'){
				checkoutClass.addCountryAjax($(this), 'shipping_state', 'stateCol_shipping');
				if ($('#diffShipping:checked').size() > 0){
					$(this).change();
				}
			}else if ($thisName == 'pickup_country'){
				checkoutClass.addCountryAjax($(this), 'pickup_state', 'stateCol_pickup');
				if ($('#diffPickup:checked').size() > 0){
					$(this).change();
				}
			}else{
				checkoutClass.addCountryAjax($(this), 'billing_state', 'stateCol_billing');
				$(this).change();
			}
		});

		$('input[name="billing_email_address"]').unbind('blur').blur(function (){
			if (checkoutClass.initializing == true){
				checkoutClass.addIcon($(this), 'required');
			}else{
				if (this.changed == false) return;
				if (checkoutClass.fieldErrorCheck($(this), true, true) == false){
					this.changed = false;
					checkoutClass.queueAjaxRequest({
						url: checkoutClass.pageLinks.checkout,
						data: 'action=checkEmailAddress&emailAddress=' + $(this).val(),
						type: 'post',
						beforeSendMsg: 'Checking Email Address',
						dataType: 'json',
						success: function (data){
							var $curField = $('input[name="billing_email_address"]');
							$('.success, .error', $curField.parent()).hide();
							if (data.success == false){
								checkoutClass.addIcon($curField, 'error', data.errMsg.replace('/n', "\n"));
								alert(data.errMsg.replace('/n', "\n").replace('/n', "\n").replace('/n', "\n"));
							}else{
								checkoutClass.addIcon($curField, 'success');
							}
						},
						errorMsg: 'There was an error checking email address, please inform IT Web Experts about this error.'
					});
				}
			}
		}).keyup(function (){
			this.changed = true;
		});

		$('#updateCartButton').click(function (){
			checkoutClass.showAjaxLoader();
			checkoutClass.queueAjaxRequest({
				url: checkoutClass.pageLinks.checkout,
				data: 'action=updateQuantities&' + $('input', $('#shoppingCart')).serialize(),
				type: 'post',
				beforeSendMsg: 'Updating Product Quantities',
				dataType: 'json',
				success: function (){
					checkoutClass.updateCartView();
					checkoutClass.updateFinalProductListing();
					if ($('#noPaymentAddress:hidden').size() > 0){
						checkoutClass.updatePaymentMethods();
						checkoutClass.updateShippingMethods();
					}
					checkoutClass.updateOrderTotals();
				},
				errorMsg: 'There was an error updating shopping cart, please inform IT Web Experts about this error.'
			});
			return false;
		}).button({
			icon: 'ui-icon-cart'
		});

		function checkAllErrors(){
			var errMsg = '';
			if ($('.required_icon:visible', $('#billingAddress')).size() > 0){
				errMsg += 'Please fill in all required fields in "Billing Address"' + "\n";
			}

			if ($('.error_icon:visible', $('#billingAddress')).size() > 0){
				errMsg += 'Please correct fields with errors in "Billing Address"' + "\n";
			}

			if ($('#diffShipping:checked').size() > 0){
				if ($('.required_icon:visible', $('#shippingAddress')).size() > 0){
					errMsg += 'Please fill in all required fields in "Shipping Address"' + "\n";
				}

				if ($('.error_icon:visible', $('#shippingAddress')).size() > 0){
					errMsg += 'Please correct fields with errors in "Shipping Address"' + "\n";
				}
			}

			if (checkoutClass.pickupEnabled === true){
				if ($('#diffPickup:checked').size() > 0){
					if ($('.required_icon:visible', $('#pickupAddress')).size() > 0){
						errMsg += 'Please fill in all required fields in "Pickup Address"' + "\n";
					}

					if ($('.error_icon:visible', $('#pickupAddress')).size() > 0){
						errMsg += 'Please correct fields with errors in "Pickup Address"' + "\n";
					}
				}
			}

			if (errMsg != ''){
				errMsg = '------------------------------------------------' + "\n" +
				'                 Address Errors                 ' + "\n" +
				'------------------------------------------------' + "\n" +
				errMsg;
			}

			if ($(':radio[name="payment"]:checked').size() <= 0){
				if ($('input[name="payment"]:hidden').size() <= 0){
					if ($('#voucherPayment').size() <= 0 || !$('#voucherPayment').hasClass('coversAll')){
						errMsg += '------------------------------------------------' + "\n" +
						'           Payment Selection Error              ' + "\n" +
						'------------------------------------------------' + "\n" +
						'You must select a payment method.' + "\n";
					}
				}
			}

			if (checkoutClass.shippingEnabled === true && onePage.onlyReservations == false){
				if ($(':radio[name="shipping"]:checked').size() <= 0){
					if ($('input[name="shipping"]:hidden').size() <= 0){
						errMsg += '------------------------------------------------' + "\n" +
						'           Shipping Selection Error             ' + "\n" +
						'------------------------------------------------' + "\n" +
						'You must select a shipping method.' + "\n";
					}
				}
			}

			if ($(':checkbox[name="terms"]:checked').size() <= 0){
				errMsg += '------------------------------------------------' + "\n" +
				'         Terms And Conditions Error             ' + "\n" +
				'------------------------------------------------' + "\n" +
				'You must agree to our terms and conditions.' + "\n";
			}

			if (errMsg.length > 0){
				alert(errMsg);
				return false;
			}else{
				$('#checkoutForm').trigger('submit');
				return false;
			}
		}

		$('#checkoutButton').click(function() {
			if (checkoutClass.pickupEnabled === true){
				var getVars = '';
				if ($('input[id="diffPickup"]:checked').size() > 0){
					getVars = '&' + $('*', $('#pickupAddress')).serialize();
				}else if ($('input[id="diffShipping"]:checked').size() > 0){
					getVars = '&use=shipping';
				}else{
					getVars = '&use=billing';
				}
				checkoutClass.queueAjaxRequest({
					url: checkoutClass.pageLinks.checkout,
					data: 'action=verifyPickupAddress' + getVars,
					type: 'post',
					beforeSendMsg: 'Checking Service Availability',
					dataType: 'json',
					success: function (data){
						if(data.isInventoryCenterEnabled == true){
							if (data.inService == true){
								return checkAllErrors();
							}else{
								alert('Sorry, currently we do not service your area, we are working to expand to service more locations.');
							}
						}else{
							return checkAllErrors();
						}
					}
				});
			}else{
				return checkAllErrors();
			}
			return false;
		}).button({
			icon: 'ui-icon-check'
		});

		if (this.ccgvInstalled == true){
			$('input[name="gv_redeem_code"]').focus(function (){
				if ($(this).val() == 'redeem code'){
					$(this).val('');
				}
			});

			$('#voucherRedeem').click(function (){
				checkoutClass.queueAjaxRequest({
					url: checkoutClass.pageLinks.checkout,
					data: 'action=redeemVoucher&code=' + $('input[name="gv_redeem_code"]').val(),
					type: 'post',
					beforeSendMsg: 'Validating Coupon',
					dataType: 'json',
					success: function (data){
						if (data.success == false){
							alert('Coupon is either invalid or expired.');
						}
						checkoutClass.updateOrderTotals();
					},
					errorMsg: 'There was an error redeeming coupon, please inform IT Web Experts about this error.'
				});
				return false;
			}).button({
				icon: 'ui-icon-plusthick'
			});
		}

		if (this.onlyReservations == true || this.newRentAccount == true){
			$('#updateCartButton').parent().parent().parent().hide();

			$('.rentalPlans').each(function (){
				$(this).click(function (){
					checkoutClass.queueAjaxRequest({
						url: checkoutClass.pageLinks.checkout,
						data: 'action=setMembershipPlan&planID=' + $(this).val(),
						type: 'post',
						beforeSendMsg: 'Setting Membership Plan',
						dataType: 'json',
						success: function (data){
							checkoutClass.updateOrderTotals();
						},
						errorMsg: 'There was an error setting membership plan, please inform IT Web Experts about this error.'
					});
				});

				if (this.checked){
					$(this).click();
				}
			});
		}

		$('.termsWindow').click(function (){
			window.open($(this).attr('href'),'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=600,height=600,screenX=150,screenY=150,top=150,left=150');
			return false;
		});

		this.initializing = false;
	}
}

$(document).ready(function (){
	$('.removeFromCart').live('click', function (){
		var $productRow = $(this).parent().parent();
		checkout.queueAjaxRequest({
			url: $(this).attr('href'),
			beforeSendMsg: 'Removing Product From Cart',
			dataType: 'json',
			success: function (data){
				if (data.products == 0){
					document.location = checkout.pageLinks.shoppingCart;
				}else if (data.redirect){
					document.location = data.redirect;
				}else{
					$productRow.remove();
					checkout.updateFinalProductListing();
					checkout.updateShippingMethods();
					checkout.updateOrderTotals();
				}
			},
			errorMsg: 'There was an error updating shopping cart, please inform IT Web Experts about this error.'
		});
		return false;
	});
	
	$('.moduleRow').live('click mouseover mouseout', function (e){
		if ($(this).hasClass('moduleRowSelected')) return;
		
		switch(e.type){
			case 'mouseover':
				$(this).addClass('moduleRowOver');
				break;
			case 'mouseout':
				$(this).removeClass('moduleRowOver');
				break;
			case 'click':
				var selector = ($(this).hasClass('shippingRow') ? '.shippingRow' : '.paymentRow') + '.moduleRowSelected';
				$(selector).removeClass('moduleRowSelected');
				
				$(this).removeClass('moduleRowOver').addClass('moduleRowSelected');
				if (!$(':radio', $(this)).is(':checked')){
					$(':radio', $(this)).attr('checked', 'checked').click();
				}
				break;
		}
	});
	
	$('input[name=shipping], input[name=payment]').live('click', function (e, noOrdertotalUpdate){
		if ($(this).attr('name') == 'shipping'){
			checkout.setShippingMethod($(this).val());
		}else{
			checkout.setPaymentMethod($(this));
		}
		
		if (!noOrdertotalUpdate){
			checkout.updateOrderTotals();
		}
	});
	
	$().live('blur change', function (){
	});
});