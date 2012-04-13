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
		var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
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
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'),
			success: function (data) {
				removeAjaxLoader($self);
				if(data.success == true){
					$Row.find('.priceEx').val(data.price).trigger('keyup');
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
		/*var mpDates = new Array();
		$Row.find('.mpDates').each(function(){
			mpDates.push($(this).val());
		});*/
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			data:'idP=' + $Row.attr('data-id')+ '&event=' + eventS.val()+ '&gate=' + gateS.val() + '&shipping=' + $ShippingInput.val() + '&qty=' + selectedQty+'&purchase_type='+$purchaseTypeSelected.val()+'&days_before='+$ShippingInput.attr('days_before')+'&days_after='+$ShippingInput.attr('days_after')+insurance,//+'&multiple_dates='+mpDates
			type:'post',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo'),
			success: function (data) {
				removeAjaxLoader($self);
				if(data.success == true){
					$Row.find('.priceEx').val(data.price).trigger('keyup');

					if(typeof data.calendar != ''){
						$('.myCalendar').remove();
						$('.myTextCalendar').remove();
						$('.calDone').remove();
						$('.closeCal').remove();

						$self.closest('td').append(data.calendar);
						$('.calDone').css('color','#000000');
						$('.calDone').css('cursor','pointer');
						$('.calDone').click(function(){
							if($('.myCalendar').is(':visible')){
								$('.myCalendar').hide();
								$(this).html('Choose Dates');
								$('.myTextCalendar').hide();
								$('.allCalendar').hide();
								$('.closeCal').hide();
								$(this).css('position','relative');
								$(this).css('top', '0px');

							}else{
								$('.myTextCalendar').show();
								$('.myCalendar').show();
								$(this).css('position','relative');
								$(this).css('top', '220px');
								$(this).css('color', '#000000');
								$(this).css('z-index', '10005');
								$(this).css('left', '30px');
								$('.closeCal').css('position','relative');
								$('.closeCal').css('top', '-30px');
								$('.closeCal').css('z-index', '10005');
								$('.closeCal').css('left', '230px');
								$('.closeCal').css('cursor', 'pointer');
								$('.closeCal').css('width', '15px');
								$('.allCalendar').show();
								$('.closeCal').show();
								$(this).html('<div class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary"><span class="ui-button-text">Done Selecting Dates</span></div>');
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
						$('.closeCal').click(function(){
							$('.calDone').trigger('click');
						});
						$('.allCalendar').css('position','absolute');
						$('.allCalendar').css('top', '0px');
						$('.allCalendar').css('padding', '10px');
						$('.allCalendar').css('padding-bottom', '55px');
						$('.allCalendar').css('background-color','#ffffff');
						$('.allCalendar').css('z-index', '1005');
						$('.allCalendar').css('left', '10px');
						var myInitialDates = $('.mydates').html();
						var goodDates = jQuery.makeArray(data.goodDates);
						$('.myCalendar').datepick({
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
								$('.mydates').remove();
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
						$('.myCalendar').hide();
						$('.myTextCalendar').hide();

						if(data.selectedDates != ''){
							var selDates =  jQuery.makeArray(data.selectedDates);
							//var selDates = '08/21/2011,08/23/2011'.split(',');
							$('.myCalendar').datepick('setDate', selDates);
							$('.calDone').trigger('click');
							$('.calDone').trigger('click');
						}else{

							$('.calDone').trigger('click');
							//$('.calDone').trigger('click');
						}
					}

				}else{
					$Row.find('.eventf').val('0');
					alert('There is no available item for the selected event. Make your Selection again.');
				}

			}
		});
	});

});