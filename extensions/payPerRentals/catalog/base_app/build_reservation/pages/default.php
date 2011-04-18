<?php
	$pID_string = $_GET['products_id'];
	$product = new product((int)$_GET['products_id']);
	$purchaseTypeClass = $product->getPurchaseType('reservation');
 	$pprTable = Doctrine_Core::getTable('ProductsPayPerRental')->findOneByProductsId($pID_string);
    $total_weight = $product->getWeight();
	OrderShippingModules::calculateWeight();

 	/*periods*/
  	$QPeriods = Doctrine_Query::create()
	->from('ProductsPayPerPeriods')
	->where('products_id=?', $_GET['products_id'])
	->andWhere('price > 0')
	->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	$semDates = array();

	$sDate = array();
    if(count($QPeriods)){
		$QPeriodsNames = Doctrine_Query::create()
		->from('PayPerRentalPeriods')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
	foreach($QPeriods as $iPeriod){
				$periodName = '';
				foreach($QPeriodsNames as $periodNames){
					if($periodNames['period_id'] == $iPeriod['period_id']){
						$periodName = $periodNames;
						break;
					}
				}
				if($periodName != ''){
					$sDate['start_date'] = $periodName['period_start_date'];
					$sDate['end_date'] = $periodName['period_end_date'];
					$sDate['period_id'] = $iPeriod['period_id'];
					$sDate['period_name'] = $periodName['period_name'];
					$sDate['price'] = $iPeriod['price'];
					$semDates[] = $sDate;
				}
		}
    }
    /*end periods*/

	$allowHourly = (sysConfig::get('EXTENSION_PAY_PER_RENTALS_ALLOW_HOURLY') == 'True')?true:false;
	$minTime = 15;//slotMinutes

    if(sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'False'){
       	$minRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->min_period, $pprTable->min_type) * 60*1000;

    }else{
       	$minRentalPeriod = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS') *24*60*60*1000;
    }

    $minRentalPeriod = 0;
	$insurancePrice = $pprTable->insurance;
	$maxRentalPeriod = -1;

	if($pprTable->max_period > 0){
		$maxRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->max_period, $pprTable->max_type) * 60* 1000;
	}

	$startTime = mktime(0,0,0,date('m'), date('d'), date('Y'));
	//$endTime = mktime(0,0,0,date('m'), 1, date('Y')+3);
    $reservArr = array();
	$barcodesBooked = array();
	$bookings = $purchaseTypeClass->getBookedDaysArray(date('Y-m-d', $startTime), 1, &$reservArr, &$barcodesBooked);
	$timeBookings = $purchaseTypeClass->getBookedTimeDaysArray(date('Y-m-d', $startTime), 1, $minTime, $reservArr, $barcodesBooked);
    //here I have to add an array for Times Booked

	$_POST['rental_qty'] = 1;
    $maxShippingDays = -1;
	if ($purchaseTypeClass->shippingIsNone() === false && $purchaseTypeClass->shippingIsStore() === false){
	        $shippingTable = $purchaseTypeClass->buildShippingTable(true, false);
	     	$maxShippingDays =  $purchaseTypeClass->getMaxShippingDays(date('Y-m-d', $startTime));
    }
/**
 * Days Bookings
 */
	$booked = array();
    $shippingDaysPadding = array();
    $shippingDaysArray = array();
	$paddingDays = array();
	foreach($bookings as  $iBook){
		$booked[] = '"' . $iBook . '"';

		//period
		$op = 0;
		foreach ($semDates as $sDate) {
			if (strtotime($iBook) >= strtotime($sDate['start_date']) && strtotime($iBook) <= strtotime($sDate['end_date'])) {
				unset($semDates[$op]);
			}
			$op++;
		}
		$semDates = array_values($semDates);
		//end period
		$startTime = strtotime($iBook);
		for ($i = 0; $i <= $maxShippingDays; $i++) {
			$dateFormattedS = date('Y-n-j', strtotime('-' . $i . ' days', $startTime));
			$valDate = '"' . $dateFormattedS . '"';
			$valPos = array_search($valDate, $shippingDaysPadding);
			if ($valPos === false) {
				$shippingDaysPadding[] = $valDate;
				$shippingDaysArray[] = '"' . ($i) . '"';
				//period
				$op = 0;
				foreach ($semDates as $sDate) {
					if (strtotime($dateFormattedS) >= strtotime($sDate['start_date']) && strtotime($dateFormattedS) <= strtotime($sDate['end_date'])) {
						unset($semDates[$op]);
					}
					$op++;
				}
				$semDates = array_values($semDates);
				//end period
			} else {
				if ((int) substr($shippingDaysArray[$valPos], 1, strlen($shippingDaysArray[$valPos]) - 2) > $i) {
					$shippingDaysArray[$valPos] = '"' . $i . '"';
				}
			}
		}
		for ($i = 0; $i <= $maxShippingDays; $i++) {
			$dateFormattedS = date('Y-n-j', strtotime('+' . $i . ' days', $startTime));
			$valDate = '"' . $dateFormattedS . '"';
			$valPos = array_search($valDate, $shippingDaysPadding);
			if ($valPos === false) {
				$shippingDaysPadding[] = $valDate;
				$shippingDaysArray[] = '"' . ($i) . '"';
				//period
				$op = 0;
				foreach ($semDates as $sDate) {
					if (strtotime($dateFormattedS) >= strtotime($sDate['start_date']) && strtotime($dateFormattedS) <= strtotime($sDate['end_date'])) {
						unset($semDates[$op]);
					}
					$op++;
				}
				$semDates = array_values($semDates);
				//end period
			} else {
				if ((int) substr($shippingDaysArray[$valPos], 1, strlen($shippingDaysArray[$valPos]) - 2) > $i) {
					$shippingDaysArray[$valPos] = '"' . $i . '"';
				}
			}
		}
	}

	$disabledDays = sysConfig::explode('EXTENSION_PAY_PER_RENTALS_DISABLED_DAYS', ',');
	$startTimePadding = strtotime(date('Y-m-d'));
	$endTimePadding = strtotime('+' . (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING').' days', $startTimePadding);
        while($startTimePadding <= $endTimePadding){
			$dateFormatted = date('Y-n-j', $startTimePadding);
			$paddingDays[] = '"' . $dateFormatted . '"';
			//period
			$op = 0;
			foreach($semDates as $sDate){
				if(strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])){
					unset($semDates[$op]);
				}
				$op++;
			}
			$semDates = array_values($semDates);
			//end period
			$startTimePadding += 60*60*24;
	}

    $QBlockedDates = Doctrine_Query::create()
    ->from('PayPerRentalBlockedDates')
    ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

	foreach($QBlockedDates as $bInfo){
		$startTimePaddingArr = array();
		$endTimePaddingArr = array();

		if($bInfo['recurring'] == 0){
			$startTimePaddingArr[] = strtotime($bInfo['block_start_date']);
			$endTimePaddingArr[] = strtotime($bInfo['block_end_date']);
			$i = 1;
		}else{
			$i = 0;
			while(true){
				$bstartDate = strtotime('+'.$i.' years', strtotime($bInfo['block_start_date']));
				$bendDate = strtotime('+'.$i.' years', strtotime($bInfo['block_end_date']));
				$startTimePaddingArr[] = $bstartDate;
				$endTimePaddingArr[] = $bendDate;
				$i++;
				if(date('Y',$bendDate) - 3 > date('Y')){
					break;
				}
			}

		}
		$j = 0;
		while($j<$i){
			$startTimePadding = $startTimePaddingArr[$j];
			$endTimePadding = $endTimePaddingArr[$j];
			while($startTimePadding <= $endTimePadding){
					$dateFormatted = date('Y-n-j', $startTimePadding);
					$paddingDays[] = '"' . $dateFormatted . '"';
					//period
					$op = 0;
					foreach($semDates as $sDate){
						if(strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])){
							unset($semDates[$op]);
						}
						$op++;
					}
					$semDates = array_values($semDates);
					//end period
					$startTimePadding += 60*60*24;
			}
			$j++;
		}
    }

	$op = 0;
    $dateFormatted = date('Y-n-j');
	foreach($semDates as $sDate){
		if(strtotime($dateFormatted) >= strtotime($sDate['start_date'])){
			unset($semDates[$op]);
		}
		$op++;
	}
 	$semDates = array_values($semDates);
   /**
	* Time Bookings
   */
    $timeBooked = array();
	$timeBookedDate =  array();
	foreach($timeBookings as  $iBook){
		$timeDateParse = date_parse($iBook);
		$stringStart = 'new Date('.$timeDateParse['year'].','.($timeDateParse['month']-1).','.$timeDateParse['day'].','.$timeDateParse['hour'].','.$timeDateParse['minute'].')';
		$stringEnd = 'new Date('.$timeDateParse['year'].','.($timeDateParse['month']-1).','.$timeDateParse['day'].','.$timeDateParse['hour'].','.($timeDateParse['minute']+1).')';
		$timeBooked[] = "{title:'Not Available',start:".$stringStart.",end:".$stringEnd.", allDay:false}";
		$timeBookedDate[] = $stringStart;
	}
 ob_start();
?>
<script>
var bookedDates = [<?php echo implode(',', $booked);?>];
var shippingDaysPadding = [<?php echo implode(',', $shippingDaysPadding);?>];
var shippingDaysArray = [<?php echo implode(',', $shippingDaysArray);?>];
var disabledDatesPadding = [<?php echo implode(',', $paddingDays);?>];
var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
var disabledDays = ["<?php echo implode('","', $disabledDays);?>"];
var disabledDates = [];
var minRentalPeriod = <?php echo $minRentalPeriod;?>;
var maxRentalPeriod = <?php echo $maxRentalPeriod;?>;

var minRentalPeriodMessage = '<?php echo sysLanguage::get('PPR_ERR_AT_LEAST').' '. $pprTable->min_period. ' '.ReservationUtilities::getPeriodType($pprTable->min_type).' '.sysLanguage::get('PPR_ERR_DAYS_RESERVED'); ?>';
var maxRentalPeriodMessage = '<?php echo sysLanguage::get('PPR_ERR_MAXIMUM').' '. $pprTable->max_period. ' '.ReservationUtilities::getPeriodType($pprTable->max_type).' '.sysLanguage::get('PPR_ERR_DAYS_RESERVED'); ?>';
var allowSelectionBefore = true;
var allowSelectionAfter = true;
var allowSelection = true;
var allowSelectionMin = true;
var allowSelectionMax = true;
var productsID = <?php echo $_GET['products_id'];?>;

var startArray =[<?php echo implode(',', $timeBooked);?>];
var bookedTimesArr = [<?php echo implode(',', $timeBookedDate);?>];

var selected = '';
var selectedDate;
var isStart = false;
//var autoChanged = false;
var isHour = false;


$(document).ready(function (){
	$('#inCart').hide();
	$('#checkAvail').hide();
        $('#refreshCal').click(function(){
			if (selectedStartTimeTd != null) {
				selectedStartTimeTd.data('element').remove();
			}
			if (selectedEndTimeTd != null) {
				selectedEndTimeTd.data('element').remove();
			}
			selectedStartTime = null;
			selectedStartTimeTd = null;
			isTimeStart = false;
			selectedEndTime = null;
			selectedEndTimeTd = null;
			<?php
	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_FORCE_START_DATE') == 'True'){
	?>
	$('#datePicker').datepick('setDate', -1);
	var todayDate = new Date();
	selected = 'start';
	selectedDate = todayDate;
	isStart = true;
	var today_day = '';
	var today_month = '';

	if(todayDate.getDate() < 10){
		today_day = '0'+todayDate.getDate();
	}else{
		today_day = todayDate.getDate();
	}

	if(todayDate.getMonth() < 10){
		today_month = '0'+todayDate.getMonth();
	}else{
		today_month = todayDate.getMonth();
	}

	$('#start_date').val(today_month +'/'+today_day+'/'+todayDate.getFullYear());
	$('#end_date').val('');
	 //$('#datePicker').datepick('setDate', new Date(), new Date());
	 //$('#datePicker')._selectDay(d);
	<?php
	}else{
	?>
                if (selected == 'start'){
                    $('#datePicker').datepick('setDate', 0);
                }else{
                    $('#datePicker').datepick('setDate', -1);
                }
                //$('#datePicker').datepick('setDate');
                selected = '';
                selectedDate = '';
                isStart = false;
                allowSelectionBefore = true;
                allowSelectionAfter = true;
                allowSelection = true;
				allowSelectionMin = true;
				allowSelectionMax = true;
                $('#start_date').val('');
			    $('#end_date').val('');
				$('#calendarTime').hide();
	<?php
	}
	?>

       });

	if ($('input[name=rental_shipping]').size() > 0 && $('input[name=rental_shipping]:checked').size() == 0){
			$('input[name=rental_shipping]').each(function (){
					$(this).trigger('click');
			});
	}

	$('#datePicker').datepick({
		useThemeRoller: true,
		minDate: '+1',
		dateFormat: '<?php echo getJsDateFormat();?>',
		rangeSelect: <?php echo ((sysConfig::get('EXTENSION_PAY_PER_RENTALS_FORCE_START_DATE') == 'True')?'false':'true');?>,
		rangeSeparator: ',',
		changeMonth: false,
		changeYear: false,
		numberOfMonths: <?php echo sysConfig::get('EXTENSION_PAY_PER_RENTALS_NUMBER_OF_MONTHS_CALENDARS');?>,
		prevText: '<span class="ui-icon ui-icon-circle-triangle-w"></span>',
		prevStatus: '<?php echo sysLanguage::get('PPR_PREV_MONTH'); ?>',
		nextText: '<span class="ui-icon ui-icon-circle-triangle-e"></span>',
		nextStatus: '<?php echo sysLanguage::get('PPR_NEXT_MONTH'); ?>',
		clearText: '<?php echo sysLanguage::get('PPR_RESET'); ?>',
		clearStatus: '<?php echo sysLanguage::get('PPR_RESET_SELECTED'); ?>',
		initStatus: '<?php echo sysLanguage::get('PPR_SELECT_START_DATE'); ?>',
		showStatus: true,
		beforeShowDay: function (dateObj){
			dateObj.setHours(0,0,0,0);
			var dateFormatted = $.datepick.formatDate('yy-m-d', dateObj);
			if ($.inArray(dayShortNames[dateObj.getDay()], disabledDays) > -1){
				return [false, 'ui-datepicker-disabled ui-datepicker-shipable', 'Disabled By Admin'];
			}else if ($.inArray(dateFormatted, bookedDates) > -1){
				return [false, 'ui-datepicker-reserved', 'Reserved'];
			}else if ($.inArray(dateFormatted, disabledDatesPadding) > -1){
				return [false, 'ui-datepicker-disabled', 'Disabled by Admin'];
			}else if ($.inArray(dateFormatted, shippingDaysPadding) > -1){
				return [true, 'hasd dayto-'+shippingDaysArray[$.inArray(dateFormatted, shippingDaysPadding)], 'Available'];
			}else{
				if (disabledDates.length > 0){
					for (var i=0; i<disabledDates.length; i++){
						var dateFrom = new Date();
						dateFrom.setFullYear(
						disabledDates[i][0][0],
						disabledDates[i][0][1]-1,
						disabledDates[i][0][2]
						);
						dateFrom.setHours(0,0,0,0);

						var dateTo = new Date();
						dateTo.setFullYear(
						disabledDates[i][1][0],
						disabledDates[i][1][1]-1,
						disabledDates[i][1][2]
						);
						dateTo.setHours(0,0,0,0);

						if (dateObj >= dateFrom && dateObj <= dateTo){
							return [false, 'ui-datepicker-disabled', '<?php echo sysLanguage::get('PPR_DISABLED_BY_ADMIN'); ?>'];
						}
					}
				}
			}
			return [true, '', '<?php echo sysLanguage::get('PPR_AVAILABLE'); ?>'];
		},
		onHover: function (value, date, inst, curTd){
			if (date == null){
				$('.ui-datepicker-shipping-day-hover').removeClass('ui-datepicker-shipping-day-hover');
				$(curTd).removeClass('ui-datepicker-start_date');
			}else{
				$(curTd).addClass('ui-datepicker-start_date');
				var shippingDaysBefore = $('input[name=rental_shipping]:checked').attr('days_before');
                var shippingDaysAfter = $('input[name=rental_shipping]:checked').attr('days_after');
				var prevTD = $(curTd);
				var nextTD = $(curTd);

				allowSelectionBefore = true;
                allowSelectionAfter = true;

				if (!isStart) {
					for (var i = 0; i < shippingDaysBefore; i++) {
						if (prevTD.prev().size() <= 0) {
							if (prevTD.find('a').html() == '1' || prevTD.html() == '1') {
								prevTD = prevTD.closest('.ui-datepicker-group').prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
							} else {
								prevTD = prevTD.parent().prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
							}
						} else {
							prevTD = prevTD.prev();
						}

						if (prevTD.hasClass('ui-datepicker-other-month')) {
							prevTD = prevTD.closest('.ui-datepicker-group').prev().find('td').filter(':not(.ui-datepicker-other-month)').last();
						}

						$('a', prevTD).addClass('ui-datepicker-shipping-day-hover');
						if (prevTD.hasClass('ui-state-disabled') && !prevTD.hasClass('ui-datepicker-shipable')) {
							allowSelectionBefore = false;
						}

					}
				} else {
					for (var i = 0; i < shippingDaysAfter; i++) {
						if (nextTD.next().size() <= 0) {
							nextTD = nextTD.parent().next().find('td').first();
						} else {
							nextTD = nextTD.next();
						}

						if (nextTD.hasClass('ui-datepicker-other-month')) {
							nextTD = nextTD.closest('.ui-datepicker-group').next().find('td').filter(':not(.ui-datepicker-other-month)').first();
						}

						$('a', nextTD).addClass('ui-datepicker-shipping-day-hover');

						if (nextTD.hasClass('ui-state-disabled') && !nextTD.hasClass('ui-datepicker-shipable')) {
							allowSelectionAfter = false;
						}

					}
				}

			}
		},
		onDayClick: function (date, inst, td){
				var shippingLabel;
	            var myclass = '';
	            var sDay = 0;
	            var words;
	            var sDaysArr;
	            var shippingDaysBefore = $('input[name=rental_shipping]:checked').attr('days_before');
	            var shippingDaysAfter = $('input[name=rental_shipping]:checked').attr('days_after');
	            myclass = $(td).attr('class');
	            if (myclass) {
	                words = myclass.split(' ');
	                sDay = 1000;
	                for (var j = 0; j < words.length; j++) {
	                    if (words[j].indexOf('dayto') >= 0) {
	                        sDaysArr = words[j].split('-');
	                        sDay = parseInt(sDaysArr[1]);
	                        break;
	                    }
	                }

	                if (!isStart) {
	                    if (sDay - shippingDaysBefore <= 0) {
	                        allowSelectionBefore = false;
	                    }
	                } else {
	                    if(sDay != 1000)
	                    if (shippingDaysAfter > sDay) {
	                        allowSelectionAfter = false;
	                    }
	                }
	            }
	            if (selected == 'start'){
	                allowSelection = true;
	                for(var k=0;k<bookedDates.length;k++){
	                    bDateArr = bookedDates[k].split('-');
	                    bDate = new Date(parseInt(bDateArr[0]),parseInt(bDateArr[1])-1, parseInt(bDateArr[2]));
	                    if(selectedDate.getTime()<=bDate.getTime() && date.getTime()>=bDate.getTime()){
	                        allowSelection = false;
	                    }
	                }
	                allowSelectionMin = true;
	                if ((date.getTime() - selectedDate.getTime()) < ((minRentalPeriod))){
	                    allowSelectionMin = false;
	                }
					allowSelectionMax = true;
	                if (((date.getTime() - selectedDate.getTime()) > (maxRentalPeriod)) && maxRentalPeriod != -1){
	                    allowSelectionMax = false;
	                }
	            }

	            //end check here
	            if (allowSelectionMin == false){
					alert(minRentalPeriodMessage);
					return false;
				}
			 	if (allowSelectionMax == false){
					alert(maxRentalPeriodMessage);
					return false;
				}
	            if (allowSelection == false){
					alert('<?php echo sysLanguage::get('PPR_ERR_RESERVATION_BETWEEN'); ?>');
					return false;
				}
	            if (allowSelectionBefore == false){
					var shippingDaysBefore = $('input[name=rental_shipping]:checked').attr('days_before');
					shippingLabel = $('input[name=rental_shipping]:checked').parent().parent().find('td').first().html();
					alert('<?php echo sysLanguage::get('PPR_ERR_SHIP_METHOD'); ?> ' + shippingLabel + ', <?php echo sysLanguage::get('PPR_ERR_NEED_TO_ALLOW'); ?> ' + shippingDaysBefore + ' <?php echo sysLanguage::get('PPR_ERR_SHIP_DAYS_BEFORE_RESERVATION'); ?>');
					return false;
				}
	            if (allowSelectionAfter == false){
	                var shippingDaysAfter = $('input[name=rental_shipping]:checked').attr('days_after');
					shippingLabel = $('input[name=rental_shipping]:checked').parent().parent().find('td').first().html();
					alert('<?php echo sysLanguage::get('PPR_ERR_SHIP_METHOD'); ?> ' + shippingLabel + ', <?php echo sysLanguage::get('PPR_ERR_NEED_TO_ALLOW'); ?> ' + shippingDaysBefore + ' <?php echo sysLanguage::get('PPR_ERR_SHIP_DAYS_AFTER_RESERVATION'); ?>');
					return false;
	            }

			selected = (selected == '' || selected == 'end' ? 'start' : 'end');

			if (selected == 'start'){
                selectedDate = date;
				$('#datePicker').datepick('option', 'initStatus', '<?php echo sysLanguage::get('PPR_SELECT_END_DATE'); ?>');
				$('#inCart').hide();
			}else if (selected == 'end'){
				$('#datePicker').datepick('option', 'initStatus', '<?php echo sysLanguage::get('PPR_DATES_SELECTED'); ?>.<br /><?php echo sysLanguage::get('PPR_CLICK_RESTART_PROCESS'); ?>');
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
				$('#end_date').val(monthTs+'/'+ daysTs+'/'+date.getFullYear());
				var $this = $('#datePicker');
				$sDate = new Date($('#start_date').val());
				$eDate = new Date($('#end_date').val());
				//alert($sDate + '   '+$eDate +' '+$('#start_date').val()+'  '+$('#end_date').val());
				if($sDate.getTime() != $eDate.getTime()){
					showAjaxLoader($this, 'xlarge');
					$.ajax({
						cache: false,
						dataType: 'json',
						type: 'post',
						url: js_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
						data: 'action=checkRes&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),
						success: function (data){
							if (data.success == true){
								$('#priceQuote').html(data.price + ' ' + data.message);
								$('#inCart').show();
								$('#inCart').button();
								//$('#checkAvail').hide();
							}else if (data.success == 'not_supported'){
								$('#priceQuote').html(data.price);
							}else{
								alert('<?php echo sysLanguage::get('PPR_NOTICE_RESERVATION_NOT_AVAILABLE'); ?>.');
							}
							hideAjaxLoader($this);
						}
					});
				}
			}else{
				$('#datePicker').datepick('option', 'initStatus', '<?php echo sysLanguage::get('PPR_SELECT_START_DATE'); ?>');
			}
<?php
		if($allowHourly){
		?>
			$('#calendarTime').show();
			$('#calendarTime').fullCalendar( 'gotoDate', date);
			$sDate = new Date($('#start_date').val());
			$eDate = new Date($('#end_date').val());

			if($sDate.getTime() != $eDate.getTime() || selected == 'end'){
				if(selectedStartTimeTd != null){
					selectedStartTimeTd.data('element').remove();
				}
				if(selectedEndTimeTd != null){
					selectedEndTimeTd.data('element').remove();
				}
			}
			if($sDate.getTime() != $eDate.getTime()){
				$('#calendarTime').hide();
			}
	<?php
	}
	?>
		},
		onSelect: function (value, date, inst){
			var dates = value.split(',');
	<?php
	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_FORCE_START_DATE') == 'True'){
	?>
	    	if($('#start_date').val() == '' && isStart){
				$('#start_date').val(dates[0]);
				$('#end_date').val(dates[1]);
				isStart = false;
				if (dates[0] != dates[1]){
					$('#datePicker').datepick('option', 'maxDate', null);
				}else{
					isStart = true;
				}
			}else{
				var todayDate = new Date();
				$('#end_date').val(dates[0]);
				selected = 'start';
				selectedDate = todayDate;
				isStart = true;

			}
<?php
}else{
	?>
	 	var dates = value.split(',');
			$('#start_date').val(dates[0]);
			$('#end_date').val(dates[1]);
			isStart = false;
			if (dates[0] != dates[1]){
				$('#datePicker').datepick('option', 'maxDate', null);
			}else{
                		isStart = true;
            		}
	<?php }
		?>
		}
	});
	<?php
	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_FORCE_START_DATE') == 'True'){
	?>
	var todayDate = new Date();
	selected = 'start';
	selectedDate = todayDate;
	isStart = true;
	var today_day = '';
	var today_month = '';

	if(todayDate.getDate() < 10){
		today_day = '0'+todayDate.getDate();
	}else{
		today_day = todayDate.getDate();
	}

	if(todayDate.getMonth() < 10){
		today_month = '0'+(todayDate.getMonth()+1);
	}else{
		today_month = todayDate.getMonth()+1;
	}

	$('#start_date').val(today_month +'/'+today_day+'/'+todayDate.getFullYear());
	 //$('#datePicker').datepick('setDate', new Date(), new Date());
	 //$('#datePicker')._selectDay(d);
	<?php
	}
	?>
	$('#checkAvail').click(function (){
		var $this = $(this);
		showAjaxLoader($this, 'small');
		if (
			($('#start_date').val() == '')
		||
			($('#end_date').val() == '')
		||
			($('input[name=rental_shipping]').size() > 0 && $('input[name=rental_shipping]:checked').size() <= 0)
		){
			var errorMsg = '';
			if ($('input[name=rental_shipping]').size() > 0 && $('input[name=rental_shipping]:checked').size() <= 0){
				errorMsg += "\n" + '<?php echo sysLanguage::get('PPR_SHIPPING_METHOD'); ?>';
			}
			if ($('#start_date').val() == ''){
				errorMsg += "\n" + '<?php echo sysLanguage::get('PPR_START_METHOD'); ?>';
			}
			if ($('#end_date').val() == ''){
				errorMsg += "\n" + '<?php echo sysLanguage::get('PPR_END_METHOD'); ?>';
			}
			alert('<?php echo sysLanguage::get('PPR_ERR_CHOOSE'); ?> ' + errorMsg);
			hideAjaxLoader($this);
		}else{
			$.ajax({
				cache: false,
				dataType: 'json',
				type: 'post',
				url: js_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
				data: 'action=checkRes&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),
				success: function (data){
					if (data.success == true){
						$('#priceQuote').html(data.price + ' ' + data.message);
						$('#inCart').show();
						$('#inCart').button();
						$('#checkAvail').hide();
					}else if (data.success == 'not_supported'){
						$('#priceQuote').html(data.price);
					}else{
						alert('<?php echo sysLanguage::get('PPR_NOTICE_RESERVATION_NOT_AVAILABLE'); ?>.');
					}
					hideAjaxLoader($this);
				}
			});
		}
	});

	$('#rental_qty').blur(function (){
		showAjaxLoader($('#datePicker'), 'xlarge');
		$.ajax({
			cache: false,
			dataType: 'json',
			type: 'post',
			url: js_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
			data: 'action=getReservedDates&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),
			success: function (data){
				if (data.success == true){
					bookedDates = data.bookedDates;
					shippingDaysPadding = data.shippingDaysPadding;
					shippingDaysArray = data.shippingDaysArray;
					disabledDatesPadding = data.disabledDatesPadding;
					disabledDays = data.disabledDays;
					if($('#selected_period')){
						$('#selected_period').html(data.semData);
					}
					$('#start_date, #end_date').val('');
					//$('#datePicker').datepick('setDate', new Date(), new Date());
					$('#datePicker').datepick('refresh');

					$('#inCart').hide();
				}
				hideAjaxLoader($('#datePicker'));
			}
		});
	});

	$('#selected_period').change(function(){
		if($(this).val() != ''){
			var selectedPeriod = $(this);
			var startDateString = $("#selected_period option:selected").attr('start_date');
			var endDateString = $("#selected_period option:selected").attr('end_date');
			$('#start_date').val(startDateString.substr(0, startDateString.length-9));
			$('#end_date').val(endDateString.substr(0, endDateString.length-9));
			//var price = $("#selected_period option:selected").attr('price');
			showAjaxLoader(selectedPeriod, 'xlarge');
			$.ajax({
					cache: false,
					dataType: 'json',
					type: 'post',
					url: js_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
					data: 'action=checkRes&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),//+'&price='+price,//isSemester=1&
					success: function (data){
						if (data.success == true){
							$('#priceQuote').html(data.price + ' ' + data.message);
							$('#inCart').show();
							$('#inCart').button();
							//$('#checkAvail').hide();
						}else if (data.success == 'not_supported'){
							$('#priceQuote').html(data.price);
						}else{
							alert('<?php echo sysLanguage::get('PPR_NOTICE_RESERVATION_NOT_AVAILABLE'); ?>.');
						}
						hideAjaxLoader(selectedPeriod);
					}
			});
		}else{
			$('#priceQuote').html('');
			$('#inCart').hide();
		}
	});
	var isTimeStart = false;
	var selectedStartTimeTd = null;
	var selectedEndTimeTd = null;
	var selectedStartTime = null;
	var selectedEndTime = null;
	$('#calendarTime').fullCalendar({
			header: {
				 left:   '',
    			center: '',
    			right:  ''
			},
			theme: true,
			allDaySlot:false,
			slotMinutes:'<?php echo $minTime;?>',
			//axisFormat:'h:mm',
			editable: false,
			disableDragging: true,
			disableResizing: true,
			minTime:'<?php echo sysConfig::get('EXTENSION_PAY_PER_RENTALS_START_TIME');?>',
			maxTime:'<?php echo sysConfig::get('EXTENSION_PAY_PER_RENTALS_END_TIME');?>',
			defaultView: 'agendaDay',
			height: 296,
			events: startArray,
			dayClick: function(date, allDay, jsEvent, view) {
				   if(isTimeStart == false){
				   		isTimeStart = true;
					    if(selectedStartTimeTd != null){
							selectedStartTimeTd.data('element').remove();
						}
					    if(selectedEndTimeTd != null){
					    	selectedEndTimeTd.data('element').remove();
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

						$('#start_date').val(today_month +'/'+today_day+'/'+selectedStartTime.getFullYear() + ' '+selectedStartTime.getHours() + ':'+selectedStartTime.getMinutes()+':00');
				   }else{
					   if(selectedStartTime < new Date(date)){

						    var allowSelectionTime = true;
							for(var k=0;k<bookedTimesArr.length;k++){
								//bDateArr = bookedDates[k].split('-');
								//bDate = new Date(parseInt(bDateArr[0]),parseInt(bDateArr[1])-1, parseInt(bDateArr[2]));
								if(selectedStartTime.getTime()<=bookedTimesArr[k].getTime() && date.getTime()>=bookedTimesArr[k].getTime()){
									allowSelectionTime = false;
								}
							}
							var allowSelectionMinTime = true;
							if ((date.getTime() - selectedStartTime.getTime()) < ((minRentalPeriod))){
								allowSelectionMinTime = false;
							}
							var allowSelectionMaxTime = true;
							if (((date.getTime() - selectedStartTime.getTime()) > (maxRentalPeriod)) && maxRentalPeriod != -1){
								alert(date.getTime() - selectedStartTime.getTime());
								allowSelectionMaxTime = false;
							}


						//end check here
						if (allowSelectionMinTime == false){
							alert(minRentalPeriodMessage);
							return false;
						}
						if (allowSelectionMaxTime == false){
							alert(maxRentalPeriodMessage);
							return false;
						}
						if (allowSelectionTime == false){
							alert('<?php echo sysLanguage::get('PPR_ERR_RESERVATION_BETWEEN'); ?>');
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

						   $('#end_date').val(today_month +'/'+today_day+'/'+selectedEndTime.getFullYear() + ' '+selectedEndTime.getHours() + ':'+selectedEndTime.getMinutes()+':00');


						   var $this = $('#datePicker');

								showAjaxLoader($this, 'xlarge');
								$.ajax({
									cache: false,
									dataType: 'json',
									type: 'post',
									url: js_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
									data: 'action=checkRes&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),
									success: function (data){
										if (data.success == true){
											$('#priceQuote').html(data.price + ' ' + data.message);
											$('#inCart').show();
											$('#inCart').button();
											//$('#checkAvail').hide();
										}else if (data.success == 'not_supported'){
											$('#priceQuote').html(data.price);
										}else{
											alert('<?php echo sysLanguage::get('PPR_NOTICE_RESERVATION_NOT_AVAILABLE'); ?>.');
										}
										hideAjaxLoader($this);
									}
								});
					   }
					   //reset selected td;
				   }
				// change the day's background color just for fun
				//$(this).css('background-color', 'red');

			}
		});

	<?php
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'True'){
	?>
		showOverlay($('#datePicker'));
	<?php
		}
	?>
	$('#inCart').click(function() {
		$(this).parent().parent().append('<input type="hidden" name="add_reservation_product">');
		$(this).parent().parent().submit();
		return false;
	});
});
</script>
<style>
.ui-datepicker-group { margin:.5em; }
.ui-datepicker-header { padding:0;text-align:center; }
.ui-datepicker-header span { margin: .5em; }
.ui-datepicker .ui-datepicker-prev, .ui-datepicker .ui-datepicker-next { top: 0px; }
.fc-event-time{
	display:none !important;
}
.fc-event{
	width:460px !important;
}
.ui-datepicker-status { margin:.5em;text-align:center;font-weight:bold; }
.fc-minor{

}
#calendarTime{
	width:540px;
}
.fc-agenda-body td.ui-state-default{
	cursor:pointer;
}
#datePicker{
}
.ui-datepicker{display:block;}
/*#datePicker { font-size: 1.25em; }
#datePicker .ui-datepicker-calendar td { font-size: 1.25em; }
#datePicker .ui-datepicker-start_date { background: #00FF00; }*/
.ui-datepicker-shipping-day-hover, .ui-datepicker-shipping-day-hover-info { background: #F7C8D3; }
#datePicker .ui-state-active { background:#CACEE6; }
</style>
  <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox reservationTable">
   <tr class="infoBoxContents">
    <td><table cellpadding="3" cellspacing="0" border="0" width="100%">
     <tr>
      <td colspan="2" class="main" id="daysMsg" style="display:none;"><b><?php echo sysLanguage::get('TEXT_ONETIME_INTRO');?></b></td>
     </tr>
	<?php
      if ($purchaseTypeClass->getDepositAmount() > 0){
		$infoIcon = htmlBase::newElement('icon')
		->setType('info')
		->attr('onclick', 'popupWindow(\'' . itw_app_link('appExt=infoPages&dialog=true', 'show_page', 'ppr_deposit_info') . '\',400,300);')
		->css(array(
			'display' => 'inline-block',
			'cursor' => 'pointer'
		));
	?>
	<tr>
      <td></td>
      <td class="main"><?php echo sysLanguage::get('PPR_DEPOSIT_AMOUNT') . ' - '. $currencies->format($purchaseTypeClass->getDepositAmount()) . $infoIcon->draw();?></td>
     </tr>
		<?php
	}
		?>
     <tr>
      <td></td>
      <td><?php echo $purchaseTypeClass->getPricingTable(false, false, false);?></td>
     </tr>
<?php
	//this part needs redone
	 if ($maxRentalPeriod > 0){
?>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_MAX') . ' ' . ReservationUtilities::getPeriodType($pprTable->max_type);?>: </td>
      <td class="main" id="maxPeriod"><?php echo $pprTable->max_period. ' '.ReservationUtilities::getPeriodType($pprTable->max_type);?></td>
     </tr>
<?php
}
?>
		<?php
if ($minRentalPeriod > 0){
?>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_MIN') . ' ' . ReservationUtilities::getPeriodType($pprTable->min_type);?>: </td>
      <td class="main" id="minPeriod"><?php echo $pprTable->min_period.' '.ReservationUtilities::getPeriodType($pprTable->min_type);?></td>
     </tr>
<?php
}
?>
	<?php
if ($insurancePrice > 0){
?>
     <tr>
      <td class="main"><?php echo sysLanguage::get('TEXT_INSURANCE') . ' ' ;?>: </td>
      <td class="main" id="insurance_price"><?php echo $currencies->format($insurancePrice) ;?></td>
     </tr>
<?php
}
?>
     <tr>
      <td class="main"><?php echo sysLanguage::get('ENTRY_QUANTITY');?></td>
      <td><input type="text" size="3" id="rental_qty" name="rental_qty" value="<?php echo (isset($rInfo) ? $rInfo['reservationInfo']['quantity'] : '1');?>"></td>
     </tr>
     <?php
     if ($purchaseTypeClass->shippingIsNone() === false && $purchaseTypeClass->shippingIsStore() === false){
     	echo $shippingTable;
     }
     ?>
		<?php
			$QPeriods = Doctrine_Query::create()
			->from('ProductsPayPerPeriods')
			->where('products_id=?', $_GET['products_id'])
			->andWhere('price > 0')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		if(count($QPeriods) > 0){
		 ?>
	<tr>
      <td class="main" colspan="2">
		  <?php
		  	$CalOrSemester = htmlBase::newElement('radio')
			->addGroup(array(
				'checked' => 1,
				'separator' => '<br />',
				'name' => 'cal_or_semester',
				'data' => array(
					array(
						'label' => sysLanguage::get('TEXT_USE_CALENDAR'),
						'labelPosition' => 'before',
						'value' => '1'
					),
					array(
						'label' => sysLanguage::get('TEXT_USE_SEMESTER'),
						'labelPosition' => 'before',
						'value' => '0'
					)
				)
			));
			  echo $CalOrSemester->draw();
		  ?>

      </td>
     </tr>
	<tr id="semRow">
      <td class="main" colspan="2">
		  <?php
		  	$selectSem = htmlBase::newElement('selectbox')
		  	->setName('semester_name')
		  	->setLabel(sysLanguage::get('TEXT_SELECT_PERIOD'))
		  	->setLabelPosition('before')
		  	->attr('id','selected_period');
			$selectSem->addOption('',sysLanguage::get('TEXT_SELECT_SEMESTER'));

			foreach($semDates as $sDate){

				$attr = array(
							/*array(
								'name' => 'price',
								'value' => $sDate['price']
							)*/
						array(
							'name' => 'start_date',
							'value' => $sDate['start_date']
						),
						array(
							'name' => 'end_date',
							'value' => $sDate['end_date']
						)
					);
				$selectSem->addOptionWithAttributes($sDate['period_name'], $sDate['period_name'],$attr);

				//$selectSem->addOption($sDate['period_name'], $sDate['period_name']);
			}
			$moreInfo = htmlBase::newElement('a')
			->attr('id','moreInfoSem')
		  	->html(sysLanguage::get('TEXT_MORE_INFO_SEM'));
			echo $selectSem->draw();//.$moreInfo;
		  ?>

      </td>
     </tr>
			<?php
			}
			?>
     <tr id="dateRow">
      <td colspan="2"><table cellpadding="0" cellspacing="3" border="0" width="100%">
       <tr>
        <td width="20%"><table cellpadding="0" cellspacing="3" border="0">
   	     <tr>
          <td style="width:10px;height:10px;" class="ui-datepicker-reserved ui-state-disabled">&nbsp;</td>
          <td style="font-size:.8em"> - <?php echo sysLanguage::get('PPR_UNAVAILABLE_DAYS'); ?>.</td>
         </tr>
<?php if ($purchaseTypeClass->shippingIsNone() === false && $purchaseTypeClass->shippingIsStore() === false){ ?>
       <tr>
        <td style="width:10px;height:10px;" class="ui-datepicker-shipping-day-hover-info">&nbsp;</td>
        <td style="font-size:.8em"> - <?php echo sysLanguage::get('PPR_SHIPPING_DAYS'); ?>.</td>
       </tr>
<?php } ?>
        </table></td>
        <td width="50%"><table cellpadding="0" cellspacing="3" border="0" width="100%">
         <tr>
          <td style="font-size:.8em" width="100%">* <?php echo sysLanguage::get('PPR_CALENDAR_EXPLAIN1'); ?><br /><?php echo sysLanguage::get('PPR_CALENDAR_EXPLAIN2'); ?><br /><?php echo sysLanguage::get('PPR_CALENDAR_EXPLAIN3'); ?>.</td>
         </tr>
        </table></td>
       </tr>
      </table><table cellpadding="3" cellspacing="0" border="0" width="100%">
       <tr>
        <td valign="top"><div type="text" id="datePicker"></div>
		<div id="calendarTime">

		</div>

		</td>
       </tr>
      </table></td>
     </tr>

     <tr id="dateSelectedCalendar">
      <td class="main"><?php echo sysLanguage::get('ENTRY_RENTAL_DATES_SELECTED');?></td>
      <td><input type="text" name="start_date" id="start_date" value="<?php echo (isset($rInfo) ? $rInfo['reservationInfo']['start_date'] : '');?>" readonly="readonly"> To <input type="text" name="end_date" id="end_date" value="<?php echo (isset($rInfo) ? $rInfo['reservationInfo']['end_date'] : '');?>" readonly="readonly">
      <?php
        echo htmlBase::newElement('button')
             ->setId('refreshCal')
             ->setName('refreshCal')
             ->setText(sysLanguage::get('PPR_CALENDAR_RESET'))
             ->draw();
      ?>

      </td>
     </tr>

    </table></td>
    <td id="calMessage"></td>
      </tr>
     </table>
   <?php
	   $pageContents = ob_get_contents();
	   ob_end_clean();

	   $pageTitle = 'Create Reservation';

	   $pageButtons = '';
	   if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_SHOW_CALENDAR_PRODUCT_INFO') == 'False') {
		    $pageButtons .= htmlBase::newElement('button')
		    ->usePreset('back')
		    ->setHref(itw_app_link('products_id=' . $pID_string, 'product', 'info'))
		    ->draw();
	   }

	   $pageButtons .= sysLanguage::get('TEXT_ESTIMATED_PRICING') . '<span id="priceQuote"></span>';
	   $pageButtons .= '<input type="hidden" name="products_id" id="pID" value="' . $product->getID() . '">';
	   $pageButtons .= $purchaseTypeClass->getHiddenFields($pID_string);

	   $pageButtons .= htmlBase::newElement('button')
	   ->setId('checkAvail')
	   ->setName('checkAvail')
	   ->setText(sysLanguage::get('TEXT_BUTTON_CHECK_AVAIL'))
	   ->draw();

	   $pageButtons .= htmlBase::newElement('div')
	   ->attr('id','inCart')
	   ->css(array(
		   'display'   => 'inline-block',
		   'width' => '150px'
	   ))
	   ->html(sysLanguage::get('TEXT_BUTTON_IN_CART'))
	   ->draw();

	   $pageContent->set('pageForm', array(
		   'name' => 'build_reservation',
		   'action' => itw_app_link(tep_get_all_get_params(array('action'))),
		   'method' => 'post'
	   ));

	   $pageContent->set('pageTitle', $pageTitle);
	   $pageContent->set('pageContent', $pageContents);
	   $pageContent->set('pageButtons', $pageButtons);
