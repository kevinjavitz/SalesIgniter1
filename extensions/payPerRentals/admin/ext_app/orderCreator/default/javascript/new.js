$(document).ready(function (){
	var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	var allowSelection = true;
	var productsID = 0;
	var selected = '';
	var autoChanged = false;

	$('.reservationDates').live('click', function (){
        var $Row = $(this).parent().parent().parent().parent();
        var $self = $Row.find('.selectDialog');
        var $calendarTime = $Row.find('.calendarTime');
        var $datePicker = $Row.find('.datePicker');
        var $refreshCal = $Row.find('.refreshCal');
		if ($self.data('dialogCreated') === undefined){
            var $selfInput = $(this);
            var $AttrInput = $Row.find('.productAttribute');
			var $qtyInput = $Row.find('.productQty');
	        var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
			var selectedQty = $qtyInput.val();
			productsID = $Row.attr('data-id');
            var attrParams = '';
			if ($AttrInput){
				attrParams = 'id[reservation]['+$AttrInput.attr('attrval')+']='+$AttrInput.val();
			}
            $refreshCal.button();
			showAjaxLoader($selfInput, 'small');
			$.ajax({
				cache: false,
				dataType: 'json',
                type:'post',
                data: attrParams,
				url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadReservationData&id=' + $Row.attr('data-id')+'&qty='+selectedQty+'&purchase_type='+$purchaseTypeSelected.val()),
				success: function (data){

					$self.html(data.calendar);

					$('#inCart').click(function(){
						showAjaxLoader($selfInput, 'x-large');
						$.ajax({
							cache: false,
							dataType: 'json',
							type:'post',
							data: attrParams,
							url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id') + '&start_date=' + $self.data('start_date').toString() + '&end_date=' + $self.data('end_date').toString() + '&shipping=' + $self.data('selectedShippingMethod') + '&qty=' + $self.data('selectedQty') + '&purchase_type=' + $purchaseTypeSelected.val()),
							success: function (data) {
								//update priceEx
								$Row.find('.priceEx').val(data.price).trigger('keyup');
								$self.hide();
								$selfInput.val($self.data('start_date').toString() + ',' + $self.data('end_date').toString());
								removeAjaxLoader($selfInput);
							}
						});
					});

                    $self.css('width','600px');
                    //$self.css('width','600px');
                    $self.css('position','absolute');
                    var posi = $selfInput.offset();
                    $self.css('top',(posi.top-200)+'px');
                    $self.css('left',(posi.left+100)+'px');
                    //$self.dialog({ minWidth: 600 });
                    $self.data('dialogCreated', true);
                    $calendarTime.hide();
					removeAjaxLoader($selfInput);
					$self.focus();
				}
			})
		}else{

        $self.show();

        }
	});
	$('.eventf').live('change', function(){
		var $self = $(this);
		var $Row = $(this).parent().parent().parent().parent();
		//productsID = $Row.attr('data-id');
		//$Row.find('.reservationDates').val('');
		//alert('Now Choose the dates of the Event');
		var $ShippingInput = $Row.find('.reservationShipping option:selected');
		var $qtyInput = $Row.find('.productQty');
		var selectedQty = $qtyInput.val();
		/*
		var reservationDates = $Row.find('.reservationDates').val();
		var mydates = reservationDates.split(',');
		var startDateInput = mydates[0];
		var endDateInput = mydates[1];
		var eventS = $Row.find('.eventf option:selected');*
        */
        var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
		var eventS = $Row.find('.eventf option:selected');
		showAjaxLoader($self, 'x-large');
		$.ajax({
			cache: false,
			dataType: 'json',
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id')+ '&event=' + eventS.val() + '&shipping=' + $ShippingInput.val() + '&qty=' + selectedQty+'&purchase_type='+$purchaseTypeSelected.val()),
			success: function (data) {

				if(data.success == true){
					$Row.find('.priceEx').val(data.price).trigger('keyup');
				}else{
					//$Row.find('.eventf').val(0);
					alert('There is no available item for the selected event. Make your Selection again.');
				}
				removeAjaxLoader($self);
			}
		});
	});
	$('.reservationShipping').live('change', function(){
		var $self = $(this);
		var $Row = $(this).parent().parent().parent().parent();
		//productsID = $Row.attr('data-id');
		//var $ShippingInput = $Row.find('.reservationShipping option:selected');
		//var $qtyInput = $Row.find('.productQty');
		//var selectedQty = $qtyInput.val();
		//showAjaxLoader($self, 'x-large');
		var reservationDatesInput = $Row.find('.reservationDates');

		if (reservationDatesInput.val()){
			reservationDatesInput.val('');
		}else{
			//$Row.find('.eventf').val(0);
		}
		alert('Make your selection again');

	});

    /*productsqty*/
    $('.productQty').live('blur',function (){
		var $Row = $(this).parent().parent();
		var $self = $Row.find('.selectDialog');
        var $calendarTime = $Row.find('.calendarTime');
        var $datePicker = $Row.find('.datePicker');
        var $refreshCal = $Row.find('.refreshCal');
		if ($self.data('dialogCreated') === undefined){
            var $selfInput =  $Row.find('.reservationDates');
            var $AttrInput = $Row.find('.productAttribute');
			var $qtyInput = $(this);
			var selectedQty = $qtyInput.val();
			productsID = $Row.attr('data-id');
            var attrParams = '';
			if ($AttrInput){
				attrParams = 'id[reservation]['+$AttrInput.attr('attrval')+']='+$AttrInput.val();
			}
            $refreshCal.button();
		    //var $datesInput = $Row.find('.reservationDates');

			//$datesInput.empty();
			$selfInput.val('');

			var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');



			if ($purchaseTypeSelected.val() == 'reservation'){
				showAjaxLoader($selfInput, 'small');
				$.ajax({
					cache: false,
					dataType: 'json',
					type:'post',
					data: attrParams,
					url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadReservationData&id=' + $Row.attr('data-id')+'&qty='+selectedQty+'&purchase_type='+$purchaseTypeSelected.val()),
					success: function (data){
						$self.data('bookedDates', data.bookedDates);
                    startArray = Array();
                    bookedTimesArr = Array();
                    for(i3=0;i3<data.bookedTimesArr.length;i3++){
                        //alert(data.bookedTimesArr[i3]);
                        var startDateS = $.fullCalendar.parseDate(data.bookedTimesArr[i3]);
                        //var startDateS1 = "new Date("+startDateS.getFullYear()+","+ startDateS.getMonth()+","+ startDateS.getDate()+","+ startDateS.getHours()+","+ (startDateS.getMinutes())+","+ startDateS.getSeconds()+")";
                        //var endDateS = "new Date("+startDateS.getFullYear()+","+ startDateS.getMonth()+","+ startDateS.getDate()+","+ startDateS.getHours()+","+ (startDateS.getMinutes()+1)+","+ startDateS.getSeconds()+")";
                        var endDateS = new Date(startDateS.getFullYear(), startDateS.getMonth(), startDateS.getDate(), startDateS.getHours(), startDateS.getMinutes()+1, startDateS.getSeconds());
                        var theEvent = new Object();
                        theEvent.title = 'Not Available';
                        theEvent.start = startDateS;
                        theEvent.end = endDateS;
                        theEvent.allDay = false;
                        startArray.push(theEvent);
                        bookedTimesArr.push(startDateS);
                    }
                    $self.data('startArray', startArray);
                    $self.data('bookedTimesArr', bookedTimesArr);
					$self.data('shippingDaysPadding', data.shippingDaysPadding);
					$self.data('shippingDaysArray', data.shippingDaysArray);
					$self.data('disabledDatesPadding', data.disabledDatesPadding);
					$self.data('disabledDays', data.disabledDays);

					$self.data('disabledDates', data.disabledDates);
					$self.data('allowSelectionBefore', data.allowSelectionBefore);
					$self.data('allowSelectionAfter', data.allowSelectionAfter);
					$self.data('allowSelection', data.allowSelection);
					$self.data('allowSelectionMin', data.allowSelectionMin);
                    $self.data('allowSelectionMax', data.allowSelectionMax);
					$self.data('minRentalPeriod', data.minRentalPeriod);
                    $self.data('maxRentalPeriod', data.maxRentalPeriod);
                    $self.data('minTime', data.minTime);
                    $self.data('maxTime', data.maxTime);
                    $self.data('allowHourly', data.allowHourly);
					$self.data('selectedDate', '');
					$self.data('isStart', false);

                    $refreshCal.click(function(){

                        if ($self.data('selectedStartTimeTd') != null) {
                            $self.data('selectedStartTimeTd').data('element').remove();
                        }
                        if ($self.data('selectedEndTimeTd') != null) {
                            $self.data('selectedEndTimeTd').data('element').remove();
                        }

                        $self.data('selectedStartTime', null);
                        $self.data('selectedStartTimeTd', null);
                        $self.data('isTimeStart', false);
                        $self.data('selectedEndTime', null);
                        $self.data('selectedEndTimeTd', null);

                            if ($self.data('selected') == 'start'){
                                $datePicker.datepick('setDate', 0);
                            }else{
                                $datePicker.datepick('setDate', -1);
                            }
                            //$('#datePicker').datepick('setDate');
                            $self.data('selected', '');
                            $self.data('selectedDate', '');
                            $self.data('isStart', false);
                            $self.data('allowSelectionBefore', true);
                            $self.data('allowSelectionAfter', true);
                            $self.data('allowSelection', true);
                            $self.data('allowSelectionMin', true);
                            $self.data('allowSelectionMax', true);

                            $self.data('start_date','');
                            $self.data('end_date','');
                            $calendarTime.hide();
                            //$('#end_date').val('');
                   });


					$datePicker.datepick({
						clickInput: true,
						useThemeRoller: true,
						minDate: '+' + data.calStartDate,
						dateFormat: 'yy-mm-dd',
						gotoCurrent: true,
						rangeSelect: true,
						rangeSeparator: ',',
						changeMonth: false,
						changeYear: false,
						numberOfMonths: 3,
						prevText: '<span class="ui-icon ui-icon-circle-triangle-w"></span>',
						prevStatus: 'Go To Previous Month',
						nextText: '<span class="ui-icon ui-icon-circle-triangle-e"></span>',
						nextStatus: 'Go To Next Month',
						clearText: 'Reset',
						clearStatus: 'Reset selected dates',
						initStatus: 'Please select a start date',
						showStatus: true,
						beforeShowDay: function (dateObj){
							dateObj.setHours(0,0,0,0);
							var dateFormatted = $.datepick.formatDate('yy-m-d', dateObj);
							if ($.inArray(dayShortNames[dateObj.getDay()], $self.data('disabledDays')) > -1){
								return [false, 'ui-datepicker-disabled ui-datepicker-shipable', 'Disabled By Admin'];
							}else if ($.inArray(dateFormatted, $self.data('bookedDates')) > -1){
								return [false, 'ui-datepicker-reserved', 'Reserved'];
							}else if ($.inArray(dateFormatted, $self.data('disabledDatesPadding')) > -1){
								return [false, 'ui-datepicker-disabled', 'Disabled by Admin'];
							}else if ($.inArray(dateFormatted, $self.data('shippingDaysPadding')) > -1){
								var shippingDaysArray = $self.data('shippingDaysArray');
								return [true, 'hasd dayto-'+shippingDaysArray[$.inArray(dateFormatted, $self.data('shippingDaysPadding'))], 'Available'];
							}else{
								if ($self.data('disabledDates').length > 0){
									var Dates = $self.data('disabledDates');
									for (var i=0; i<Dates.length; i++){
										var dateFrom = new Date();
										dateFrom.setFullYear(
											Dates[i][0][0],
											Dates[i][0][1]-1,
											Dates[i][0][2]
											);
										dateFrom.setHours(0,0,0,0);

										var dateTo = new Date();
										dateTo.setFullYear(
											Dates[i][1][0],
											Dates[i][1][1]-1,
											Dates[i][1][2]
											);
										dateTo.setHours(0,0,0,0);

										if (dateObj >= dateFrom && dateObj <= dateTo){
											return [false, 'ui-datepicker-disabled', 'Disabled By Admin'];
										}
									}
								}
							}
							return [true, '', 'Available'];
						},
						onHover: function (value, date, inst, curTd){
							if (date == null){
								$('.ui-datepicker-shipping-day-hover').removeClass('ui-datepicker-shipping-day-hover');
								$(curTd).removeClass('ui-datepicker-start_date');
							}else{
								$(curTd).addClass('ui-datepicker-start_date');
								var $ShippingInput = $Row.find('.reservationShipping option:selected');
								var shippingDaysBefore = $ShippingInput.attr('days_before');
								var shippingDaysAfter = $ShippingInput.attr('days_after');
								var prevTD = $(curTd);
								var nextTD = $(curTd);

								$self.data('allowSelectionBefore', true);
								$self.data('allowSelectionAfter', true);

								if (!$self.data('isStart')){
									for(var i=0; i<shippingDaysBefore; i++){
										if (prevTD.prev().size() <= 0){
											if (prevTD.find('a').html() == '1' || prevTD.html() == '1'){
												prevTD = prevTD.closest('.ui-datepicker-group').prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
											}else{
												prevTD = prevTD.parent().prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
											}
										}else{
											prevTD = prevTD.prev();
										}

										if (prevTD.hasClass('ui-datepicker-other-month')){
											prevTD = prevTD.closest('.ui-datepicker-group').prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
										}

										$('a', prevTD).addClass('ui-datepicker-shipping-day-hover');
										if (prevTD.hasClass('ui-state-disabled') && !prevTD.hasClass('ui-datepicker-shipable')){
											$self.data('allowSelectionBefore', false);
										}

									}
								}else{
									for(var i=0; i<shippingDaysAfter; i++){
										if (nextTD.next().size() <= 0){
											nextTD = nextTD.parent().next().find('td').first();
										}else{
											nextTD = nextTD.next();
										}

										if (nextTD.hasClass('ui-datepicker-other-month')){
											nextTD = nextTD.closest('.ui-datepicker-group').next().find('td').filter(':not(.ui-datepicker-other-month)').first();
										}

										$('a', nextTD).addClass('ui-datepicker-shipping-day-hover');

										if (nextTD.hasClass('ui-state-disabled') && !nextTD.hasClass('ui-datepicker-shipable')){
											$self.data('allowSelectionAfter', false);
										}

									}
								}
							}
						},
						onDayClick: function (date, td){
							var $ShippingInput = $Row.find('.reservationShipping option:selected');
							var shippingDaysBefore = $ShippingInput.attr('days_before');
							var shippingDaysAfter = $ShippingInput.attr('days_after');
							var shippingLabel = $ShippingInput.parent().parent().find('td').first().html();
							var selectedShippingMethod = $ShippingInput.val();
							var $qtyInput = $Row.find('.productQty');
							var selectedQty = $qtyInput.val();
							;
							var myclass = '';
							var sDay = 0;
							var words;
							var sDaysArr;
							//dumpProps(td);
							//alert($(td).html());
                            $self.data('selectedShippingMethod', selectedShippingMethod);
                            $self.data('selectedQty', selectedQty);
							//console.debug(td);

							myclass = $('#'+td.id).attr("class");
							//alert(myclass);
							if (myclass){
								words = myclass.split(' ');
								sDay = 1000;
								for (var j = 0; j < words.length; j++) {
									if (words[j].indexOf('dayto') >= 0) {
										sDaysArr = words[j].split('-');
										sDay = parseInt(sDaysArr[1]);
										break;
									}
								}

								if (!$self.data('isStart')) {
									if (sDay - shippingDaysBefore <= 0) {
										$self.data('allowSelectionBefore', false);
									}
								} else {
									if (sDay != 1000){
										if (shippingDaysAfter - sDay <= 0) {
											$self.data('allowSelectionAfter', false);
										}
									}
								}
							}

							if (selected == 'start'){
								$self.data('allowSelection', true);
								var bookedDates = $self.data('bookedDates');
								var selectedDate = $self.data('selectedDate');
								for(var k=0;k<bookedDates.length;k++){
									bDateArr = bookedDates[k].split('-');
									bDate = new Date(parseInt(bDateArr[0]),parseInt(bDateArr[1])-1, parseInt(bDateArr[2]));
									if (selectedDate.getTime() <= bDate.getTime() && date.getTime() >= bDate.getTime()){
										$self.data('allowSelection', false);
									}
								}

								$self.data('allowSelectionMin', true);
								if ((date.getTime() - selectedDate.getTime()) < ($self.data('minRentalPeriod'))){
									$self.data('allowSelectionMin', false);
								}
								$self.data('allowSelectionMax', true);
								if ((date.getTime() - selectedDate.getTime()) > ($self.data('maxRentalPeriod')) &&($self.data('maxRentalPeriod') != -1)){
									$self.data('allowSelectionMax', false);
								}
							}


							if ($self.data('allowSelectionMin') == false){
								alert('Error: the date you selected is not available because you must have at least ' + $self.data('minRentalPeriod') + ' days reserved');
								return false;
							}
			 				if ($self.data('allowSelectionMax') == false){
								alert('Error: the date you selected is not available because you must have maximum '+$self.data('maxRentalPeriod') + ' days reserved');
								return false;
							}
							if ($self.data('allowSelection') == false){
								alert('Error: the date you selected is not available because there are reservation in between your selected dates');
								return false;
							}
							if ($self.data('allowSelectionBefore') == false){
								alert('Error: the date you selected is not available because with the selected ship method of ' + shippingLabel + ', you need to allow ' + shippingDaysBefore + ' ship day(s) before your reservation');
								return false;
							}
							if ($self.data('allowSelectionAfter') == false){
								alert('Error: the date you selected is not available because with the selected ship method of ' + shippingLabel + ', you need to allow ' + shippingDaysAfter + ' ship day(s) after your reservation');
								return false;
							}

							selected = (selected == '' || selected == 'end' ? 'start' : 'end');
                            $self.data('selected', selected);
							if (selected == 'start'){
								$self.data('selectedDate', date);
								//$self.datepick('option', 'initStatus', 'Please select an end date');
							}else if (selected == 'end'){
								var monthT = date.getMonth() + 1;
								var daysT = date.getDate();
								var daysTs = '';
								var monthTs = '';
								if (daysT < 10){
									daysTs = '0' + daysT;
								} else{
									daysTs = daysT + '';
								}
								if (monthT < 10){
									monthTs = '0' + monthT;
								}else{
									monthTs = monthT + '';
								}
								endDateString = date.getFullYear()+'-'+monthTs+'-'+ daysTs;

                                $self.data('end_date', endDateString);

                                $sDate = new Date($self.data('start_date'));
                                $eDate = new Date(endDateString);
                                if($sDate.getTime() != $eDate.getTime()){
                                    showAjaxLoader($selfInput, 'x-large');
                                    $.ajax({
                                        cache: false,
                                        dataType: 'json',
                                        type:'post',
                                        data: attrParams,
                                        url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id') + '&start_date=' + $self.data('start_date').toString() + '&end_date=' + $self.data('end_date').toString() + '&shipping=' + $self.data('selectedShippingMethod') + '&qty='+$self.data('selectedQty')+'&purchase_type='+$purchaseTypeSelected.val()),
                                        success: function (data){
                                            //update priceEx
                                            $Row.find('.priceEx').val(data.price).trigger('keyup');
                                            $self.hide();
                                            $selfInput.val($self.data('start_date').toString()+','+$self.data('end_date').toString());
                                            removeAjaxLoader($selfInput);
                                        }
                                    });
                                }
								//$self.datepick('option', 'initStatus', 'Both dates have been selected.<br />Clicking again will restart the selection process');
							}else{
								//$self.datepick('option', 'initStatus', 'Please select a start date');
							}
                            $calendarTime.show();
                            $calendarTime.fullCalendar( 'gotoDate', date);
                           $sDate = new Date($self.data('start_date'));
                           $eDate = new Date($self.data('end_date'));

                            if($sDate.getTime() != $eDate.getTime() || $self.data('selected') == 'end'){
                                if($self.data('selectedStartTimeTd') != null){
                                    $self.data('selectedStartTimeTd').data('element').remove();
                                }
                                if($self.data('selectedEndTimeTd') != null){
                                    $self.data('selectedEndTimeTd').data('element').remove();
                                }
                            }
                            if($sDate.getTime() != $eDate.getTime()){
                                $calendarTime.hide();
                            }
						},
						onSelect: function (value, date, inst){
							var dates = value.split(',');
							//$('#start_date').val(dates[0]);
							//$('#end_date').val(dates[1]);
							$self.data('start_date', dates[0]);
                            $self.data('end_date', dates[1]);
							$self.data('isStart', false);
							if (dates[0] != dates[1]){
								//$self.datepick('option', 'maxDate', null);
							}else{
								$self.data('isStart', true);
							}

							//$('#inCart').hide();
							//$('#checkAvail').show();
						}/*,
						statusForDate: function (date, inst){
							selectedCheck = (selected == '' || selected == 'end' ? 'start' : 'end');
							var text = '';
							if (selectedCheck == 'start'){
								text = 'Please select a start date';
							}else if (selectedCheck == 'end'){
								text = 'Please select an end date';
							}
							text = text + '<br />' + $.datepick.formatDate(
								$.datepick._get(inst, 'dateStatus'),
								date,
								$.datepick._getFormatConfig(inst)
								);
							return text;
						}*/
					});
                    if($self.data('allowHourly') == true){
                        //alert('ds');
                        //$('<div id="calendarTime"></div>').appendTo($('#ui-datepicker-div'));
                       //alert($self.data('startArray'));
                        var isTimeStart = false;
                        var selectedStartTimeTd = null;
                        var selectedEndTimeTd = null;
                        var selectedStartTime = null;
                        var selectedEndTime = null;
                        $self.data('selectedStartTime', selectedStartTime);
                        $self.data('selectedStartTimeTd', selectedStartTimeTd);
                        $self.data('isTimeStart', isTimeStart);
                        $self.data('selectedEndTime', selectedEndTime);
                        $self.data('selectedEndTimeTd', selectedEndTimeTd);
                        $calendarTime.fullCalendar({
                                header: {
                                     left:   '',
                                    center: '',
                                    right:  ''
                                },
                                theme: true,
                                allDaySlot:false,
                                slotMinutes:$self.data('slotMinutes'),
                                //axisFormat:'h:mm',
                                editable: false,
                                disableDragging: true,
                                disableResizing: true,
                                minTime:$self.data('minTime'),
                                maxTime:$self.data('maxTime'),
                                defaultView: 'agendaDay',
                                height: 296,
                                events: $self.data('startArray'),
                                dayClick: function(date, allDay, jsEvent, view) {
                                       if( $self.data('isTimeStart') == false){
                                            isTimeStart = true;
                                            if($self.data('selectedStartTimeTd') != null){
                                                $self.data('selectedStartTimeTd').data('element').remove();
                                            }
                                            if($self.data('selectedEndTimeTd') != null){
                                                $self.data('selectedEndTimeTd').data('element').remove();
                                            }
                                            selectedStartTimeTd = $(this);
                                            selectedEndTime = null;
                                            selectedEndTimeTd = null;

                                            selectedStartTime = new Date(date);

                                            $el = $('<span></span>').html('Selected Start Time');
                                            $el.css('background-color','red');
                                            $el.css('color','white');
                                            selectedStartTimeTd.find('div').first().remove();
                                            selectedStartTimeTd.append($el);
                                            selectedStartTimeTd.data('element',$el);

                                            if(selectedStartTime.getDate() < 10){
                                                today_day = '0'+selectedStartTime.getDate();
                                            }else{
                                                today_day = selectedStartTime.getDate();
                                            }

                                            if(selectedStartTime.getMonth() < 10){
                                                today_month = '0'+(selectedStartTime.getMonth()+1);
                                            }else{
                                                today_month = selectedStartTime.getMonth()+1;
                                            }

                                           $self.data('selectedStartTime', selectedStartTime);
                                           $self.data('selectedStartTimeTd', selectedStartTimeTd);
                                           $self.data('isTimeStart', isTimeStart);
                                           $self.data('selectedEndTime', selectedEndTime);
                                           $self.data('selectedEndTimeTd', selectedEndTimeTd);


                                            $self.data('start_date',(today_month +'/'+today_day+'/'+selectedStartTime.getFullYear() + ' '+selectedStartTime.getHours() + ':'+selectedStartTime.getMinutes()+':00'));
                                       }else{
                                           if($self.data('selectedStartTime') < new Date(date)){

                                                var allowSelectionTime = true;
                                                var bookedTimesArr = $self.data('bookedTimesArr');
                                                for(var k=0;k<bookedTimesArr.length;k++){
                                                    var ddTime = new Date(bookedTimesArr[k]);
                                                    //bDateArr = bookedDates[k].split('-');
                                                    //bDate = new Date(parseInt(bDateArr[0]),parseInt(bDateArr[1])-1, parseInt(bDateArr[2]));
                                                    if($self.data('selectedStartTime').getTime()<=ddTime.getTime() && date.getTime()>=ddTime.getTime()){
                                                        allowSelectionTime = false;
                                                    }
                                                }
                                                var allowSelectionMinTime = true;
                                                if ((date.getTime() - $self.data('selectedStartTime').getTime()) < (($self.data('minRentalPeriod')))){
                                                    allowSelectionMinTime = false;
                                                }
                                                var allowSelectionMaxTime = true;
                                                if (((date.getTime() - $self.data('selectedStartTime').getTime()) > ($self.data('maxRentalPeriod'))) && $self.data('maxRentalPeriod') != -1){
                                                    allowSelectionMaxTime = false;
                                                }


                                            //end check here
                                            if (allowSelectionMinTime == false){
                                                alert('' + $self.data('minRentalPeriod') + '');
                                                return false;
                                            }
                                            if (allowSelectionMaxTime == false){
                                                alert(' ' + $self.data('maxRentalPeriod') + ' ');
                                                return false;
                                            }
                                            if (allowSelectionTime == false){
                                                alert('Cannot select');
                                                return false;
                                            }

                                               isTimeStart = false;
                                               selectedEndTimeTd = $(this);
                                               selectedEndTime = new Date(date);
                                               $el = $('<span></span>').html('Selected End Time');
                                               $el.css('background-color','red');
                                               $el.css('color','white');
                                               selectedEndTimeTd.find('div').first().remove();
                                               selectedEndTimeTd.append($el);
                                               selectedEndTimeTd.data('element',$el);

                                               if(selectedEndTime.getDate() < 10){
                                                   today_day = '0'+selectedEndTime.getDate();
                                               }else{
                                                   today_day = selectedEndTime.getDate();
                                               }

                                               if(selectedEndTime.getMonth() < 10){
                                                   today_month = '0'+(selectedEndTime.getMonth()+1);
                                               }else{
                                                   today_month = selectedEndTime.getMonth()+1;
                                               }

                                           //$self.data('selectedStartTime', selectedStartTime);
                                           //$self.data('selectedStartTimeTd', selectedStartTimeTd);
                                           $self.data('isTimeStart', isTimeStart);
                                           $self.data('selectedEndTime', selectedEndTime);
                                           $self.data('selectedEndTimeTd', selectedEndTimeTd);
                                               //$('#end_date').val(today_month +'/'+today_day+'/'+selectedEndTime.getFullYear() + ' '+selectedEndTime.getHours() + ':'+selectedEndTime.getMinutes()+':00');
                                               endDateString = today_month +'/'+today_day+'/'+selectedEndTime.getFullYear() + ' '+selectedEndTime.getHours() + ':'+selectedEndTime.getMinutes()+':00';
                                               $self.data('end_date', endDateString)

                                               showAjaxLoader($selfInput, 'x-large');
                                                    $.ajax({
                                                        cache: false,
                                                        dataType: 'json',
                                                        type:'post',
                                                        data: attrParams,
                                                        url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id') + '&start_date=' + $self.data('start_date').toString() + '&end_date=' + $self.data('end_date').toString() + '&shipping=' + $self.data('selectedShippingMethod') + '&qty='+$self.data('selectedQty')+'&purchase_type='+$purchaseTypeSelected.val()),
                                                        success: function (data){
                                                            //update priceEx
                                                            $Row.find('.priceEx').val(data.price).trigger('keyup');
                                                            $self.hide();
                                                            $selfInput.val($self.data('start_date').toString()+','+$self.data('end_date').toString());
                                                            removeAjaxLoader($selfInput);
                                                        }
                                                    });
                                           }
                                           //reset selected td;
                                       }
                                    // change the day's background color just for fun
                                    //$(this).css('background-color', 'red');

                                }
                            });
                    }
                    $self.css('width','600px');
                    //$self.css('width','600px');
                    $self.css('position','absolute');
                    var posi = $selfInput.offset();
                    $self.css('top',(posi.top-200)+'px');
                    $self.css('left',(posi.left+100)+'px');
                    //$self.dialog({ minWidth: 600 });
                    $self.data('dialogCreated', true);
                    $calendarTime.hide();
					removeAjaxLoader($selfInput);
					$self.focus();
				}
			})
        }
		}else{

        $self.show();

        }
	});
    /*end qty*/

    /*attribute*/
    $('.productAttribute').live('change',function (){
		var $Row = $(this).parent().parent().parent().parent();
		var $self = $Row.find('.selectDialog');
        var $calendarTime = $Row.find('.calendarTime');
        var $datePicker = $Row.find('.datePicker');
        var $refreshCal = $Row.find('.refreshCal');
		if ($self.data('dialogCreated') === undefined){
            var $selfInput =  $Row.find('.reservationDates');
			var $qtyInput = $Row.find('.productQty');
			var selectedQty = $qtyInput.val();
			productsID = $Row.attr('data-id');
            var	attrParams = 'id[reservation]['+$(this).attr('attrval')+']='+$(this).val();
            $refreshCal.button();
		    $selfInput.val('');

			var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');

			if ($purchaseTypeSelected.val() == 'reservation'){
				showAjaxLoader($selfInput, 'small');
				$.ajax({
					cache: false,
					dataType: 'json',
					type:'post',
					data: attrParams,
					url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadReservationData&id=' + $Row.attr('data-id')+'&qty='+selectedQty+'&purchase_type='+$purchaseTypeSelected.val()),
					success: function (data){
						$self.data('bookedDates', data.bookedDates);
                    startArray = Array();
                    bookedTimesArr = Array();
                    for(i3=0;i3<data.bookedTimesArr.length;i3++){
                        //alert(data.bookedTimesArr[i3]);
                        var startDateS = $.fullCalendar.parseDate(data.bookedTimesArr[i3]);
                        //var startDateS1 = "new Date("+startDateS.getFullYear()+","+ startDateS.getMonth()+","+ startDateS.getDate()+","+ startDateS.getHours()+","+ (startDateS.getMinutes())+","+ startDateS.getSeconds()+")";
                        //var endDateS = "new Date("+startDateS.getFullYear()+","+ startDateS.getMonth()+","+ startDateS.getDate()+","+ startDateS.getHours()+","+ (startDateS.getMinutes()+1)+","+ startDateS.getSeconds()+")";
                        var endDateS = new Date(startDateS.getFullYear(), startDateS.getMonth(), startDateS.getDate(), startDateS.getHours(), startDateS.getMinutes()+1, startDateS.getSeconds());
                        var theEvent = new Object();
                        theEvent.title = 'Not Available';
                        theEvent.start = startDateS;
                        theEvent.end = endDateS;
                        theEvent.allDay = false;
                        startArray.push(theEvent);
                        bookedTimesArr.push(startDateS);
                    }
                    $self.data('startArray', startArray);
                    $self.data('bookedTimesArr', bookedTimesArr);
					$self.data('shippingDaysPadding', data.shippingDaysPadding);
					$self.data('shippingDaysArray', data.shippingDaysArray);
					$self.data('disabledDatesPadding', data.disabledDatesPadding);
					$self.data('disabledDays', data.disabledDays);

					$self.data('disabledDates', data.disabledDates);
					$self.data('allowSelectionBefore', data.allowSelectionBefore);
					$self.data('allowSelectionAfter', data.allowSelectionAfter);
					$self.data('allowSelection', data.allowSelection);
					$self.data('allowSelectionMin', data.allowSelectionMin);
                    $self.data('allowSelectionMax', data.allowSelectionMax);
					$self.data('minRentalPeriod', data.minRentalPeriod);
                    $self.data('maxRentalPeriod', data.maxRentalPeriod);
                    $self.data('minTime', data.minTime);
                    $self.data('maxTime', data.maxTime);
                    $self.data('allowHourly', data.allowHourly);
					$self.data('selectedDate', '');
					$self.data('isStart', false);

                    $refreshCal.click(function(){

                        if ($self.data('selectedStartTimeTd') != null) {
                            $self.data('selectedStartTimeTd').data('element').remove();
                        }
                        if ($self.data('selectedEndTimeTd') != null) {
                            $self.data('selectedEndTimeTd').data('element').remove();
                        }

                        $self.data('selectedStartTime', null);
                        $self.data('selectedStartTimeTd', null);
                        $self.data('isTimeStart', false);
                        $self.data('selectedEndTime', null);
                        $self.data('selectedEndTimeTd', null);

                            if ($self.data('selected') == 'start'){
                                $datePicker.datepick('setDate', 0);
                            }else{
                                $datePicker.datepick('setDate', -1);
                            }
                            //$('#datePicker').datepick('setDate');
                            $self.data('selected', '');
                            $self.data('selectedDate', '');
                            $self.data('isStart', false);
                            $self.data('allowSelectionBefore', true);
                            $self.data('allowSelectionAfter', true);
                            $self.data('allowSelection', true);
                            $self.data('allowSelectionMin', true);
                            $self.data('allowSelectionMax', true);

                            $self.data('start_date','');
                            $self.data('end_date','');
                            $calendarTime.hide();
                            //$('#end_date').val('');
                   });


					$datePicker.datepick({
						clickInput: true,
						useThemeRoller: true,
						minDate: '+' + data.calStartDate,
						dateFormat: 'yy-mm-dd',
						gotoCurrent: true,
						rangeSelect: true,
						rangeSeparator: ',',
						changeMonth: false,
						changeYear: false,
						numberOfMonths: 3,
						prevText: '<span class="ui-icon ui-icon-circle-triangle-w"></span>',
						prevStatus: 'Go To Previous Month',
						nextText: '<span class="ui-icon ui-icon-circle-triangle-e"></span>',
						nextStatus: 'Go To Next Month',
						clearText: 'Reset',
						clearStatus: 'Reset selected dates',
						initStatus: 'Please select a start date',
						showStatus: true,
						beforeShowDay: function (dateObj){
							dateObj.setHours(0,0,0,0);
							var dateFormatted = $.datepick.formatDate('yy-m-d', dateObj);
							if ($.inArray(dayShortNames[dateObj.getDay()], $self.data('disabledDays')) > -1){
								return [false, 'ui-datepicker-disabled ui-datepicker-shipable', 'Disabled By Admin'];
							}else if ($.inArray(dateFormatted, $self.data('bookedDates')) > -1){
								return [false, 'ui-datepicker-reserved', 'Reserved'];
							}else if ($.inArray(dateFormatted, $self.data('disabledDatesPadding')) > -1){
								return [false, 'ui-datepicker-disabled', 'Disabled by Admin'];
							}else if ($.inArray(dateFormatted, $self.data('shippingDaysPadding')) > -1){
								var shippingDaysArray = $self.data('shippingDaysArray');
								return [true, 'hasd dayto-'+shippingDaysArray[$.inArray(dateFormatted, $self.data('shippingDaysPadding'))], 'Available'];
							}else{
								if ($self.data('disabledDates').length > 0){
									var Dates = $self.data('disabledDates');
									for (var i=0; i<Dates.length; i++){
										var dateFrom = new Date();
										dateFrom.setFullYear(
											Dates[i][0][0],
											Dates[i][0][1]-1,
											Dates[i][0][2]
											);
										dateFrom.setHours(0,0,0,0);

										var dateTo = new Date();
										dateTo.setFullYear(
											Dates[i][1][0],
											Dates[i][1][1]-1,
											Dates[i][1][2]
											);
										dateTo.setHours(0,0,0,0);

										if (dateObj >= dateFrom && dateObj <= dateTo){
											return [false, 'ui-datepicker-disabled', 'Disabled By Admin'];
										}
									}
								}
							}
							return [true, '', 'Available'];
						},
						onHover: function (value, date, inst, curTd){
							if (date == null){
								$('.ui-datepicker-shipping-day-hover').removeClass('ui-datepicker-shipping-day-hover');
								$(curTd).removeClass('ui-datepicker-start_date');
							}else{
								$(curTd).addClass('ui-datepicker-start_date');
								var $ShippingInput = $Row.find('.reservationShipping option:selected');
								var shippingDaysBefore = $ShippingInput.attr('days_before');
								var shippingDaysAfter = $ShippingInput.attr('days_after');
								var prevTD = $(curTd);
								var nextTD = $(curTd);

								$self.data('allowSelectionBefore', true);
								$self.data('allowSelectionAfter', true);

								if (!$self.data('isStart')){
									for(var i=0; i<shippingDaysBefore; i++){
										if (prevTD.prev().size() <= 0){
											if (prevTD.find('a').html() == '1' || prevTD.html() == '1'){
												prevTD = prevTD.closest('.ui-datepicker-group').prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
											}else{
												prevTD = prevTD.parent().prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
											}
										}else{
											prevTD = prevTD.prev();
										}

										if (prevTD.hasClass('ui-datepicker-other-month')){
											prevTD = prevTD.closest('.ui-datepicker-group').prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
										}

										$('a', prevTD).addClass('ui-datepicker-shipping-day-hover');
										if (prevTD.hasClass('ui-state-disabled') && !prevTD.hasClass('ui-datepicker-shipable')){
											$self.data('allowSelectionBefore', false);
										}

									}
								}else{
									for(var i=0; i<shippingDaysAfter; i++){
										if (nextTD.next().size() <= 0){
											nextTD = nextTD.parent().next().find('td').first();
										}else{
											nextTD = nextTD.next();
										}

										if (nextTD.hasClass('ui-datepicker-other-month')){
											nextTD = nextTD.closest('.ui-datepicker-group').next().find('td').filter(':not(.ui-datepicker-other-month)').first();
										}

										$('a', nextTD).addClass('ui-datepicker-shipping-day-hover');

										if (nextTD.hasClass('ui-state-disabled') && !nextTD.hasClass('ui-datepicker-shipable')){
											$self.data('allowSelectionAfter', false);
										}

									}
								}
							}
						},
						onDayClick: function (date, td){
							var $ShippingInput = $Row.find('.reservationShipping option:selected');
							var shippingDaysBefore = $ShippingInput.attr('days_before');
							var shippingDaysAfter = $ShippingInput.attr('days_after');
							var shippingLabel = $ShippingInput.parent().parent().find('td').first().html();
							var selectedShippingMethod = $ShippingInput.val();
							var $qtyInput = $Row.find('.productQty');
							var selectedQty = $qtyInput.val();
							;
							var myclass = '';
							var sDay = 0;
							var words;
							var sDaysArr;
							//dumpProps(td);
							//alert($(td).html());
                            $self.data('selectedShippingMethod', selectedShippingMethod);
                            $self.data('selectedQty', selectedQty);
							//console.debug(td);

							myclass = $('#'+td.id).attr("class");
							//alert(myclass);
							if (myclass){
								words = myclass.split(' ');
								sDay = 1000;
								for (var j = 0; j < words.length; j++) {
									if (words[j].indexOf('dayto') >= 0) {
										sDaysArr = words[j].split('-');
										sDay = parseInt(sDaysArr[1]);
										break;
									}
								}

								if (!$self.data('isStart')) {
									if (sDay - shippingDaysBefore <= 0) {
										$self.data('allowSelectionBefore', false);
									}
								} else {
									if (sDay != 1000){
										if (shippingDaysAfter - sDay <= 0) {
											$self.data('allowSelectionAfter', false);
										}
									}
								}
							}

							if (selected == 'start'){
								$self.data('allowSelection', true);
								var bookedDates = $self.data('bookedDates');
								var selectedDate = $self.data('selectedDate');
								for(var k=0;k<bookedDates.length;k++){
									bDateArr = bookedDates[k].split('-');
									bDate = new Date(parseInt(bDateArr[0]),parseInt(bDateArr[1])-1, parseInt(bDateArr[2]));
									if (selectedDate.getTime() <= bDate.getTime() && date.getTime() >= bDate.getTime()){
										$self.data('allowSelection', false);
									}
								}

								$self.data('allowSelectionMin', true);
								if ((date.getTime() - selectedDate.getTime()) < ($self.data('minRentalPeriod'))){
									$self.data('allowSelectionMin', false);
								}
								$self.data('allowSelectionMax', true);
								if ((date.getTime() - selectedDate.getTime()) > ($self.data('maxRentalPeriod')) &&($self.data('maxRentalPeriod') != -1)){
									$self.data('allowSelectionMax', false);
								}
							}


							if ($self.data('allowSelectionMin') == false){
								alert('Error: the date you selected is not available because you must have at least ' + $self.data('minRentalPeriod') + ' days reserved');
								return false;
							}
			 				if ($self.data('allowSelectionMax') == false){
								alert('Error: the date you selected is not available because you must have maximum '+$self.data('maxRentalPeriod') + ' days reserved');
								return false;
							}
							if ($self.data('allowSelection') == false){
								alert('Error: the date you selected is not available because there are reservation in between your selected dates');
								return false;
							}
							if ($self.data('allowSelectionBefore') == false){
								alert('Error: the date you selected is not available because with the selected ship method of ' + shippingLabel + ', you need to allow ' + shippingDaysBefore + ' ship day(s) before your reservation');
								return false;
							}
							if ($self.data('allowSelectionAfter') == false){
								alert('Error: the date you selected is not available because with the selected ship method of ' + shippingLabel + ', you need to allow ' + shippingDaysAfter + ' ship day(s) after your reservation');
								return false;
							}

							selected = (selected == '' || selected == 'end' ? 'start' : 'end');
                            $self.data('selected', selected);
							if (selected == 'start'){
								$self.data('selectedDate', date);
								//$self.datepick('option', 'initStatus', 'Please select an end date');
							}else if (selected == 'end'){
								var monthT = date.getMonth() + 1;
								var daysT = date.getDate();
								var daysTs = '';
								var monthTs = '';
								if (daysT < 10){
									daysTs = '0' + daysT;
								} else{
									daysTs = daysT + '';
								}
								if (monthT < 10){
									monthTs = '0' + monthT;
								}else{
									monthTs = monthT + '';
								}
								endDateString = date.getFullYear()+'-'+monthTs+'-'+ daysTs;

                                $self.data('end_date', endDateString);

                                $sDate = new Date($self.data('start_date'));
                                $eDate = new Date(endDateString);
                                if($sDate.getTime() != $eDate.getTime()){
                                    showAjaxLoader($selfInput, 'x-large');
                                    $.ajax({
                                        cache: false,
                                        dataType: 'json',
                                        type:'post',
                                        data: attrParams,
                                        url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id') + '&start_date=' + $self.data('start_date').toString() + '&end_date=' + $self.data('end_date').toString() + '&shipping=' + $self.data('selectedShippingMethod') + '&qty='+$self.data('selectedQty')+'&purchase_type='+$purchaseTypeSelected.val()),
                                        success: function (data){
                                            //update priceEx
                                            $Row.find('.priceEx').val(data.price).trigger('keyup');
                                            $self.hide();
                                            $selfInput.val($self.data('start_date').toString()+','+$self.data('end_date').toString());
                                            removeAjaxLoader($selfInput);
                                        }
                                    });
                                }
								//$self.datepick('option', 'initStatus', 'Both dates have been selected.<br />Clicking again will restart the selection process');
							}else{
								//$self.datepick('option', 'initStatus', 'Please select a start date');
							}
                            $calendarTime.show();
                            $calendarTime.fullCalendar( 'gotoDate', date);
                           $sDate = new Date($self.data('start_date'));
                           $eDate = new Date($self.data('end_date'));

                            if($sDate.getTime() != $eDate.getTime() || $self.data('selected') == 'end'){
                                if($self.data('selectedStartTimeTd') != null){
                                    $self.data('selectedStartTimeTd').data('element').remove();
                                }
                                if($self.data('selectedEndTimeTd') != null){
                                    $self.data('selectedEndTimeTd').data('element').remove();
                                }
                            }
                            if($sDate.getTime() != $eDate.getTime()){
                                $calendarTime.hide();
                            }
						},
						onSelect: function (value, date, inst){
							var dates = value.split(',');
							//$('#start_date').val(dates[0]);
							//$('#end_date').val(dates[1]);
							$self.data('start_date', dates[0]);
                            $self.data('end_date', dates[1]);
							$self.data('isStart', false);
							if (dates[0] != dates[1]){
								//$self.datepick('option', 'maxDate', null);
							}else{
								$self.data('isStart', true);
							}

							//$('#inCart').hide();
							//$('#checkAvail').show();
						}/*,
						statusForDate: function (date, inst){
							selectedCheck = (selected == '' || selected == 'end' ? 'start' : 'end');
							var text = '';
							if (selectedCheck == 'start'){
								text = 'Please select a start date';
							}else if (selectedCheck == 'end'){
								text = 'Please select an end date';
							}
							text = text + '<br />' + $.datepick.formatDate(
								$.datepick._get(inst, 'dateStatus'),
								date,
								$.datepick._getFormatConfig(inst)
								);
							return text;
						}*/
					});
                    if($self.data('allowHourly') == true){
                        //alert('ds');
                        //$('<div id="calendarTime"></div>').appendTo($('#ui-datepicker-div'));
                       //alert($self.data('startArray'));
                        var isTimeStart = false;
                        var selectedStartTimeTd = null;
                        var selectedEndTimeTd = null;
                        var selectedStartTime = null;
                        var selectedEndTime = null;
                        $self.data('selectedStartTime', selectedStartTime);
                        $self.data('selectedStartTimeTd', selectedStartTimeTd);
                        $self.data('isTimeStart', isTimeStart);
                        $self.data('selectedEndTime', selectedEndTime);
                        $self.data('selectedEndTimeTd', selectedEndTimeTd);
                        $calendarTime.fullCalendar({
                                header: {
                                     left:   '',
                                    center: '',
                                    right:  ''
                                },
                                theme: true,
                                allDaySlot:false,
                                slotMinutes:$self.data('slotMinutes'),
                                //axisFormat:'h:mm',
                                editable: false,
                                disableDragging: true,
                                disableResizing: true,
                                minTime:$self.data('minTime'),
                                maxTime:$self.data('maxTime'),
                                defaultView: 'agendaDay',
                                height: 296,
                                events: $self.data('startArray'),
                                dayClick: function(date, allDay, jsEvent, view) {
                                       if( $self.data('isTimeStart') == false){
                                            isTimeStart = true;
                                            if($self.data('selectedStartTimeTd') != null){
                                                $self.data('selectedStartTimeTd').data('element').remove();
                                            }
                                            if($self.data('selectedEndTimeTd') != null){
                                                $self.data('selectedEndTimeTd').data('element').remove();
                                            }
                                            selectedStartTimeTd = $(this);
                                            selectedEndTime = null;
                                            selectedEndTimeTd = null;

                                            selectedStartTime = new Date(date);

                                            $el = $('<span></span>').html('Selected Start Time');
                                            $el.css('background-color','red');
                                            $el.css('color','white');
                                            selectedStartTimeTd.find('div').first().remove();
                                            selectedStartTimeTd.append($el);
                                            selectedStartTimeTd.data('element',$el);

                                            if(selectedStartTime.getDate() < 10){
                                                today_day = '0'+selectedStartTime.getDate();
                                            }else{
                                                today_day = selectedStartTime.getDate();
                                            }

                                            if(selectedStartTime.getMonth() < 10){
                                                today_month = '0'+(selectedStartTime.getMonth()+1);
                                            }else{
                                                today_month = selectedStartTime.getMonth()+1;
                                            }

                                           $self.data('selectedStartTime', selectedStartTime);
                                           $self.data('selectedStartTimeTd', selectedStartTimeTd);
                                           $self.data('isTimeStart', isTimeStart);
                                           $self.data('selectedEndTime', selectedEndTime);
                                           $self.data('selectedEndTimeTd', selectedEndTimeTd);


                                            $self.data('start_date',(today_month +'/'+today_day+'/'+selectedStartTime.getFullYear() + ' '+selectedStartTime.getHours() + ':'+selectedStartTime.getMinutes()+':00'));
                                       }else{
                                           if($self.data('selectedStartTime') < new Date(date)){

                                                var allowSelectionTime = true;
                                                var bookedTimesArr = $self.data('bookedTimesArr');
                                                for(var k=0;k<bookedTimesArr.length;k++){
                                                    var ddTime = new Date(bookedTimesArr[k]);
                                                    //bDateArr = bookedDates[k].split('-');
                                                    //bDate = new Date(parseInt(bDateArr[0]),parseInt(bDateArr[1])-1, parseInt(bDateArr[2]));
                                                    if($self.data('selectedStartTime').getTime()<=ddTime.getTime() && date.getTime()>=ddTime.getTime()){
                                                        allowSelectionTime = false;
                                                    }
                                                }
                                                var allowSelectionMinTime = true;
                                                if ((date.getTime() - $self.data('selectedStartTime').getTime()) < (($self.data('minRentalPeriod')))){
                                                    allowSelectionMinTime = false;
                                                }
                                                var allowSelectionMaxTime = true;
                                                if (((date.getTime() - $self.data('selectedStartTime').getTime()) > ($self.data('maxRentalPeriod'))) && $self.data('maxRentalPeriod') != -1){
                                                    allowSelectionMaxTime = false;
                                                }


                                            //end check here
                                            if (allowSelectionMinTime == false){
                                                alert('' + $self.data('minRentalPeriod') + '');
                                                return false;
                                            }
                                            if (allowSelectionMaxTime == false){
                                                alert(' ' + $self.data('maxRentalPeriod') + ' ');
                                                return false;
                                            }
                                            if (allowSelectionTime == false){
                                                alert('Cannot select');
                                                return false;
                                            }

                                               isTimeStart = false;
                                               selectedEndTimeTd = $(this);
                                               selectedEndTime = new Date(date);
                                               $el = $('<span></span>').html('Selected End Time');
                                               $el.css('background-color','red');
                                               $el.css('color','white');
                                               selectedEndTimeTd.find('div').first().remove();
                                               selectedEndTimeTd.append($el);
                                               selectedEndTimeTd.data('element',$el);

                                               if(selectedEndTime.getDate() < 10){
                                                   today_day = '0'+selectedEndTime.getDate();
                                               }else{
                                                   today_day = selectedEndTime.getDate();
                                               }

                                               if(selectedEndTime.getMonth() < 10){
                                                   today_month = '0'+(selectedEndTime.getMonth()+1);
                                               }else{
                                                   today_month = selectedEndTime.getMonth()+1;
                                               }

                                           //$self.data('selectedStartTime', selectedStartTime);
                                           //$self.data('selectedStartTimeTd', selectedStartTimeTd);
                                           $self.data('isTimeStart', isTimeStart);
                                           $self.data('selectedEndTime', selectedEndTime);
                                           $self.data('selectedEndTimeTd', selectedEndTimeTd);
                                               //$('#end_date').val(today_month +'/'+today_day+'/'+selectedEndTime.getFullYear() + ' '+selectedEndTime.getHours() + ':'+selectedEndTime.getMinutes()+':00');
                                               endDateString = today_month +'/'+today_day+'/'+selectedEndTime.getFullYear() + ' '+selectedEndTime.getHours() + ':'+selectedEndTime.getMinutes()+':00';
                                               $self.data('end_date', endDateString)

                                               showAjaxLoader($selfInput, 'x-large');
                                                    $.ajax({
                                                        cache: false,
                                                        dataType: 'json',
                                                        type:'post',
                                                        data: attrParams,
                                                        url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id') + '&start_date=' + $self.data('start_date').toString() + '&end_date=' + $self.data('end_date').toString() + '&shipping=' + $self.data('selectedShippingMethod') + '&qty='+$self.data('selectedQty')+'&purchase_type='+$purchaseTypeSelected.val()),
                                                        success: function (data){
                                                            //update priceEx
                                                            $Row.find('.priceEx').val(data.price).trigger('keyup');
                                                            $self.hide();
                                                            $selfInput.val($self.data('start_date').toString()+','+$self.data('end_date').toString());
                                                            removeAjaxLoader($selfInput);
                                                        }
                                                    });
                                           }
                                           //reset selected td;
                                       }
                                    // change the day's background color just for fun
                                    //$(this).css('background-color', 'red');

                                }
                            });
                    }
                    $self.css('width','600px');
                    //$self.css('width','600px');
                    $self.css('position','absolute');
                    var posi = $selfInput.offset();
                    $self.css('top',(posi.top-200)+'px');
                    $self.css('left',(posi.left+100)+'px');
                    //$self.dialog({ minWidth: 600 });
                    $self.data('dialogCreated', true);
                    $calendarTime.hide();
					removeAjaxLoader($selfInput);
					$self.focus();
				}
			})
        }
		}else{

        $self.show();

        }
	});

$('.productAttribute').live('change',function (){
			var $Row = $(this).parent().parent().parent().parent();
		    var $datesInput = $Row.find('.reservationDates');

			$datesInput.empty();
			$Row.find('.reservationDates').val('');
			var $self = $datesInput;
			var $purchaseTypeSelected = $Row.find('.purchaseType option:selected');
			var eventS = $Row.find('.eventf option:selected');
			productsID = $Row.attr('data-id');
            var $qtyInput = $Row.find('.productQty');
			var	attrParams = 'id[reservation]['+$(this).attr('attrval')+']='+$(this).val();

			if ($purchaseTypeSelected.val() == 'reservation'){
				showAjaxLoader($self, 'small');
				$.ajax({
					cache: false,
					dataType: 'json',
					type:'post',
					data: attrParams,
					url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadReservationData&id=' + $Row.attr('data-id')+'&qty='+$qtyInput.val()+'&purchase_type='+$purchaseTypeSelected.val()),
					success: function (data){
						$self.data('bookedDates', data.bookedDates);
						$self.data('shippingDaysPadding', data.shippingDaysPadding);
						$self.data('shippingDaysArray', data.shippingDaysArray);
						$self.data('disabledDatesPadding', data.disabledDatesPadding);
						$self.data('disabledDays', data.disabledDays);
						$self.data('disabledDates', data.disabledDates);
						$self.data('minRentalDays', data.minRentalDays);
						$self.data('maxRentalDays', data.maxRentalDays);
						$self.data('allowSelectionBefore', data.allowSelectionBefore);
						$self.data('allowSelectionAfter', data.allowSelectionAfter);
						$self.data('allowSelection', data.allowSelection);
						$self.data('allowSelectionMin', data.allowSelectionMin);
						$self.data('minRentalDays', data.minRentalDays);
						$self.data('selectedDate', '');
						$self.data('isStart', false);

						$self.datepick({
							clickInput: true,
							useThemeRoller: true,
							minDate: '+' + data.calStartDate,
							dateFormat: 'yy-mm-dd',
							gotoCurrent: true,
							rangeSelect: true,
							rangeSeparator: ',',
							changeMonth: false,
							changeYear: false,
							numberOfMonths: 3,
							prevText: '<span class="ui-icon ui-icon-circle-triangle-w"></span>',
							prevStatus: 'Go To Previous Month',
							nextText: '<span class="ui-icon ui-icon-circle-triangle-e"></span>',
							nextStatus: 'Go To Next Month',
							clearText: 'Reset',
							clearStatus: 'Reset selected dates',
							initStatus: 'Please select a start date',
							showStatus: true,
							beforeShowDay: function (dateObj){
								dateObj.setHours(0,0,0,0);
								var dateFormatted = $.datepick.formatDate('yy-m-d', dateObj);
								if ($.inArray(dayShortNames[dateObj.getDay()], $self.data('bookedDates')) > -1){
									return [false, 'ui-datepicker-disabled ui-datepicker-shipable', 'Disabled By Admin'];
								}else if ($.inArray(dateFormatted, $self.data('bookedDates')) > -1){
									return [false, 'ui-datepicker-reserved', 'Reserved'];
								}else if ($.inArray(dateFormatted, $self.data('disabledDatesPadding')) > -1){
									return [false, 'ui-datepicker-disabled', 'Disabled by Admin'];
								}else if ($.inArray(dateFormatted, $self.data('shippingDaysPadding')) > -1){
									var shippingDaysArray = $self.data('shippingDaysArray');
									return [true, 'hasd dayto-'+shippingDaysArray[$.inArray(dateFormatted, $self.data('shippingDaysPadding'))], 'Available'];
								}else{
									if ($self.data('disabledDates').length > 0){
										var Dates = $self.data('disabledDates');
										for (var i=0; i<Dates.length; i++){
											var dateFrom = new Date();
											dateFrom.setFullYear(
												Dates[i][0][0],
												Dates[i][0][1]-1,
												Dates[i][0][2]
												);
											dateFrom.setHours(0,0,0,0);

											var dateTo = new Date();
											dateTo.setFullYear(
												Dates[i][1][0],
												Dates[i][1][1]-1,
												Dates[i][1][2]
												);
											dateTo.setHours(0,0,0,0);

											if (dateObj >= dateFrom && dateObj <= dateTo){
												return [false, 'ui-datepicker-disabled', 'Disabled By Admin'];
											}
										}
									}
								}
								return [true, '', 'Available'];
							},
							onHover: function (value, date, inst, curTd){
								if (date == null){
									$('.ui-datepicker-shipping-day-hover').removeClass('ui-datepicker-shipping-day-hover');
									$(curTd).removeClass('ui-datepicker-start_date');
								}else{
									$(curTd).addClass('ui-datepicker-start_date');
									var $ShippingInput = $Row.find('.reservationShipping option:selected');
									var shippingDaysBefore = $ShippingInput.attr('days_before');
									var shippingDaysAfter = $ShippingInput.attr('days_after');
									var prevTD = $(curTd);
									var nextTD = $(curTd);

									$self.data('allowSelectionBefore', true);
									$self.data('allowSelectionAfter', true);

									if (!$self.data('isStart')){
										for(var i=0; i<shippingDaysBefore; i++){
											if (prevTD.prev().size() <= 0){
												if (prevTD.find('a').html() == '1' || prevTD.html() == '1'){
													prevTD = prevTD.closest('.ui-datepicker-group').prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
												}else{
													prevTD = prevTD.parent().prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
												}
											}else{
												prevTD = prevTD.prev();
											}

											if (prevTD.hasClass('ui-datepicker-other-month')){
												prevTD = prevTD.closest('.ui-datepicker-group').prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
											}

											$('a', prevTD).addClass('ui-datepicker-shipping-day-hover');
											if (prevTD.hasClass('ui-state-disabled') && !prevTD.hasClass('ui-datepicker-shipable')){
												$self.data('allowSelectionBefore', false);
											}

										}
									}else{
										for(var i=0; i<shippingDaysAfter; i++){
											if (nextTD.next().size() <= 0){
												nextTD = nextTD.parent().next().find('td').first();
											}else{
												nextTD = nextTD.next();
											}

											if (nextTD.hasClass('ui-datepicker-other-month')){
												nextTD = nextTD.closest('.ui-datepicker-group').next().find('td').filter(':not(.ui-datepicker-other-month)').first();
											}

											$('a', nextTD).addClass('ui-datepicker-shipping-day-hover');

											if (nextTD.hasClass('ui-state-disabled') && !nextTD.hasClass('ui-datepicker-shipable')){
												$self.data('allowSelectionAfter', false);
											}

										}
									}
								}
							},
							onDayClick: function (date, td){
								var $ShippingInput = $Row.find('.reservationShipping option:selected');
								var shippingDaysBefore = $ShippingInput.attr('days_before');
								var shippingDaysAfter = $ShippingInput.attr('days_after');
								var shippingLabel = $ShippingInput.parent().parent().find('td').first().html();
								var selectedShippingMethod = $ShippingInput.val();
								var $qtyInput = $Row.find('.productQty');
								var selectedQty = $qtyInput.val();
								;
								var myclass = '';
								var sDay = 0;
								var words;
								var sDaysArr;
								//dumpProps(td);
								//alert($(td).html());

								//console.debug(td);

								myclass = $('#'+td.id).attr("class");
								//alert(myclass);
								if (myclass){
									words = myclass.split(' ');
									sDay = 1000;
									for (var j = 0; j < words.length; j++) {
										if (words[j].indexOf('dayto') >= 0) {
											sDaysArr = words[j].split('-');
											sDay = parseInt(sDaysArr[1]);
											break;
										}
									}

									if (!$self.data('isStart')) {
										if (sDay - shippingDaysBefore <= 0) {
											$self.data('allowSelectionBefore', false);
										}
									} else {
										if (sDay != 1000){
											if (shippingDaysAfter - sDay <= 0) {
												$self.data('allowSelectionAfter', false);
											}
										}
									}
								}

								if (selected == 'start'){
									$self.data('allowSelection', true);
									var bookedDates = $self.data('bookedDates');
									var selectedDate = $self.data('selectedDate');
									for(var k=0;k<bookedDates.length;k++){
										bDateArr = bookedDates[k].split('-');
										bDate = new Date(parseInt(bDateArr[0]),parseInt(bDateArr[1])-1, parseInt(bDateArr[2]));
										if (selectedDate.getTime() <= bDate.getTime() && date.getTime() >= bDate.getTime()){
											$self.data('allowSelection', false);
										}
									}

									$self.data('allowSelectionMin', true);
									if ((date.getTime() - selectedDate.getTime()) < ($self.data('minRentalDays')*24*60*60*1000)){
										$self.data('allowSelectionMin', false);
									}
									$self.data('allowSelectionMax', true);
									if ((date.getTime() - selectedDate.getTime()) < ($self.data('maxRentalDays')*24*60*60*1000)){
										$self.data('allowSelectionMax', false);
									}
								}


								if ($self.data('allowSelectionMin') == false){
									alert('Error: the date you selected is not available because you must have at least ' + $self.data('minRentalDays') + ' days reserved');
									return false;
								}
								if ($self.data('allowSelectionMax') == false){
									alert('Error: the date you selected is not available because you must have maximum '+$self.data('maxRentalDays') + ' days reserved');
									return false;
								}
								if ($self.data('allowSelection') == false){
									alert('Error: the date you selected is not available because there are reservation in between your selected dates');
									return false;
								}
								if ($self.data('allowSelectionBefore') == false){
									alert('Error: the date you selected is not available because with the selected ship method of ' + shippingLabel + ', you need to allow ' + shippingDaysBefore + ' ship day(s) before your reservation');
									return false;
								}
								if ($self.data('allowSelectionAfter') == false){
									alert('Error: the date you selected is not available because with the selected ship method of ' + shippingLabel + ', you need to allow ' + shippingDaysAfter + ' ship day(s) after your reservation');
									return false;
								}

								selected = (selected == '' || selected == 'end' ? 'start' : 'end');

								if (selected == 'start'){
									$self.data('selectedDate', date);
									//$self.datepick('option', 'initStatus', 'Please select an end date');
								}else if (selected == 'end'){
									var monthT = date.getMonth() + 1;
									var daysT = date.getDate();
									var daysTs = '';
									var monthTs = '';
									if (daysT < 10){
										daysTs = '0' + daysT;
									} else{
										daysTs = daysT + '';
									}
									if (monthT < 10){
										monthTs = '0' + monthT;
									}else{
										monthTs = monthT + '';
									}
									endDateString = date.getFullYear()+'-'+monthTs+'-'+ daysTs;

									showAjaxLoader($self, 'x-large');
									$.ajax({
										cache: false,
										dataType: 'json',
                                        type:'post',
					                    data: attrParams,
										url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfo&id=' + $Row.attr('data-id') + '&start_date=' + $self.data('start_date').toString() + '&end_date=' + endDateString + '&shipping=' + selectedShippingMethod + '&qty='+selectedQty),
										success: function (data){
											//update priceEx
											$Row.find('.priceEx').val(data.price).trigger('keyup');
											removeAjaxLoader($self);
										}
									});
								}
							},
							onSelect: function (value, date, inst){
								var dates = value.split(',');
								//$('#start_date').val(dates[0]);
								//$('#end_date').val(dates[1]);
								$self.data('start_date', dates[0]);
                                $self.data('end_date', dates[1]);
								$self.data('isStart', false);
								if (dates[0] != dates[1]){
									//$self.datepick('option', 'maxDate', null);
								}else{
									$self.data('isStart', true);
								}

							}
						});

						removeAjaxLoader($self);
						$self.focus();
					}
				})
			}
	});
    /*end attribute*/
});