$(document).ready(function (){
	var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
	var allowSelection = true;
	var productsID = 0;
	var selected = '';
	var autoChanged = false;

	$('.reservationDates').live('click', function (){
		if (!$(this).hasClass('hasDatepick')){
			var $self = $(this);
			var $Row = $(this).parent().parent().parent().parent();

			productsID = $Row.attr('data-id');
			showAjaxLoader($self, 'small');
			$.ajax({
				cache: false,
				dataType: 'json',
				url: js_app_link('app=orders&appPage=new&action=loadReservationData&id=' + $Row.attr('data-id')),
				success: function (data){
					$self.data('bookedDates', data.bookedDates);
					$self.data('shippingDaysPadding', data.shippingDaysPadding);
					$self.data('shippingDaysArray', data.shippingDaysArray);
					$self.data('disabledDatesPadding', data.disabledDatesPadding);
					$self.data('disabledDays', data.disabledDays);
					$self.data('disabledDates', data.disabledDates);
					$self.data('minRentalDays', data.minRentalDays);
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
							}else if ($.inArray(dateFormatted, $self.data('shippingDaysPadding')) > -1){
								var shippingPadding = $self.data('shippingDaysPadding');
								return [true, 'hasd dayto-' + shippingPadding[$.inArray(dateFormatted, Data)], 'Available'];
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
							;
							var myclass = '';
							var sDay = 0;
							var words;
							var sDaysArr;
							
							myclass = $(td).attr('class');
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
							}
							
							
							if ($self.data('allowSelectionMin') == false){
								alert('Error: the date you selected is not available because you must have at least ' + $self.data('minRentalDays') + ' days reserved');
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
								//$self.datepick('option', 'initStatus', 'Both dates have been selected.<br />Clicking again will restart the selection process');
							}else{
								//$self.datepick('option', 'initStatus', 'Please select a start date');
							}
						},
						onSelect: function (value, date, inst){
							var dates = value.split(',');            
							//$('#start_date').val(dates[0]);
							//$('#end_date').val(dates[1]);
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
					
					removeAjaxLoader($self);
					$self.focus();
				}
			})
		}
	});
});