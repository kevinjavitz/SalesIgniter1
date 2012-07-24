function number_format(number){
	return Math.round(
		(number * 100)
	) / 100;
}

function updateOC($myRow){
    showAjaxLoader($myRow, 'xlarge');
    var myStartDate = $myRow.find('.start_date').val();

    if($myRow.find('.start_time').size() > 0){
        myStartDate = myStartDate+' '+$myRow.find('.start_time').val()+':00:00';
    }

    var myEndDate = $myRow.find('.end_date').val();

    if($myRow.find('.end_time').size() > 0){
        myEndDate = myEndDate+' '+$myRow.find('.end_time').val()+':00:00';
    }
    $.ajax({
        cache: false,
        dataType: 'json',
        type: 'post',
        url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'),
        data: 'qty='+$myRow.find('.productQty').val()+'&start_date='+myStartDate+'&end_date='+myEndDate+'&purchase_type='+$myRow.attr('data-product_type')+'&idP='+$myRow.attr('data-id')+'&'+$myRow.find('.pName *').serialize(),
        success: function (data) {
            removeAjaxLoader($myRow);
            if (data.success == true && data.price != null) {
                $myRow.find('.priceEx').val(data.price).trigger('keyup');
            }else{
                $myRow.find('.priceEx').trigger('keyup');
            }
            if(data.hasInventory == true){
                $myRow.find('.ui-widget-content').css('background','#ffffff');
                $myRow.find('.ui-widget-content').css('color','#000000');
            }else{
                $myRow.find('.ui-widget-content').css('background','#ff0000');
                $myRow.find('.ui-widget-content').css('color','#ffffff');
            }

        }
    });
}

$(document).ready(function (){
    $('#tabsCustomer').tabs();
    /*$('#ui-widget-content-left').panel({
        collapseType:'slide-left',
        trueVerticalText:true,
        collapsed:false,
        width:'350px'
    });*/
    $('#accordion').accordion();
    /*$('#panelLeft_1, #panelLeft_2, #panelLeft_3, #panelLeft_4').panel({
        accordion:'my_group'
    });*/

    $('#panelCenter_1').panel({
        collapsible:false
    });

    $('#panelCenter_2').panel({
        collapsible:true
    });

    $('#panelCenter_3').panel({
        collapsible:true
    });

    $('#panelCenter_5').panel({
        collapsible:true
    });


    //makeTabsVertical('#tabsCustomer');
    /*$('select[name="payment_method"]').live('change', function(){
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
	}); */
	var getVars = getUrlVars();

	var oID;
	if(getVars['oID']){
		 oID = '&oID='+getVars['oID'];
	}else{
		 oID = '';
	}

	$('.resReports').click(function(){
		var newwindow=window.open(js_app_link('appExt=payPerRentals&app=reservations_reports&appPage=default'),'name','height=700,width=960');
		if (window.focus) {newwindow.focus()}
		return false;
	});

    $('.trackingButton').click(function(){
        var $Row = $(this).parentsUntil('tbody').last();

        if($Row.find('.trackingInput').val() ==  '') {
            alert('Please enter tracking number');
            return false;
        }
    });
    
     $('.removePayment').click(function(){
        var $self = $(this);

        $('<div></div>').dialog({
            autoOpen: true,
            width: 300,
            modal: true,
            resizable: false,
            allowClose: false,
            title: 'Delete Paymen tConfirm',
            open: function (e){
                $(e.target).html('Are you sure you want to null this payment');
            },
            close: function (){
                $(this).dialog('destroy');
            },
            buttons: {
                'Delete': function(){
                    var $selfD = $(this);
                    $.ajax({
                        url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=removePayment'),
                        cache: false,
                        dataType: 'json',
                        data: 'idpayment=' + $self.attr('idpayment')+'&oID='+getVars['oID'],
                        type: 'post',
                        success: function (data){
                            if(data.success == true){
                                $self.parent().parent().remove();
                            }else{
                                alert('Cannot be deleted');
                            }
                            $selfD.dialog('destroy');
                        }
                    });
                },
                'Don\'t Delete': function(){
                    $(this).dialog('destroy');
                }
            }
        });

        return false;
    });

	$('#emailEstimate').click(function(){
		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=sendEstimateEmail'),
			cache: false,
			dataType: 'json',
			data: 'email=' + $('#emailInput').val()+'&oID='+getVars['oID'],
			type: 'post',
			success: function (data){
				if(data.success == true){
					alert('Estimate Sent!');
				}else{
					alert('Email not sent. Please Check email address');
				}
			}
		});
		return false;
	});

	$('#resendEmail').click(function(){
		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=resendEmail'),
			cache: false,
			dataType: 'json',
			data: 'oID='+getVars['oID']+'&isEstimate='+getVars['isEstimate'],
			type: 'post',
			success: function (data){
				if(data.success == true){
					alert('Confirmation email resent');
				}
			}
		});
		return false;
	});


	$('input[name=customer_search]').autocomplete({
		html: true,
		source: js_app_link('appExt=orderCreator&app=default&appPage=new&action=findCustomer'+oID),
		select: function (e, ui){
			if (ui.item.value == 'no-select') return false;
			if (ui.item.value == 'disabled'){
				alert(ui.item.reason);
				return false;
			}

			showAjaxLoader($('#tabsCustomer'), 'xlarge');
			$.ajax({
				cache: false,
				dataType: 'json',
				url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadCustomerInfo&cID=' + ui.item.value+oID),
				success: function (data){
					removeAjaxLoader($('#tabsCustomer'));

					$('.customerAddress').html(data.customer);
					$('.billingAddress').html(data.billing);
					$('.deliveryAddress').html(data.delivery);
					$('.pickupAddress').html(data.pickup);
					$.each(data.field_values, function (k, v){
						$('*[name=' + k + ']').val(v);
					});
					$('input[name=account_password]').attr('disabled', 'disabled');
					$('input[name=member_number]').attr('disabled', 'disabled');
                    $('input[name=customer_search]').attr('disabled','disabled');
					//$('.ui-widget-content-middle, .trackingSection, .productSection, .totalSection, .paymentSection, .commentSection').show();
					if (data.productTable){
						$('.productTable').replaceWith(data.productTable);
					}

					if (data.orderTotalTable){
						$('.orderTotalTable').replaceWith(data.orderTotalTable);
					}

					if (data.paymentsTable){
						$('.paymentsTable').replaceWith(data.paymentsTable);
					}

					$('select[name="payment_method"]').change(function(){
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
								}
							}
						});
					});
					$('select[name="payment_method"]').trigger('change');
					$('select[name=customers_store]').trigger('change');
				}
			});
			$('input[name=customer_search]').val('');
			return true;
		}
	});
	
	$('.customerSearchReset').click(function (){
		$('#tabsCustomer').find('input').val('');
		$('#tabsCustomer').find('select').val('');
		$('input[name=customer_search]').val('');
		$('input[name=email]').val('');
		$('input[name=telephone]').val('');
		$('input[name=account_password]').removeAttr('disabled');
        $('input[name=customer_search]').removeAttr('disabled');
		//$('.ui-widget-content-middle, .trackingSection, .productSection, .totalSection, .paymentSection, .commentSection').hide();
	});
	
	$('.purchaseType').live('change', function (){
		var self = this;
		var $Row = $(self).parentsUntil('tbody').last();
		var prType = $(self).val();

		showAjaxLoader($Row, 'normal');
		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=updateOrderProduct&id=' + $Row.attr('data-id') + '&purchase_type=' + $(this).val()+oID),
			cache: false,
			dataType: 'json',
			success: function (data){
				if (data.hasError == true){
					alert(data.errorMessage);
					$(self).val('');
				}else{
					$Row.find('td:eq(1)').html(data.name);
					$Row.find('td:eq(2)').html(data.barcodes);


                    if($Row.find('.consumption').val() == '1'){
                        $Row.find('.addonsPopup').hide();
                    }
					//$Row.find('td:eq(2)').find('select').combobox();

					$Row.find('.priceEx').val(data.price).trigger('keyup');
					var isEvent = false;
					if($('.eventf').size() > 0){
						isEvent = true;
					}
					if(prType == 'reservation' && isEvent == false){
						$('.productQty').attr('readonly','readonly');
						if(!getVars['oID']){
							$Row.addClass('nodatesSelected');
						}
					}
					if(isEvent && $Row.find('.eventf').val() != '0'){
						//$('.reservationShipping').trigger('change');
						//$('.eventf').attr('isTriggered','1');
						//$('.eventf').trigger('change');
					}
				}
				removeAjaxLoader($Row);
			}
		})
	});

	$('.purchaseType').live('updateInfo', function (){
		var self = this;
		var $Row = $(self).parentsUntil('tbody').last();
		var prType = $(self).val();

		showAjaxLoader($Row, 'normal');
		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=updateInfoOrderProduct&id=' + $Row.attr('data-id') + '&purchase_type=' + $(this).val()+oID),
			cache: false,
			dataType: 'json',
			success: function (data){
				if (data.hasError == true){
					alert(data.errorMessage);
					$(self).val('');
				}else if(data.noObject == true){
                    $Row.find('.priceEx').trigger('keyup');
				}else{
					$Row.find('td:eq(1)').html(data.name);
					$Row.find('td:eq(2)').html(data.barcodes);
					//$Row.find('td:eq(2)').find('select').combobox();

					$Row.find('.priceEx').val(data.price).trigger('keyup');
					var isEvent = false;
					if($('.eventf').size() > 0){
						isEvent = true;
					}
					if(prType == 'reservation' && isEvent == false){
						$('.productQty').attr('readonly','readonly');
						if(!getVars['oID']){
							$Row.addClass('nodatesSelected');
						}
					}
					if(isEvent && $Row.find('.eventf').val() != '0'){
						//$('.reservationShipping').trigger('change');
						$('.eventf').attr('isTriggered','1');
						$('.eventf').trigger('change');
					}
					$('.barcodeName').live('focus',function(){
						if($(this).val() == ''){
							$(this).keyup().autocomplete("search", "");
						}
					});
				}
				removeAjaxLoader($Row);	
			}
		})
	});

	$('.barcodeName').live('mousedown', function() {
					var $rowVal = $(this).parentsUntil('tbody').last();;

                    if($rowVal.find('.datesSelected').attr('data-id'))
					    var attributeVal = 'mId='+ $rowVal.attr('data-id') + '&purchaseType=' + $rowVal.find('.purchaseType').val();
                    else
                        var attributeVal = 'mId='+ $rowVal.attr('data-id') + '&purchaseType=' + $rowVal.find('.purchaseType').val();
					attributeVal += '&id[reservation]=';
					$rowVal.find('.productAttribute option:selected').each(function() {
						attributeVal += '{' + $(this).parent().parent().attr('attrval') + '}' + $(this).val();/*this part needs updated for attributes*/
					});

                    if($rowVal.find('.datesSelected').attr('data-product_id'))
                        attributeVal += '&pID='+ $rowVal.attr('data-product_id');
                    else
                        attributeVal += '&pID='+ $rowVal.attr('data-product_id');

                    var i = 0;
                    var a = new Array();
                    $rowVal.find('.bar').each(function (){
                        attributeVal += '&barcode' + i + '=' +  $(this).find('.bar_id').attr('barid');
                        i++;

                    });
                    if(i >= 1)
                        attributeVal += '&barcodeQty=' + i;
                    else
                        attributeVal += '&barcodeQty=0';
                     // alert(attributeVal);
                    var link = js_app_link('appExt=orderCreator&app=default&appPage=new&action=getBarcodes');


					var link = js_app_link('appExt=orderCreator&app=default&appPage=new&action=getBarcodes'+oID);

		            var $barInput = $(this);
					$(this).autocomplete({
						source: function(request, response) {
							$.ajax({
							  url: link,
							  data: attributeVal,
							  dataType: 'json',
							  type: 'POST',
							  success: function(data){
								  response(data);
							  }
							});
						 },
						minLength: 0,
						select: function(event, ui) {

                            $barInput.val('');
                            var newBarcode=  '<div class="barcode" ><span class="bar_id" barid="' + ui.item.value + '">' + ui.item.label + '</span><a class="ui-icon ui-icon-closethick removeBarcode"></a><br/><small><i>- Start Date: <span class="res_start_date">'+ ui.item.date + '</span><br/><span class="res_end_date" style="display: none">' + ui.item.date + '</span></small></div>';
                            $rowVal.find('.barcodes').append(newBarcode);
                            $rowVal.find('.startConsumption').trigger('click');

							return false;
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

        if($Row.find('.consumption').val() == '1'){
            $Row.find('.addonsPopup').hide();
        }

		var $TotalRow = null;
		var total = 0;
		var subtotal = 0;
		var tax = 0;
		$('.priceEx').each(function (){
			var Quantity = parseFloat($(this).parent().parent().find('.productQty').val());
			var Price = parseFloat($(this).val());

			subtotal += Price * Quantity;
			tax += (Price * Quantity) * (parseFloat($(this).parent().parent().find('.taxRate').val()) / 100);
		});

		$('.orderTotalTable > tbody > tr').each(function (){
			var $Row = $(this);
			if ($Row.data('code') == 'subtotal'){
				$Row.find('.orderTotalValue').attr('readonly','readonly');
				$Row.find('.orderTotalValue').val(number_format(subtotal));
				total += subtotal;
			}else if ($Row.data('code') == 'tax'){
				$Row.find('.orderTotalValue').attr('readonly','readonly');
				$Row.find('.orderTotalValue').val(number_format(tax));
				total += tax;
			}else if ($Row.data('code') == 'total'){
				$Row.find('.orderTotalValue').attr('readonly','readonly');
				$TotalRow = $(this);
			}else{
				total += parseFloat($Row.find('.orderTotalValue').val());
			}
		});
		
		if ($TotalRow){
			$TotalRow.find('.orderTotalValue span').html(number_format(total));
			$TotalRow.find('.orderTotalValue input').val(number_format(total));
			$TotalRow.find('.orderTotalValue').val(number_format(total));
		}
	});

	$('.orderTotalTable > tbody > tr input').live('keyup', function(){
		$('.taxRate').trigger('keyup');
	});

	$('.priceEx').live('keyup', function (){
		var $Row = $(this).parent().parent();
		var Quantity = parseFloat($Row.find('.productQty').val());
		var TaxRate = parseFloat($Row.find('.taxRate').val());
		var Price = parseFloat($(this).val());

		$Row.find('.priceExTotal').html(number_format(Price * Quantity));
		$Row.find('.taxRate').trigger('keyup');
	})

	$('.productQty').live('change', function (){
		var $Row = $(this).parent().parent();
        if($Row.hasClass('firstRow') == false){
		    $Row.find('.priceEx').trigger('keyup');
            updateOC($Row);
        }
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

    var $Row = $('.productTable .firstRow');
	$('.insertProductIcon').live('click', function (){
		//var $TableBody = $Row.parent();
		//var productInput = '';

		/*$Row = $('<tr></tr>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none"></td>')
			.append('<td class="ui-widget-content productInput" valign="top" style="border-top:none;border-left:none">' + productInput + '</td>')
			.append('<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"></td>')
			.append('<td class="ui-widget-content" align="right" valign="top" style="border-top:none;border-left:none"><span class="ui-icon ui-icon-closethick deleteIcon"></span></td>');

		$TableBody.prepend($Row);*/
        loadProductRow($Row.attr('data-product_id'), $Row.attr('data-product_type'));


	});

    var loadProductRow = function (pID, prtype,barcode){
        showAjaxLoader($Row, 'normal');
        //showAjaxLoader($Row, 'xlarge');

        var myStartDate = $Row.find('.start_date').val();

        if($Row.find('.start_time').size() > 0){
            myStartDate = myStartDate+' '+$Row.find('.start_time').val()+':00:00';
        }

        var myEndDate = $Row.find('.end_date').val();

        if($Row.find('.end_time').size() > 0){
            myEndDate = myEndDate+' '+$Row.find('.end_time').val()+':00:00';
        }

        $.ajax({
            cache: false,
            dataType: 'json',
            type:'post',
            url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadProductRow&pID=' + pID+'&purchaseType=' + prtype+oID),
            data: 'qty='+$Row.find('.productQty').val()+'&start_date='+myStartDate+'&end_date='+myEndDate,
            success: function (data) {
                removeAjaxLoader($Row);
                if (data.hasError == true){
                    alert(data.errorMessage);
                }
                else {
                    var html = data.html;
                    var $myRow = $(html).insertAfter($Row);
                    $('.productQty').css('width','50px');
                    if(data.price != null){
                        $myRow.find('.priceEx').val(data.price).trigger('keyup');
                    }else{
                        $myRow.find('.priceEx').trigger('keyup');
                    }
                    if(data.hasInventory == true){
                        $myRow.find('.ui-widget-content').css('background','#ffffff');
                        $myRow.find('.ui-widget-content').css('color','#000000');
                    }else{
                        $myRow.find('.ui-widget-content').css('background','#ff0000');
                        $myRow.find('.ui-widget-content').css('color','#ffffff');
                    }
                    $myRow.find('.barcodeName').val(barcode);
                    $myRow.find('.startConsumption').trigger('click');

                    $myRow.find('.start_date').datepicker({
                        onSelect: function(dateText, inst) {
                            updateOC($myRow);
                        }
                    });

                    $myRow.find('.end_date').datepicker({
                        onSelect: function(dateText, inst) {
                            updateOC($myRow);
                        }
                    });

                    $myRow.find('.start_time').change(function(){
                        updateOC($myRow);
                    });

                    $myRow.find('.end_time').change(function(){
                        updateOC($myRow);
                    });



                }
            }
        });
    };

    $Row.find('.end_date').datepicker(
            /*        {
             onSelect: function(dateText, inst) {
             var $Row = $(this).parent();

             if($Row.find('.end_date').val() == ''){
             $Row.find('.end_date').val($Row.find('.start_date').val());
             }
             }
             }*/
    );
    $Row.find('.start_date').datepicker();

    $('.productTable tbody tr').each(function(){
        var $Row = $(this);
        if($Row.hasClass('firstRow') == false){
            $('.productQty').css('width','50px');

            $Row.find('.start_date').datepicker({
                onSelect: function(dateText, inst) {
                    updateOC($Row);
                }
            });

            $Row.find('.end_date').datepicker({
                onSelect: function(dateText, inst) {
                    updateOC($Row);
                }
            });

            $Row.find('.start_time').change(function(){
                updateOC($Row);
            });

            $Row.find('.end_time').change(function(){
                updateOC($Row);
            });

            updateOC($Row);
        }
    });

    /*if ($('.productTable').data('product_entry_method') == 'autosuggest'){
        productInput = '<input class="productSearch" name="product_search" style="width:95%">';
    }else if ($('.productTable').data('product_entry_method') == 'dropmenu'){
        showAjaxLoader($('.productSection'), 'xlarge');
        $.ajax({
            cache: false,
            url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=getProductsDropBox'+oID),
            dataType: 'html',
            success: function (data){
                $('.productTable .productInput').html(data);
                $('.productTable .productSelectBox').change(function (){
                    loadProductRow($(this).val());
                });
                removeAjaxLoader($('.productSection'));
            }
        });
    }*/

    if ($('.productTable').data('product_entry_method') == 'autosuggest'){

        $('.productTable .productSearch').autocomplete({
            source: js_app_link('appExt=orderCreator&app=default&appPage=new&action=findProduct'),
            select: function (e, ui) {
                var barcode = 0;
                var product = 0;
                var $productRow;

                if(ui.item.consumption == '1'){
                    $Row.parent().find('.bar_id').each(function(){
                        if(ui.item.barcode == $(this).html())
                            barcode = 1;
                    });

                    $Row.parent().find('.productName').each(function(){
                        if(ui.item.label == $(this).html()){
                            $productRow = $(this).parent();
                            product = 1;
                        }
                    });

                    if(barcode == 0 && product == 0)
                        loadProductRow(ui.item.value, ui.item.prtype, ui.item.barcode);
                    if(barcode == 1 && product == 1)
                        alert('Product already selected');
                    if(barcode == 0 && ui.item.bar_id == null && product == 1)
                        alert('Product already selected');
                    if(barcode == 0 && ui.item.bar_id != null && product == 1){
                        var newBarcode=  '<div class="barcode" ><span class="bar_id" barid="' + ui.item.bar_id + '">' + ui.item.barcode + '</span><a class="ui-icon ui-icon-closethick removeBarcode"></a><br/><small><i>- Start Date: <span class="res_start_date">'+ ui.item.date + '</span><br/>- End Date: <span class="res_end_date" >' + ui.item.date + '</span></small></div>';
                        $productRow.find('.barcodes').append(newBarcode);
                        $productRow.find('.startConsumption').trigger('click');
                    }
                }else{
                    $Row.attr('data-id','none');
                    $Row.attr('data-product_id',ui.item.pr_id);
                    $Row.attr('data-product_type',ui.item.prtype);
                    //loadProductRow(ui.item.value, ui.item.prtype, ui.item.barcode);
                }

            }
        });
    }

	$('.deleteProductIcon').live('click', function (){
		var $Row = $(this).parent().parent();
		showAjaxLoader($Row, 'normal');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=removeProductRow&id=' + $Row.attr('data-id')+oID),
			success: function (data){
				removeAjaxLoader($Row);
                $Row.remove();
			$('.priceEx').trigger('keyup');

			}
		});
	});

	$('.deleteIcon').live('click', function (){
        if($(this).parent().parent().attr('data-code') != 'subtotal' && $(this).parent().parent().attr('data-code') != 'total' && $(this).parent().parent().attr('data-code') != 'tax'){
            if (this.Tooltip){
                this.Tooltip.remove();
            }
            $(this).parent().parent().remove();
            $('.priceEx').trigger('keyup');
        }
	});
	
	$('select.country').live('change', function (){
		var $self = $(this);
        var state = $(this).parent().parent().parent().find('.stateCol select option:selected').val();
		showAjaxLoader($self, 'small');
		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=getCountryZones&addressType=' + $self.attr('data-address_type') + '&country=' + $self.val()+'&state='+state+oID),
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
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=processPayment'+oID),
			cache: false,
			dataType: 'json',
			data: $(this).parent().parent().find('*').serialize(),
			type: 'post',
			success: function (data){
				if (data.success == true){
					$('.paymentsTable tbody:nth-child(2)').append(data.tableRow);
                    if(data.status != ''){
                        $('select[name="status"]').val(data.status);
                    }
				}else if (typeof data.success == 'object'){
					alert(data.success.error_message);
				}else{
					alert('Payment Failed');
				}
				removeAjaxLoader($self);
			}
		});
	});

	$('select[name="payment_method"]').live('change',function(){
		var $self = $(this);
		showAjaxLoader($self, 'small');

		$.ajax({
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=changePaymentMethod'+oID),
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
				}
			}
		});
	});
	$('select[name="payment_method"]').trigger('change');
	$('select.country').trigger('change');
	//$('.purchaseType').trigger('change');
	$('.purchaseType').trigger('updateInfo');

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
						url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=refundPayment'+oID),
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
				url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=getShippingQuotes&totalCount=' + $self.parent().parent().attr('data-count')+oID),
				success: function (data){
					$self.parent().parent().find('td:eq(0)').html(data);
					removeAjaxLoader($self.parent().parent());
				}
			});
		}else{
			$self.parent().parent().find('td:eq(0)').html('<input class="ui-widget-content" type="text" style="width:98%;" name="order_total[' + $self.parent().parent().attr('data-count') + '][title]" value="">');
		}
	});
	
	function getTotalRow(type){
		return $('.orderTotalTable > tbody').find('tr[data-code=' + type + ']');
	}
	
	$('.orderTotalValue').live('keyup', function (){
		var total = 0;
		$('.orderTotalTable > tbody > tr').each(function (){
			if ($(this).data('code') == 'total'){
				return;
			}

			if ($(this).find('div.orderTotalValue').size() > 0){
				total += parseFloat($(this).find('.orderTotalValue').html());
			}else if ($(this).find('input.orderTotalValue').size() > 0){
				total += parseFloat($(this).find('.orderTotalValue').val());
			}
		});
		var $totalRow = getTotalRow('total');
		$totalRow.find('.orderTotalValue span').html(total);
		$totalRow.find('.orderTotalValue input').val(total);
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
	//data-webforms2-force-js-validation
	$('.saveAddressButton').click(function (){
        var canSubmit = true;
        $('#tabsCustomer input').each(function(){
            if($(this).is(':required') && !$(this).is(':valid') || $(this).is(':invalid')){
                canSubmit = false;
            }
        });
        if(!canSubmit){
            alert("Please select a Customer first");
            return false;
        }

		showAjaxLoader($('#tabsCustomer'), 'xlarge');

        $.ajax({
			cache: false,
			dataType: 'html',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveCustomerInfo'+oID),
			data: $('#tabsCustomer *').serialize(),
			type: 'post',
			success: function (data){
				//$('.ui-widget-content-middle, .trackingSection, .productSection, .totalSection, .paymentSection, .commentSection').show();
				$('select[name=payment_method]').change(function(){
					var $self = $(this);
					showAjaxLoader($self, 'small');

					$.ajax({
						url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=changePaymentMethod'+oID),
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
				removeAjaxLoader($('#tabsCustomer'));
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
	

	if (!getVars['error'] && !getVars['oID']){
		//$('.ui-widget-content-middle, .trackingSection, .productSection, .totalSection, .paymentSection, .commentSection').hide();
	}

    $('.startConsumption').each(function (){
        var $Row = $(this).parentsUntil('tbody').last();

        var attrv = 'idP='+ $Row.attr('data-id')+'&pID='+ $Row.attr('data-product_id');
        if ($Row.find('.productAttribute').size() > 0) {
            attrv = attrv + '&id[reservation]=';
            $Row.find('.productAttribute').each(function(){
                attrv = attrv+'{'+$(this).attr('attrval')+'}'+$(this).val();
            });

        }

        attrv = attrv + '&bar_id='
        var i = 0;
        $Row.find('.barcode').each(function(){
            attrv = attrv+$(this).find('.bar_id').attr('barid')+ '(' + $(this).find('.res_start_date').html() + '-' + $(this).find('.res_start_date').html() + ')' +',';
            i++;
        });

        var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
        var postData1 = attrv + '&has_info=1&start_date=' + $Row.find('.res_start_date').html() + '&end_date=' + $Row.find('.res_end_date').html() + '&shipping=0&qty='+$Row.find('.productQty').val()+'&purchase_type='+$purchaseTypeSelected.val();
        var addCartData = {};
        var postData = $.extend(addCartData, {
            shipping   : false,
            start_date : $Row.find('.res_start_date').html(),
            end_date   : $Row.find('.res_end_date').html(),
            qty        : $Row.find('.productQty').val(),
            bar_name   : $Row.find('.barcodeName').val(),
            bar_id     : $Row.find('.barcodeName').attr('barid')
        });

        $.ajax({
            cache: false,
            dataType: 'json',
            type:'post',
            data: postData1,
            url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'),
            success: function (postResp) {
                //update priceEx
                if($Row.hasClass('datesSelected') == false){
                    $Row.removeClass('nodatesSelected');
                    $Row.addClass('datesSelected');
                }

                $Row.find('.resDateHidden').val(postData.start_date + ',' + postData.end_date);
                $Row.find('.res_start_date').html(postData.start_date);
                $Row.find('.res_start_date').show();
                $Row.find('.res_end_date').html(postData.end_date);
                $Row.find('.res_end_date').show();
                $Row.find('.productQty').val(postData.qty);
                $Row.find('.barcodeName').val(postData.bar_name);
                $Row.find('.barcodeName').attr('barid', postData.bar_id);
                $Row.find('.priceEx').val(postResp.price).trigger('keyup');

                var $shippingInput = $Row.find('.reservationShipping');
                var $shippingText = $Row.find('.reservationShippingText');
                var $shipRadio = $(self).find('input[name="rental_shipping"]:checked');
                if ($shipRadio.size() > 0) {
                    var valShip = $shipRadio.val().split('_');
                    $shippingInput.val(valShip[1]);
                    $shippingText.html($shipRadio.parent().parent().find('td:eq(0)').html());
                }

            }
        });

    });

    function encodeParam(name, value)
    {
        if (value)
        {
            if (!value.toString() == "")
            {
                return name + "=" + escape( value ) + "&";
            }
        }
        return "";
    }

    $('#popShipRush').live('click', function (){
        window.location.href = btnGenerateShipURL_onclick();
        return false;
    });

    function btnGenerateShipURL_onclick() {
        var url =
            "nntp://" +
                "ship?" +
                encodeParam("Carrier", "FedEx" ) +
                encodeParam("AutoPrintLabel", false ) +
                encodeParam("ShipmentXML", getShippmentXML() );

        return url;
    }

    function getShippmentXML()
    {
        return '<?xml version = "1.0"?>' +
            '<Request>' +
            '<ShipTransaction>' +
            '<Shipment>' +
            '<UPSServiceType>FedEx Ground></UPSServiceType>' +
            '<ShipmentChgType>PRE</ShipmentChgType>' +
            '<DeliveryAddress>' +
            '<Address>' +
            '<FirstName><![CDATA[' + $('input[name="address[delivery][entry_name]"]').val() + ']]></FirstName>' +
            '<Company><![CDATA[' + $('input[name="address[delivery][entry_company]"]').val() + ']]></Company>' +
            '<Address1><![CDATA[' + $('input[name="address[delivery][entry_street_address]"]').val() + ']]></Address1>' +
            '<Address2></Address2>' +
            '<City><![CDATA[' + $('input[name="address[delivery][entry_city]"]').val() + ']]></City>' +
            '<State><![CDATA[' + $('.state_delivery option:selected').attr('iso_code') + ']]></State>' +
            '<PostalCode><![CDATA[' + $('input[name="address[delivery][entry_postcode]"]').val() + ']]></PostalCode>' +
            '<Country><![CDATA[' + $('.country option:selected').attr('iso_code') + ']]></Country>' +
            '</Address>' +
            '</DeliveryAddress>' +
            '<Package>' +
            '<PackagingType>02></PackagingType>' +
            '<PackageActualWeight></PackageActualWeight>' +
            '<InsuranceAmount><![CDATA[' + $('input[name="order_total[1][value]"]').val() + ']]></InsuranceAmount>' +
            '<PackageReference1></PackageReference1>' +
            '<PkgLength>10</PkgLength>' +
            '<PkgWidth>10</PkgWidth>' +
            '<PkgHeight>5</PkgHeight>' +
            '</Package>' +
            '</Shipment>' +
            '</ShipTransaction>' +
            '</Request>';

    }

});