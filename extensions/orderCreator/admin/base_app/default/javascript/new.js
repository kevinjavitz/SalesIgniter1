function number_format(number){
	return Math.round(
		(number * 100)
	) / 100;
}

$(document).ready(function (){

	$('select[name=payment_method]').change(function(){
		var $self = $(this);
		showAjaxLoader($self, 'small');

		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=changePaymentMethod'),
			cache: false,
			dataType: 'json',
			data: 'payment_method=' + $self.val(),
			type: 'post',
			success: function (data){
				if (data.success == true){
					$self.parent().parent().replaceWith(data.tableRow);
					$('.paymentProcessButton').button();
				}else if (typeof data.success == 'object'){
					alert(data.success.error_message);
				}else{
					//alert('Payment Failed');
				}
				//removeAjaxLoader($self);
			}
		});
	});

	$('input[name=customer_search]').autocomplete({
		source: js_app_link('appExt=orderCreator&app=default&appPage=new&action=findCustomer'),
		select: function (e, ui){
			showAjaxLoader($('.addressTable'), 'xlarge');
			$.ajax({
				cache: false,
				dataType: 'json',
				url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadCustomerInfo&cID=' + ui.item.value),
				success: function (data){
					removeAjaxLoader($('.addressTable'));
					
					$('.customerAddress').html(data.customer);
					$('.billingAddress').html(data.billing);
					$('.deliveryAddress').html(data.delivery);
					$('.pickupAddress').html(data.pickup);
					$('input[name=email]').val(data.email_address);
					$('input[name=telephone]').val(data.telephone);
					$('input[name=account_password]').attr('disabled', 'disabled');
					
					$('.productSection, .totalSection, .paymentSection, .commentSection').show();
					$('select[name=payment_method]').change(function(){
						var $self = $(this);
						showAjaxLoader($self, 'small');

						$.ajax({
							url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=changePaymentMethod'),
							cache: false,
							dataType: 'json',
							data: 'payment_method=' + $self.val(),
							type: 'post',
							success: function (data){
								if (data.success == true){
									$self.parent().parent().replaceWith(data.tableRow);
									$('.paymentProcessButton').button();
								}else if (typeof data.success == 'object'){
									alert(data.success.error_message);
								}else{
									//alert('Payment Failed');
								}
								//removeAjaxLoader($self);
							}
						});
					});
					$('select[name=payment_method]').trigger('change');
					$('.purchaseType').trigger('change');
				}
			});
			$('input[name=customer_search]').val(ui.item.label);
			return false;
		}
	});
	
	$('.customerSearchReset').click(function (){
		$('.addressTable').find('input').val('');
		$('.addressTable').find('select').val('');
		$('input[name=customer_search]').val('');
		$('input[name=email]').val('');
		$('input[name=telephone]').val('');
		$('input[name=account_password]').removeAttr('disabled');
		
		$('.productSection, .totalSection, .paymentSection, .commentSection').hide();
	});
	
	$('.purchaseType').live('change', function (){
		var $Row = $(this).parent().parent().parent().parent().parent();
		var prType = $(this).val();

		showAjaxLoader($Row, 'normal');
		$.ajax({
			url: js_app_link('rType=ajax&appExt=orderCreator&app=default&appPage=new&action=updateOrderProduct&id=' + $Row.attr('data-id') + '&purchase_type=' + $(this).val()),
			cache: false,
			dataType: 'json',
			success: function (data){
				$Row.find('td:eq(1)').html(data.name);
				$Row.find('.priceEx').val(data.price).trigger('keyup');
				var isEvent = false;
				if($('.eventf').size() > 0){
					isEvent = true;
				}
				if(prType == 'reservation' && isEvent == false){
					$('.productQty').attr('readonly','readonly');
				}
				if(isEvent && $Row.find('.eventf').val() != '0'){
					$('.reservationShipping').trigger('change');
				}
				removeAjaxLoader($Row);
			}
		})
	});

	$('.taxRate').live('keyup', function (){
		var $Row = $(this).parent().parent();
		var Quantity = parseFloat($Row.find('.productQty').val());
		var TaxRate = parseFloat($(this).val());
		var Price = parseFloat($Row.find('.priceEx').val());

		$Row.find('.priceIn').html(number_format(Price + (Price * (TaxRate/100))));
		$Row.find('.priceInTotal').html(number_format(((Price * Quantity) + ((Price * Quantity) * (TaxRate/100)))));

		var total = 0;
		var $TotalElement = null;
		$('.orderTotalType').each(function (){
			var $Row = $(this).parent().parent();
			if ($(this).val() == 'subtotal'){
				var subtotal = 0;
				$('.priceEx').each(function (){
					subtotal += parseFloat($(this).val()) * parseFloat($(this).parent().parent().find('.productQty').val());
				});

				$Row.find('.orderTotalValue').val(number_format(subtotal));
				total += subtotal;
			}else if ($(this).val() == 'tax'){
				var tax = 0;
				$('.priceEx').each(function (){
					tax += (parseFloat($(this).val()) * parseFloat($(this).parent().parent().find('.productQty').val())) * (parseFloat($(this).parent().parent().find('.taxRate').val()) / 100);
				});

				$Row.find('.orderTotalValue').val(number_format(tax));
				total += tax;
			}else if ($(this).val() == 'shipping'){
				total += parseFloat($Row.find('.orderTotalValue').val());
			}else if ($(this).val() == 'total'){
				$TotalElement = $(this);
			}
		});
		
		if ($TotalElement){
			$TotalElement.parent().parent().find('.orderTotalValue').val(number_format(total));
		}
	})

	$('.priceEx').live('keyup', function (){
		var $Row = $(this).parent().parent();
		var Quantity = parseFloat($Row.find('.productQty').val());
		var TaxRate = parseFloat($Row.find('.taxRate').val());
		var Price = parseFloat($(this).val());

		$Row.find('.priceExTotal').html(number_format(Price * Quantity));
		$Row.find('.taxRate').trigger('keyup');
	})

	$('.productQty').live('keyup', function (){
		var $Row = $(this).parent().parent();
		$Row.find('.priceEx').trigger('keyup');
	});

	$('.insertTotalIcon').live('click', function (){
		var $TableBody = $(this).parent().parent().parent().parent().find('tbody');

		var count = parseInt($TableBody.parent().attr('data-nextId'));
		$TableBody.parent().attr('data-nextId', count + 1);

		var $selectBox = $TableBody.find('.orderTotalType:first')

		$TableBody.prepend('<tr data-count="' + count + '">' +
			'<td class="ui-widget-content" style="border-top:none;" align="center"><input class="ui-widget-content" type="text" style="width:98%;" name="order_total[' + count + '][title]" value=""></td>' +
			'<td class="ui-widget-content" style="border-top:none;border-left:none;" align="center"><input class="ui-widget-content orderTotalValue" type="text" size="10" name="order_total[' + count + '][value]" value="0"><input type="hidden" name="order_total[' + count + '][sort_order]" class="totalSortOrder"></td>' +
			'<td class="ui-widget-content" style="border-top:none;border-left:none;" align="right"><select name="order_total[' + count + '][type]" class="orderTotalType">' + $selectBox.html() + '</select></td>' +
			'<td class="ui-widget-content" style="border-top:none;border-left:none;" align="center"><span class="ui-icon ui-icon-closethick deleteIcon" tooltip="Remove From Order"></span><span class="ui-icon ui-icon-arrow-4 moveTotalIcon" tooltip="Drag To Reorder"></span></td>' +
		'</tr>');
	});

	$('.insertProductIcon').live('click', function (){
		var $TableBody = $(this).parent().parent().parent().parent().find('tbody');

		var $Row = $('<tr></tr>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none"></td>')
			.append('<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none"><input class="productSearch" name="product_search" style="width:95%"></td>')
			.append('<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"><span class="ui-icon ui-icon-closethick deleteIcon"></span></td>');

		$TableBody.prepend($Row);
	
		$TableBody.find('.productSearch').autocomplete({
			source: js_app_link('appExt=orderCreator&app=default&appPage=new&action=findProduct'),
			select: function (e, ui){
				showAjaxLoader($Row, 'normal');
				$.ajax({
					cache: false,
					dataType: 'html',
					url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadProductRow&pID=' + ui.item.value),
					success: function (html){
						removeAjaxLoader($Row);
						$(html).insertAfter($Row);
						/*change for single purchaseType*/
						$(html).find('.priceEx').trigger('keyup');
						$Row.remove();
						$('.purchaseType').first().trigger('change');
					}
				});
			}
		});
	});

	$('.deleteProductIcon').live('click', function (){
		var $Row = $(this).parent().parent();
		showAjaxLoader($Row, 'normal');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=removeProductRow&id=' + $Row.attr('data-id')),
			success: function (data){
				removeAjaxLoader($Row);
				$Row.remove();
				$('.priceEx:eq(0)').trigger('keyup');
			}
		});
	});

	$('.deleteIcon').live('click', function (){
		if (this.Tooltip){
			this.Tooltip.remove();
		}
		$(this).parent().parent().remove();
	});
	
	$('select.country').live('change', function (){
		var $self = $(this);
		showAjaxLoader($self, 'small');
		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=getCountryZones&addressType=' + $self.attr('data-address_type') + '&country=' + $self.val()),
			cache: false,
			dataType: 'html',
			success: function (html){
				$self.parent().parent().parent().find('.stateCol').html(html);
				removeAjaxLoader($self);
			}
		});
	});
	
	$('.paymentProcessButton').live('click',function (){
		var $self = $(this);
		showAjaxLoader($self, 'small');
		
		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=processPayment'),
			cache: false,
			dataType: 'json',
			data: $(this).parent().parent().find('*').serialize(),
			type: 'post',
			success: function (data){
				if (data.success == true){
					$('.paymentsTable tbody:nth-child(2)').append(data.tableRow);
				}else if (typeof data.success == 'object'){
					alert(data.success.error_message);
				}else{
					alert('Payment Failed');
				}
				removeAjaxLoader($self);
			}
		});
	});

	$('select[name=payment_method]').trigger('change');
	$('.purchaseType').trigger('change');
	$('.paymentRefundButton').click(function (){
		var $self = $(this);
		showAjaxLoader($self, 'small');


		$('<div id="popupRefund"></div>').dialog({
		autoOpen: true,
		width: 300,
		height: 150,
		close: function (e, ui){
			$(this).dialog('destroy').remove();
			removeAjaxLoader($self);
		},
		open: function (e, ui){
			$(e.target).html('Refund Amount: <input id="refundedAmount" name="refundedAmount">');

		},
		buttons: {
				'Save': function() {
					 //ajax call to save comment on success
						dialog = $(this);
					   $.ajax({
						url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=refundPayment'),
						cache: false,
						dataType: 'json',
						data: 'payment_module=' + $self.data('payment_module') + '&payment_history_id=' + $self.data('payment_history_id')+'&amount='+$('#refundedAmount').val(),
						type: 'post',
						success: function (data){
							if (data.success == true){
								$('.paymentsTable tbody').append(data.tableRow);
							}else if (typeof data.success == 'object'){
								alert(data.success.error_message);
							}else{
								alert('Payment Failed');
							}
							removeAjaxLoader($self);
							dialog.dialog('close');
						}
					});
				},
				Cancel: function() {
					$(this).dialog('close');
					removeAjaxLoader($self);
				}
			}
	});

	});
	
	$('.orderTotalType').live('change', function (){
		var $self = $(this);
		if ($self.val() == 'shipping'){
			showAjaxLoader($self.parent().parent(), 'small');
			$.ajax({
				cache: false,
				dataType: 'html',
				url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=getShippingQuotes&totalCount=' + $self.parent().parent().attr('data-count')),
				success: function (data){
					$self.parent().parent().find('td:eq(0)').html(data);
					removeAjaxLoader($self.parent().parent());
				}
			});
		}else{
			$self.parent().parent().find('td:eq(0)').html('<input class="ui-widget-content" type="text" style="width:98%;" name="order_total[' + $self.parent().parent().attr('data-count') + '][title]" value="">');
		}
	});
	
	function getTotalsTotal(exclude){
		exclude = exclude || [];
		var returnVal = 0;
		$('.orderTotalTable > tbody').find('.orderTotalType').each(function (){
			if ($.inArray($(this).val(), exclude) == -1){
				returnVal += parseFloat($(this).parent().parent().find('.orderTotalValue').val());
			}
		});
		return returnVal;
	}
	
	function getTotalRow(type){
		var $returnVal;
		$('.orderTotalTable > tbody').find('.orderTotalType').each(function (){
			if ($(this).val() == type){
				$returnVal = $(this).parent().parent();
				return;
			}
		});
		return $returnVal;
	}
	
	$('.orderTotalValue').live('keyup', function (){
		var $tableTbody = $(this).parent().parent().parent();
		var $selectBox = $(this).parent().parent().find('.orderTotalType');
		var $totalRow = getTotalRow('total');
		if ($selectBox.val() == 'subtotal'){
			var TotalsValues = parseFloat(getTotalsTotal(['total', $selectBox.val()]));
			var ThisVal = parseFloat($(this).val());
			$totalRow.find('.orderTotalValue').val(
				Math.round((TotalsValues + ThisVal) * 100) / 100
			);
		}else if ($selectBox.val() == 'shipping'){
			var TotalsValues = parseFloat(getTotalsTotal(['total', $selectBox.val()]));
			var ThisVal = parseFloat($(this).val());
			$totalRow.find('.orderTotalValue').val(
				Math.round((TotalsValues + ThisVal) * 100) / 100
			);
		}else if ($selectBox.val() != 'total'){
			var TotalsValues = parseFloat(getTotalsTotal(['total', $selectBox.val()]));
			var ThisVal = parseFloat($(this).val());
			$totalRow.find('.orderTotalValue').val(
				Math.round((TotalsValues + ThisVal) * 100) / 100
			);
		}
	});
	
	$('.orderTotalTable > tbody').sortable({
		handle: '.moveTotalIcon',
		placeholder: '.ui-state-highlight',
		forcePlaceholderSize: true,
		update: function (e, ui){
			$('.orderTotalTable > tbody > tr').each(function (i, el){
				$(el).find('.totalSortOrder').val(i);
			});
		}
	});
	
	$('.saveAddressButton').click(function (){
		showAjaxLoader($('.customerSection'), 'xlarge');
		$.ajax({
			cache: false,
			dataType: 'html',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveCustomerInfo'),
			data: $('.customerSection *').serialize(),
			type: 'post',
			success: function (data){
				$('.productSection, .totalSection, .paymentSection, .commentSection').show();
				removeAjaxLoader($('.customerSection'));
			}
		});
	});
	
	$('.addressCopyButton').live('click', function (){
		var copyFrom = $(this).data('copy_from');
		var copyTo = $(this).data('copy_to');
		
		$('input[name="address[' + copyTo + '][entry_name]"]').val($('input[name="address[' + copyFrom + '][entry_name]"]').val());
		$('input[name="address[' + copyTo + '][entry_company]"]').val($('input[name="address[' + copyFrom + '][entry_company]"]').val());
		$('input[name="address[' + copyTo + '][entry_street_address]"]').val($('input[name="address[' + copyFrom + '][entry_street_address]"]').val());
		$('input[name="address[' + copyTo + '][entry_suburb]"]').val($('input[name="address[' + copyFrom + '][entry_suburb]"]').val());
		$('input[name="address[' + copyTo + '][entry_city]"]').val($('input[name="address[' + copyFrom + '][entry_city]"]').val());
		$('input[name="address[' + copyTo + '][entry_postcode]"]').val($('input[name="address[' + copyFrom + '][entry_postcode]"]').val());
		$('select[name="address[' + copyTo + '][entry_country]"]').val($('select[name="address[' + copyFrom + '][entry_country]"]').val());
		
		if ($('input[name="address[' + copyFrom + '][entry_state]"]').size() > 0){
			$('input[name="address[' + copyTo + '][entry_state]"]').val($('input[name="address[' + copyFrom + '][entry_state]"]').val());
		}else if ($('select[name="address[' + copyFrom + '][entry_state]"]').size() > 0){
			var stateCopyTo = $('select[name="address[' + copyFrom + '][entry_state]"]').clone(true);
			stateCopyTo.attr('name', 'address[' + copyTo + '][entry_state]');
			stateCopyTo.val($('select[name="address[' + copyFrom + '][entry_state]"]').val());
			
			if ($('input[name="address[' + copyTo + '][entry_state]"]').size() > 0){
				$('input[name="address[' + copyTo + '][entry_state]"]').replaceWith(stateCopyTo);
			}else if ($('select[name="address[' + copyTo + '][entry_state]"]').size() > 0){
				$('select[name="address[' + copyTo + '][entry_state]"]').replaceWith(stateCopyTo);
			}
		}
	});
	
	var getVars = getUrlVars();
	if (!getVars['error'] && !getVars['oID']){
		$('.productSection, .totalSection, .paymentSection, .commentSection').hide();
	}
});