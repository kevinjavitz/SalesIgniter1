function updateTotals(){
    var isShip = false;
    $(':radio[name="shipping_method"]:checked').each(function(){
                         $(this).click();
                        isShip = true;
    });
	if($(':hidden[name="shipping_method"]')){
		setOnlyShippingMethod();
		isShip = true;
	}
    if (isShip == false){
         $('.orderTotalsList').each(function (){
                    showAjaxLoader($(this), 'large');
         });
         
         var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
         $.ajax({
                url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=updateTotals'),
                type: 'post',
                dataType: 'json',
                success: function (data) {
					if(data.redirectUrl != ''){
						js_redirect(data.redirectUrl);
					}
                   $('.orderTotalsList').each(function () {
                        removeAjaxLoader($(this), 'large', 'append');
                    });
                    $('.orderTotalsList').html(data.orderTotalRows);
                }
         });
    }
}

function setOnlyShippingMethod(){
	if($(':radio[name="shipping_method"]')){}else {
		$('.orderTotalsList').each(function (){
			showAjaxLoader($(this).parent(), 'large');
		});
		var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
		$.ajax({
			url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=setShippingMethod'),
			cache: false,
			dataType: 'json',
			type: 'post',
			data: 'shipping_method=' + $(':hidden[name="shipping_method"]').val(),
			success: function (data){
				$('.orderTotalsList').each(function (){
					removeAjaxLoader($(this).parent(), 'large', 'append');
				});
				$('.orderTotalsList').html(data.orderTotalRows);
			}
		});
	}
}
$(document).ready(function (){
	$('.shippingAddressDiff').live('click',function (){
		if (this.checked){
			$('.shippingAddress').show();
		}else{
			$('.shippingAddress').hide();
		}
	});

	$('.pickupAddressDiff').live('click',function (){
		if (this.checked){
			$('.pickupAddress').show();
		}else{
			$('.pickupAddress').hide();
		}
	});

	$('.createAccountButton').live('click',function (){
		if (this.checked){
			$('.accountSettings').show();
		}else{
			$('.accountSettings').hide();
		}
	});

	 $('#printOrder').live('click', function(){
		window.print();
	 });

     $('input[name="qty"]').live('change',function () {
         var $elem = $(this).parent();
         showAjaxLoader($elem, 'xlarge');
         var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
       $.ajax({
            url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=updateQuantity'),
            data: 'pID=' + $(this).attr('pID')+'&type='+$(this).attr('ptype')+'&qty='+$(this).val(),
            type: 'post',
            dataType: 'json',
            success: function (data) {
                removeAjaxLoader($elem);
				$('#checkoutShoppingCart').html(data.pageHtml);
                updateTotals();
            }
        });
    });

    /*redeem*/
    $('input[name="redeem_code"]').live('focus',function () {
        if ($(this).val() == 'redeem code') {
            $(this).val('');
        }
    });

    $('.removeFromCart').live('click', function(){
       var $elem = $(this).parent();
       showAjaxLoader($elem, 'xlarge');
       var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
       $.ajax({
            url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=removeProduct'),
            data: 'pID=' + $(this).attr('pID')+'&type='+$(this).attr('type'),
            type: 'post',
            dataType: 'json',
            success: function (data) {
                removeAjaxLoader($elem);
                if (data.empty == false){
				    $('#checkoutShoppingCart').html(data.pageHtml);
                    updateTotals();
                }else{
                    js_redirect(js_app_link('app=shoppingCart&appPage=default'));
                }
            }
        });
        return false;
    });
    $('#voucherRedeem').live('click', function () {
            $('.orderTotalsList').each(function (){
                showAjaxLoader($(this), 'large');
            });
            var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
       $.ajax({
            url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=redeemVoucher'),
            data: 'code=' + $('input[name="redeem_code"]').val(),
            type: 'post',
            dataType: 'json',
            success: function (data) {
               $('.orderTotalsList').each(function () {
                    removeAjaxLoader($(this), 'large', 'append');
                });
				if (data.errorMsg != ''){
					alert(data.errorMsg);
				}
	            $('.orderTotalsList').html(data.orderTotalRows);
	            //updateTotals();
            }
        });
        return false;
    });
    /*end redeem*/
    /*saved address*/
    $('#changeBillingAddress, #changeShippingAddress, #changePickupAddress').live('click',function (){
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
             var $dialog = $(this);
            showAjaxLoader($dialog.parent(), 'xlarge');
            var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
                 $.ajax({
                    url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=getAddressBook'),
                    data: 'addressType=' + addressType,
                    type: 'post',
                    success: function (data){
                        $dialog.html(data);
                        hideAjaxLoader($dialog.parent(), 'xlarge');
                    }
                });
			},
			buttons: {
				'Cancel': function (){
						var self = $(this);
						var action = $('input[name="action"]', self).val();
						if (action == 'selectAddress'){
							self.dialog('close');
						}
				},
				'Continue': function (){
						var $this = $(this);
						var action = $('input[name="action"]', $this).val();
						//alert($(':input, :select, :radio, :checkbox', this).serialize());
						if (action == 'selectAddress'){
							  showAjaxLoader($this.parent(), 'xlarge');
							  var linkParams = js_get_all_get_params(['app', 'appPage', 'action', 'type']);
							   $.ajax({
                                url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=process&type=addressBook'),
								dataType: 'json',
								data: $(':input, :radio', this).serialize(),
								type: 'post',
								success: function (data){
									$this.dialog('close');
                                    hideAjaxLoader($this.parent(), 'xlarge');
									$('.checkoutContent').html(data.pageHtml);
                                    $('#changeBillingAddress').button();
                                    $('#changeShippingAddress').button();
                                    $('#changePickupAddress').button();
									if (data.isShipping == true){
                                        $('.shippingAddressDiff').trigger('click');
                                        $('.shippingAddress').show();
                                    }
                                    if (data.isPickup == true){
                                        $('.pickupAddressDiff').trigger('click');
                                        $('.pickupAddress').show();
                                    }
								}
							});
						}
					}

            }});
		return false;
	});
    /*saved addresses*/

    $('#changeBillingAddress').button();
    $('#changeShippingAddress').button();
    $('#changePickupAddress').button();
    $('#insure_button').live('click',function(){
    	var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
			var url = js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=saveInsuranceCheckboxes');
            var $tableInsure = $(this).parent().parent().parent();
			showAjaxLoader($tableInsure, 'xlarge');
			$.ajax({
				cache: false,
				url: url,
				type: 'post',
				data: $('#insure_form *').serialize(),
				dataType: 'json',
				success: function (data){
					hideAjaxLoader($tableInsure);
                    $('#checkoutShoppingCart').html(data.pageHtml);
                    updateTotals();
                    if(data.isRemove == true){
                        $('#insuranceTextRemove').show();
                        $('#insuranceText').hide();
                    }else{
                        $('#insuranceTextRemove').hide();
                        $('#insuranceText').show();
                    }
                    $('#insure_button').button();
				}
			});
			return false;
	});
    $('#loginButton').button();
	$('#continueButton').click(function (){
         window.scrollTo(0,100);
        //validate_form for currentpage addresses and shipping_payment
        if ($('#currentPage').val() == 'payment_shipping'){
            	if ($(':radio[name="payment_method"]:checked').size() <= 0){
                    if ($('input[name="payment_method"]:hidden').size() <= 0){
                        alert('Please Select a Payment Method');
                        return false;
                    }
                }
	        if($(':hidden[name="shipping_method"]:checked')){
		        setOnlyShippingMethod();
	        }
        }
        if ($('#currentPage').val() == 'success'){
            js_redirect(DIR_WS_CATALOG);
            return false;
        }
		showAjaxLoader($('.checkoutContent'), 'xlarge', 'dialog');
		var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
		$.ajax({
			url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=process'),
			cache: false,
			dataType: 'json',
			data: $('form[name=checkout]').serialize(),
			type: 'post',
			success: function (data){
				removeAjaxLoader($('.checkoutContent'));
				$('.checkoutContent').html(data.pageHtml);
                if (data.isShipping == true) {
                    $('.shippingAddressDiff').trigger('click');
                    $('.shippingAddress').show();
                }
                if (data.isPickup == true) {
                    $('.pickupAddressDiff').trigger('click');
                    $('.pickupAddress').show();
                }
                if ($('#currentPage').val() == 'processing'){
                     $('#continueButton').hide();
                    $('.breadCrumb').html('<a class="headerNavigation" href="'+js_app_link('app=index&appPage=default')+'">You Are Here: Home</a> &raquo; Checkout &raquo; Processing');
                }else{
                     $('#continueButton').show();
                }                
                if ($('#currentPage').val() == 'success'){
                    $('#checkoutMessage').hide();
                    $('#bar_step1').hide();
                    $('#bar_step2').hide();
                    $('#bar_step3').show();
                    $('#continueButton').find('.ui-button-text').html(CONTINUE_TO_HOMEPAGE);
					$('#printOrder').button();
                    $('.breadCrumb').html('<a class="headerNavigation" href="'+js_app_link('app=index&appPage=default')+'">You Are Here: Home</a> &raquo; Checkout &raquo; Order Processed');
                }else
                if ($('#currentPage').val() != 'addresses'){	              

                     $('#voucherRedeem').button();
                     $('#gcRedeem').button();
                     $('#agreeMessage').hide();
                     $('#bar_step1').hide();
                     $('#bar_step2').show();
                     $('#bar_step3').hide();
                     $('#continueButton').find('.ui-button-text').html(TEXT_CONFIRM_ORDER);

					 if($(':radio[name="shipping_method"]:checked').size() == 0){
						 $(':radio[name="shipping_method"]').each(function(){
							 $(this).trigger('click');
						 });
					 }else{
						 $(':radio[name="shipping_method"]:checked').each(function(){
                        	 $(this).trigger('click');
                     	 });
					 }
			if($(':hidden[name="shipping_method"]:checked')){
	                    setOnlyShippingMethod();
	                }
	                $('.shipInfo').css('cursor','pointer');
	                 $('.shipInfo').click(function(){
		                 link = js_app_link('appExt=payPerRentals&app=show_shipping&appPage=default_all&dialog=true');
		                 popupWindow(link,'400','300');
		                 return false;
	                 });
	                if($(':radio[name="payment_method"]:checked').size() == 0){
		                var p=0;
		                $(':radio[name="payment_method"]').each(function(){
			                p++;
			                if(p == 1){
				                $(this).trigger('click');
			                }
		                });
	                }else{
		                $(':radio[name="payment_method"]:checked').each(function(){
			                $(this).trigger('click');
		                });
	                }

                     $('.breadCrumb').html('<a class="headerNavigation" href="'+js_app_link('app=index&appPage=default')+'">You Are Here: Home</a> &raquo; Checkout &raquo; Payment & Shipping');
					 $('#insure_button').button();
                      try{
						if($(':radio[name="plan_id"]:checked').size() == 0){
							$(':radio[name="plan_id"]').each(function(){
								$(this).trigger('click');
							});
						} else{
							$(':radio[name="plan_id"]:checked').each(function(){
								$(this).trigger('click');
							});
						}
	                }catch(err){
	                }
                	if ($('.rentalPlans').length <= 0){
                        updateTotals();
                	}
                    if ($('.giftCertificates').length <= 0){
                        updateTotals();
                    }
                }
                $('#loginButton').button();
                $('#changeBillingAddress').button();
                $('#changeShippingAddress').button();
                $('#changePickupAddress').button();
                window.scrollTo(0,0);
			}
		});
		return false;
	});

	$('.moduleRow').live('mouseover mouseout click', function (e){		
		if (e.type == 'click'){
			if (!$(this).find(':radio').is(':checked')){
				$(this).parent().find(':checked').removeAttr('checked');
				$(this).find(':radio').attr('checked', 'checked').click();
			}
		}else if (e.type == 'mouseover'){
			$(this).addClass('ui-state-hover');
		}else{
			$(this).removeClass('ui-state-hover');
		}
	});

	$('input[name=payment_method]').live('click', function (){
		$('.paymentFields').hide();
		$('.paymentFields *').attr('disabled', 'disabled');
		$('.paymentRow.ui-state-active').removeClass('ui-corner-all ui-state-active');
		$(this).parent().parent().removeClass('ui-state-hover').addClass('ui-corner-all ui-state-active');
		$(this).parent().parent().find('.paymentFields').show();
		$(this).parent().parent().find('.paymentFields *').removeAttr('disabled');

		$('.orderTotalsList').each(function (){
			showAjaxLoader($(this).parent(), 'large');
		});
		var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
		$.ajax({
			url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=setPaymentMethod'),
			cache: false,
			dataType: 'json',
			type: 'post',
			data: 'payment_method=' + $(this).val(),
			success: function (data){
				$('.orderTotalsList').each(function (){
					removeAjaxLoader($(this).parent(), 'large', 'append');
				});
				$('.orderTotalsList').html(data.orderTotalRows);
				//updateTotals();
			}
		});
	});

    $('.rentalPlans').live('click', function () {
       $('.orderTotalsList').each(function (){
			showAjaxLoader($(this), 'large');
		});
		var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
        $.ajax({
            url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=setMembershipPlan'),
            cache: false,
            dataType: 'json',
            type: 'post',
            data: 'planID=' + $(this).val(),
            success: function (data) {
                $('.orderTotalsList').each(function () {
                    removeAjaxLoader($(this), 'large', 'append');
                });
                $('.orderTotalsList').html(data.orderTotalRows);
            }
        });

    });

    $('select[name=billing_country], select[name=shipping_country], select[name=pickup_country]').live('change', function (){
        var stateTypeArr = $(this).attr('name').split('_');
        var stateType = stateTypeArr[0]+'_state';
        var $stateColumn = $('#'+stateType);
        if($stateColumn.size() > 0){
            showAjaxLoader($stateColumn, 'large');
            var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
            $.ajax({
                url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=getCountryZones'),
                cache: false,
                dataType: 'html',
                data: 'cID=' + $(this).val()+'&state_type='+stateType,
                success: function (data){
                    removeAjaxLoader($stateColumn);
                    $('#'+stateType).replaceWith(data);
                }
            });
        }
    });
	$('input[name=shipping_method]').live('click', function (){
		$('.shippingRow.ui-state-active').removeClass('ui-corner-all ui-state-active');
		$(this).parent().parent().removeClass('ui-state-hover').addClass('ui-corner-all ui-state-active');

		$('.orderTotalsList').each(function (){
			showAjaxLoader($(this).parent(), 'large');
		});
		var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
		$.ajax({
			url: js_app_link(linkParams + 'rType=ajax&app=checkout&appPage=default&action=setShippingMethod'),
			cache: false,
			dataType: 'json',
			type: 'post',
			data: 'shipping_method=' + $(this).val(),
			success: function (data){
				$('.orderTotalsList').each(function (){
					removeAjaxLoader($(this).parent(), 'large', 'append');
				});
				$('.orderTotalsList').html(data.orderTotalRows);
			}
		});
	});
});
