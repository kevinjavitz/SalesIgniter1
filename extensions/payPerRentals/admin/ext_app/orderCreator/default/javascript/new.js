$(document).ready(function (){
	var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	var allowSelection = true;
	var productsID = 0;
	var selected = '';
	var autoChanged = false;

	$('.reservationDates').live('click', function (){
		var mainField = this;
        var $Row = $(this).parentsUntil('tbody').last();

		var attrv = 'idP='+ $Row.attr('data-id')+'&pID='+ $Row.attr('data-product_id');
		if ($Row.find('.productAttribute').size() > 0) {
			attrv = attrv + '&id[reservation]=';
			$Row.find('.productAttribute').each(function(){
				attrv = attrv+'{'+$(this).attr('attrval')+'}'+$(this).val();
			});

		}
		showAjaxLoader($(mainField), 'small');
		$.ajax({
			cache: false,
			dataType: 'json',
			type:'post',
			data: attrv,
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadReservationData'),
			success: function (data) {
				var $dialog = $('<div></div>').dialog({
					title: 'Select Reservation Settings',
					width: 600,
					height: 561,
					open: function (){
						$(this).html(data.calendar);
					},
					close:function(){
						removeAjaxLoader($(mainField));
						$dialog.dialog('destroy').remove();
					},
					buttons: {
						'Add To Cart': function (){
							var self = this;
							if($(self).find('.start_date').val() != '' && $(self).find('.start_date').val() != ''){
								showAjaxLoader($(self).parent(), 'large');
								var myStartDate = $(self).find('.start_date').val();

								if($(self).find('.start_time').size() > 0){
									myStartDate = myStartDate+' '+$(self).find('.start_time').val();
								}

								var myEndDate = $(self).find('.end_date').val();

								if($(self).find('.end_time').size() > 0){
									myEndDate = myEndDate+' '+$(self).find('.end_time').val();
								}
								var postData1 = attrv + '&start_date='+myStartDate+'&end_date='+myEndDate+'&days_before='+$(self).find('input[name="rental_shipping"]:checked').attr('days_before')+'&days_after='+$(self).find('input[name="rental_shipping"]:checked').attr('days_after')+'&shipping='+$(self).find('input[name="rental_shipping"]:checked').val()+'&qty='+$Row.find('.productQty').val();
								var addCartData = {};
								var postData = $.extend(addCartData, {
									start_date : myStartDate,
									end_date   : myEndDate,
									days_before   : $(self).find('input[name="rental_shipping"]:checked').attr('days_before'),
									days_after   : $(self).find('input[name="rental_shipping"]:checked').attr('days_after'),
									shipping   : $(self).find('input[name="rental_shipping"]:checked').val(),
									qty        : $Row.find('.productQty').val()
								});

								var insVal = -1;
								if($(self).find('.hasInsurance').attr('checked') == true){
									insVal = 1;
								}
								$.ajax({
									cache: false,
									dataType: 'json',
									type:'post',
									data: postData1,
									url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'),
									success: function (postResp) {
										//update priceEx

										$Row.find('.resDateHidden').val(postData.start_date + ',' + postData.end_date);
										$Row.find('.res_start_date').html(postData.start_date);
										$Row.find('.res_end_date').html(postData.end_date);
										$Row.find('.productQty').val(postData.qty);
										$Row.find('.priceEx').val(postResp.price).trigger('keyup');

										var $shippingInput = $Row.find('.reservationShipping');
										var $shippingText = $Row.find('.reservationShippingText');
										var $shipRadio = $(self).find('input[name="rental_shipping"]:checked');
										if ($shipRadio.size() > 0) {
											var valShip = $shipRadio.val().split('_');
											$shippingInput.val(valShip[1]);
											$shippingText.html($shipRadio.parent().parent().find('td:eq(0)').html());
										}

										removeAjaxLoader($(self).parent());
										removeAjaxLoader($(mainField));
										$dialog.dialog('destroy').remove();
									}
								});
							}else{
								alert('Please select dates');
							}
						}
					}
				});
			}
		})

	});


	$('.productAttribute').live('change', function (){
        var $Row = $(this).parentsUntil('tbody').last();
		$Row.find('.reservationDates').val('');
	});


	$('.eventf, .reservationShipping, .gatef').live('change', function(){
		var $self = $(this);
		var $Row =$(this).parentsUntil('tbody').last();
		var $ShippingInput = $Row.find('.reservationShipping option:selected');
		var $qtyInput = $Row.find('.productQty');
		var selectedQty = $qtyInput.val();

        var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
		var eventS = $Row.find('.eventf option:selected');
		var gateS = $Row.find('.gatef option:selected');
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			data:'idP=' + $Row.attr('data-id')+ '&event=' + eventS.val()+ '&gate=' + gateS.val() + '&shipping=' + $ShippingInput.val() + '&qty=' + selectedQty+'&purchase_type='+$purchaseTypeSelected.val()+'&days_before='+$ShippingInput.attr('days_before')+'&days_after='+$ShippingInput.attr('days_after'),
			type:'post',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'),
			success: function (data) {

				if(data.success == true){
					$Row.find('.priceEx').val(data.price).trigger('keyup');
				}else{
					$Row.find('.eventf').val('0');
					alert('There is no available item for the selected event. Make your Selection again.');
				}
				removeAjaxLoader($self);
			}
		});
	});

});