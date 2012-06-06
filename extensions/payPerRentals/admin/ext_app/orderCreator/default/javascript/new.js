$(document).ready(function (){
	var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	var allowSelection = true;
	var productsID = 0;
	var selected = '';
	var autoChanged = false;
	var getVars = getUrlVars();
	var oID;
	if(getVars['oID']){
		oID = '&oID='+getVars['oID'];
	}else{
		oID = '';
	}


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
		var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
		showAjaxLoader($(mainField), 'small');
		$.ajax({
			cache: false,
			dataType: 'json',
			type:'post',
			data: attrv,
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadReservationData'+oID),
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
								var postData1 = attrv + '&start_date='+myStartDate+'&end_date='+myEndDate+'&days_before='+$(self).find('input[name="rental_shipping"]:checked').attr('days_before')+'&days_after='+$(self).find('input[name="rental_shipping"]:checked').attr('days_after')+'&shipping='+$(self).find('input[name="rental_shipping"]:checked').val()+'&qty='+$(self).find('.rental_qty').val()+'&purchase_type='+$purchaseTypeSelected.val();
								var addCartData = {};
								var postData = $.extend(addCartData, {
									start_date : myStartDate,
									end_date   : myEndDate,
									days_before   : $(self).find('input[name="rental_shipping"]:checked').attr('days_before'),
									days_after   : $(self).find('input[name="rental_shipping"]:checked').attr('days_after'),
									shipping   : $(self).find('input[name="rental_shipping"]:checked').val(),
									qty        : $(self).find('.rental_qty').val()
								});

								var insVal = -1;
								if($(self).find('.hasInsurance').attr('checked') == 'checked'){
									insVal = 1;
								}
								$.ajax({
									cache: false,
									dataType: 'json',
									type:'post',
									data: postData1,
									url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'+oID),
									success: function (postResp) {
										//update priceEx
										if($Row.hasClass('datesSelected') == false){
											$Row.removeClass('nodatesSelected');
											$Row.addClass('datesSelected');
										}
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


	$('.gatef, .eventInsurance').live('change', function(){
		var $self = $(this);
		var $Row =$(this).parentsUntil('tbody').last();
		var $ShippingInput = $Row.find('.reservationShipping option:selected');
		var $qtyInput = $Row.find('.productQty');
		var selectedQty = $qtyInput.val();

		var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
		var eventS = $Row.find('.eventf option:selected');
		var gateS = $Row.find('.gatef option:selected');
		var insurance = '';
		if($Row.find('.eventInsurance').attr('checked') == 'checked'){
			insurance = '&hasInsurance=1';
		}
		var mpDates = new Array();
		 $Row.find('.mpDates').each(function(){
		 mpDates.push($(this).val());
		 });
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			data:'idP=' + $Row.attr('data-id')+ '&event=' + eventS.val()+ '&gate=' + gateS.val() + '&shipping=' + $ShippingInput.val() + '&qty=' + selectedQty+'&purchase_type='+$purchaseTypeSelected.val()+'&days_before='+$ShippingInput.attr('days_before')+'&days_after='+$ShippingInput.attr('days_after')+insurance+'&multiple_dates='+mpDates,
			type:'post',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'+oID),
			success: function (data) {
				removeAjaxLoader($self);
				if(data.success == true){
					$Row.find('.priceEx').val(data.price).trigger('keyup');
					if($Row.hasClass('datesSelected') == false){
						$Row.removeClass('nodatesSelected');
						$Row.addClass('datesSelected');
					}
				}else{
					$Row.find('.eventf').val('0');
					alert('There is no available item for the selected event. Make your Selection again.');
				}

			}
		});
	});


	$('.eventf, .reservationShipping').live('change', function(){
		var $self = $(this);
		var $Row =$(this).parentsUntil('tbody').last();
		var $ShippingInput = $Row.find('.reservationShipping option:selected');
		var $qtyInput = $Row.find('.productQty');
		var selectedQty = $qtyInput.val();

        var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
		var eventS = $Row.find('.eventf option:selected');
		var gateS = $Row.find('.gatef option:selected');
		var insurance = '';
		if($Row.find('.eventInsurance').attr('checked') == 'checked'){
			insurance = '&hasInsurance=1';
		}
		var mpDates = new Array();
		var notOpen = 0;
		if($Row.find('.eventf').attr('isTriggered') == '1'){
			$Row.find('.eventf').attr('isTriggered','0');
			$Row.find('.mpDates').each(function(){
				mpDates.push($(this).val());
			});
			$('.mydates').remove();

			notOpen = 0;
		}
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			data:'idP=' + $Row.attr('data-id')+ '&event=' + eventS.val()+ '&gate=' + gateS.val() + '&shipping=' + $ShippingInput.val() + '&qty=' + selectedQty+'&purchase_type='+$purchaseTypeSelected.val()+'&days_before='+$ShippingInput.attr('days_before')+'&days_after='+$ShippingInput.attr('days_after')+insurance+'&multiple_dates='+mpDates,
			type:'post',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'),
			success: function (data) {
				removeAjaxLoader($self);
				if(data.success == true){
					//if(notOpen == 0){
					$Row.find('.priceEx').val(data.price).trigger('keyup');
					if($Row.hasClass('datesSelected') == false){
						$Row.removeClass('nodatesSelected');
						$Row.addClass('datesSelected');
					}
					if(typeof data.calendar != ''){
						$Row.find('.myCalendar').remove();
						$Row.find('.myTextCalendar').remove();
						$Row.find('.calDone').remove();
						$Row.find('.closeCal').remove();

						$self.closest('td').append(data.calendar);
						$Row.find('.calDone').css('color','#000000');
						$Row.find('.calDone').css('cursor','pointer');
						$Row.find('.calDone').click(function(){
							if($Row.find('.myCalendar').is(':visible')){
								$Row.find('.myCalendar').hide();
								$(this).html('Choose Dates');
								$Row.find('.myTextCalendar').hide();
								$Row.find('.allCalendar').hide();
								$Row.find('.closeCal').hide();
								$(this).css('position','relative');
								$(this).css('top', '0px');

							}else{
								$Row.find('.myTextCalendar').show();
								$Row.find('.myCalendar').show();
								$(this).css('position','relative');
								$(this).css('top', '220px');
								$(this).css('color', '#000000');
								$(this).css('z-index', '10005');
								$(this).css('left', '30px');
								$Row.find('.closeCal').css('position','relative');
								$Row.find('.closeCal').css('top', '-30px');
								$Row.find('.closeCal').css('z-index', '10005');
								$Row.find('.closeCal').css('left', '230px');
								$Row.find('.closeCal').css('cursor', 'pointer');
								$Row.find('.closeCal').css('width', '15px');
								$Row.find('.allCalendar').show();
								$Row.find('.closeCal').show();
								$(this).html('<div class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"><span class="ui-button-text">Done Selecting Dates</span></div>');
							}
							var mpDates = new Array();
							$Row.find('.mpDates').each(function(){
								mpDates.push($(this).val());
							});
							//showAjaxLoader($self, 'x-large');
							$.ajax({
								cache: false,
								dataType: 'json',
								data:'idP=' + $Row.attr('data-id')+ '&event=' + eventS.val()+ '&gate=' + gateS.val() + '&shipping=' + $ShippingInput.val() + '&qty=' + selectedQty+'&purchase_type='+$purchaseTypeSelected.val()+'&days_before='+$ShippingInput.attr('days_before')+'&days_after='+$ShippingInput.attr('days_after')+insurance+'&multiple_dates='+mpDates,
								type:'post',
								url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'),
								success: function (data) {

									if(data.success == true){
										$Row.find('.priceEx').val(data.price).trigger('keyup');
										if($Row.hasClass('datesSelected') == false){
											$Row.removeClass('nodatesSelected');
											$Row.addClass('datesSelected');
										}
									}else{
										$Row.find('.eventf').val('0');
										alert('There is no available item for the selected event. Make your Selection again.');
									}
									//removeAjaxLoader($self);
								}
							});


						});
						$Row.find('.closeCal').click(function(){
							$Row.find('.calDone').trigger('click');
						});
						$Row.find('.allCalendar').css('position','absolute');
						$Row.find('.allCalendar').css('top', '0px');
						$Row.find('.allCalendar').css('padding', '10px');
						$Row.find('.allCalendar').css('padding-bottom', '55px');
						$Row.find('.allCalendar').css('background-color','#ffffff');
						$Row.find('.allCalendar').css('z-index', '1005');
						$Row.find('.allCalendar').css('left', '10px');
						var myInitialDates = $('.mydates').html();
						var goodDates = jQuery.makeArray(data.goodDates);
						$Row.find('.myCalendar').datepick({
							useThemeRoller: true,
							dateFormat: 'mm/dd/yy',
							multiSelect: 999,
							multiSeparator: ',',
							defaultDate: data.events_date,
							changeMonth: false,
							firstDay:0,
							changeYear: false,
							numberOfMonths: 1,
							onSelect: function (value, date, inst) {
								var dates = value.split(',');
								html = '<div class="mydates">';
								for(p=0;p<dates.length;p++){
									html += '<input type="hidden" class="mpDates" name="multiple_dates[]" value="'+dates[p]+'">';
								}
								html +='</div>';
								$Row.find('.mydates').remove();
								$self.closest('td').append(html);
							},

							beforeShowDay: function (dateObj) {
								dateObj.setHours(0, 0, 0, 0);
								var dateFormatted = $.datepick.formatDate('yy-m-d', dateObj);
								today = new Date();
								if ($.inArray(dateFormatted, goodDates) > -1 && (today.getTime() <= dateObj.getTime() - (1000 * 60 * 60 * 24 - (24 - dateObj.getHours()) * 1000 * 60 * 60))){
									return [true, '', 'Available'];
								}

								return [false, '', 'Outside event days'];
							}
						});
						$Row.find('.myCalendar').hide();
						$Row.find('.myTextCalendar').hide();
						if(data.selectedDates != ''){
							var selDates =  jQuery.makeArray(data.selectedDates);
							/*var selDatesArr = new Array();
							for(var u=0;u<selDates.length;u++){
								var p = $.datepicker.parseDate('mm/dd/yy', selDates[u]);
								selDatesArr.push(selDates[u]);
							}
							//var selDates = '08/09/2012,08/23/2012'.split(',');*/
							$Row.find('.myCalendar').datepick('setDate', selDates);
							$Row.find('.calDone').trigger('click');
							$Row.find('.calDone').trigger('click');
						}else{

							$Row.find('.calDone').trigger('click');
							//$('.calDone').trigger('click');
						}
						if($Row.find('.eventf').attr('isTriggered') == '1'){
							$Row.find('.eventf').attr('isTriggered','0');
							//$('.calDone').trigger('click');
						}
					}
					//}
				}else{
					$Row.find('.eventf').val('0');
					alert('There is no available item for the selected event. Make your Selection again.');
				}

			}
		});
	});
	$('.saveOrder').click(function(){
		var canpass = true;
		if($('.productTable tbody tr').size() == 0){
			canpass = false;
		}
		$('.productTable tbody tr').each(function(){
			if($(this).hasClass('nodatesSelected')){
				alert('You have to select dates for all reservation products on the order');
				canpass = false;
			}

		});
		return canpass;
	});
});