<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utilities
 *
 * @author Stephen
 */
class ReservationUtilities {

	public static function getShippingDetails($method = null){
		$Quote = null;
		$ModuleQuote = OrderShippingModules::quote($method, 'zonereservation');
		if (isset($ModuleQuote[0]['methods']) && !empty($ModuleQuote[0]['methods'][0])){
			$Quote = $ModuleQuote[0]['methods'][0];
		}
		return $Quote;
	}

	public static function getEvent($evId = null){
		$Query = Doctrine_Query::create()
		->from('PayPerRentalEvents');

		if (is_null($evId) === false && is_numeric($evId) === false){
			$Query->andWhere('events_name = ?', $evId);
		}else{
			$Query->andWhere('events_id = ?', $evId);
		}

		if (is_null($evId) === false){
			$Result = $Query->fetchOne();
		}else{
			$Result = $Query->execute();
		}
		return $Result;
	}


	public static function addReservationProductToCart($simPost = false){
		global $ShoppingCart;
		if ($simPost !== false){
			$_POST['products_id'] = $simPost['products_id'];
			$_POST['id'] = $simPost['id']; /* @TODO: Add event to allow attributes extension to handle this */
			$_POST['rental_qty'] = $simPost['rental_qty'];
			$_POST['insurance'] = $simPost['insurance'];
			$_POST['rental_shipping'] = $simPost['rental_shipping'];
			$_POST['start_date'] = $simPost['start_date'];
			$_POST['end_date'] = $simPost['end_date'];
			$_POST['semester_name'] = $simPost['semester_name'];
			if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_EVENTS') == 'True'){
				$_POST['event_name'] = $simPost['event_name'];
				$_POST['event_date'] = $simPost['event_date'];
			}
		}
		$ShoppingCart->addProduct($_POST['products_id'], 'reservation', $_POST['rental_qty']);
	}

	public static function getPeriodTime($period, $type){
		$QPayPerRentalTypes = Doctrine_Query::create()
		->from('PayPerRentalTypes')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($QPayPerRentalTypes as $iType){
			if($type == $iType['pay_per_rental_types_id']){
				 return $period * $iType['minutes'];
			}
		}
		return 0;
	}

	public static function getPeriodType($type){
	   $QPayPerRentalTypes = Doctrine_Query::create()
		->from('PayPerRentalTypes')
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		foreach($QPayPerRentalTypes as $iType){
			if($type == $iType['pay_per_rental_types_id']){
				 return $iType['pay_per_rental_types_name'];
			}
		}
		return '';
	}

	public static function getCalendar($productsId, $product, $purchaseTypeClass, $showShipping = true)
	{

		$pID_string = $productsId;

		$pprTable = Doctrine_Core::getTable('ProductsPayPerRental')->findOneByProductsId($pID_string);
		$total_weight = $product->getWeight();
		OrderShippingModules::calculateWeight();

		/*periods*/
		$QPeriods = Doctrine_Query::create()
				->from('ProductsPayPerPeriods')
				->where('products_id=?', $pID_string)
				->andWhere('price > 0')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
		$semDates = array();

		$sDate = array();
		if (count($QPeriods)) {
			$QPeriodsNames = Doctrine_Query::create()
					->from('PayPerRentalPeriods')
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			foreach ($QPeriods as $iPeriod) {
				$periodName = '';
				foreach ($QPeriodsNames as $periodNames) {
					if ($periodNames['period_id'] == $iPeriod['period_id']) {
						$periodName = $periodNames;
						break;
					}
				}
				if ($periodName != '') {
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

		$allowHourly = (sysConfig::get('EXTENSION_PAY_PER_RENTALS_ALLOW_HOURLY') == 'True') ? true : false;
		$minTime = 15; //slotMinutes

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_GLOBAL_MIN_RENTAL_DAYS') == 'False') {
			$minRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->min_period, $pprTable->min_type) * 60 * 1000;

		} else {
			$minRentalPeriod = (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_MIN_RENTAL_DAYS') * 24 * 60 * 60 * 1000;
		}

		$maxRentalPeriod = -1;

		if ($pprTable->max_period > 0) {
			$maxRentalPeriod = ReservationUtilities::getPeriodTime($pprTable->max_period, $pprTable->max_type) * 60 * 1000;
		}

		$startTime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		//$endTime = mktime(0,0,0,date('m'), 1, date('Y')+3);
		$reservArr = array();
		$barcodesBooked = array();
		$bookings = $purchaseTypeClass->getBookedDaysArray(date('Y-m-d', $startTime), 1, &$reservArr, &$barcodesBooked);
		$timeBookings = $purchaseTypeClass->getBookedTimeDaysArray(date('Y-m-d', $startTime), 1, $minTime, $reservArr, $barcodesBooked);
		//here I have to add an array for Times Booked
		$maxShippingDays = -1;
		$shippingTable = '';
		if ($purchaseTypeClass->shippingIsNone() === false && $purchaseTypeClass->shippingIsStore() === false) {
			if($showShipping){
				$shippingTable = $purchaseTypeClass->buildShippingTable(true, false);
			}
			$maxShippingDays = $purchaseTypeClass->getMaxShippingDays(date('Y-m-d', $startTime));
		}
		/**
		 * Days Bookings
		 */
		$booked = array();
		$shippingDaysPadding = array();
		$shippingDaysArray = array();
		$paddingDays = array();
		foreach ($bookings as $iBook) {
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
					if ((int)substr($shippingDaysArray[$valPos], 1, strlen($shippingDaysArray[$valPos]) - 2) > $i) {
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
					if ((int)substr($shippingDaysArray[$valPos], 1, strlen($shippingDaysArray[$valPos]) - 2) > $i) {
						$shippingDaysArray[$valPos] = '"' . $i . '"';
					}
				}
			}
		}

		$disabledDays = sysConfig::explode('EXTENSION_PAY_PER_RENTALS_DISABLED_DAYS', ',');
		$startTimePadding = strtotime(date('Y-m-d'));
		$endTimePadding = strtotime('+' . (int)sysConfig::get('EXTENSION_PAY_PER_RENTALS_DATE_PADDING') . ' days', $startTimePadding);
		while ($startTimePadding <= $endTimePadding) {
			$dateFormatted = date('Y-n-j', $startTimePadding);
			$paddingDays[] = '"' . $dateFormatted . '"';
			//period
			$op = 0;
			foreach ($semDates as $sDate) {
				if (strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])) {
					unset($semDates[$op]);
				}
				$op++;
			}
			$semDates = array_values($semDates);
			//end period
			$startTimePadding += 60 * 60 * 24;
		}

		$QBlockedDates = Doctrine_Query::create()
				->from('PayPerRentalBlockedDates')
				->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		foreach ($QBlockedDates as $bInfo) {
			$startTimePaddingArr = array();
			$endTimePaddingArr = array();

			if ($bInfo['recurring'] == 0) {
				$startTimePaddingArr[] = strtotime($bInfo['block_start_date']);
				$endTimePaddingArr[] = strtotime($bInfo['block_end_date']);
				$i = 1;
			} else {
				$i = 0;
				while (true) {
					$bstartDate = strtotime('+' . $i . ' years', strtotime($bInfo['block_start_date']));
					$bendDate = strtotime('+' . $i . ' years', strtotime($bInfo['block_end_date']));
					$startTimePaddingArr[] = $bstartDate;
					$endTimePaddingArr[] = $bendDate;
					$i++;
					if (date('Y', $bendDate) - 3 > date('Y')) {
						break;
					}
				}

			}
			$j = 0;
			while ($j < $i) {
				$startTimePadding = $startTimePaddingArr[$j];
				$endTimePadding = $endTimePaddingArr[$j];
				while ($startTimePadding <= $endTimePadding) {
					$dateFormatted = date('Y-n-j', $startTimePadding);
					$paddingDays[] = '"' . $dateFormatted . '"';
					//period
					$op = 0;
					foreach ($semDates as $sDate) {
						if (strtotime($dateFormatted) >= strtotime($sDate['start_date']) && strtotime($dateFormatted) <= strtotime($sDate['end_date'])) {
							unset($semDates[$op]);
						}
						$op++;
					}
					$semDates = array_values($semDates);
					//end period
					$startTimePadding += 60 * 60 * 24;
				}
				$j++;
			}
		}

		$op = 0;
		$dateFormatted = date('Y-n-j');
		foreach ($semDates as $sDate) {
			if (strtotime($dateFormatted) >= strtotime($sDate['start_date'])) {
				unset($semDates[$op]);
			}
			$op++;
		}
		$semDates = array_values($semDates);
		/**
		 * Time Bookings
		 */
		$timeBooked = array();
		$timeBookedDate = array();
		foreach ($timeBookings as $iBook) {
			$timeDateParse = date_parse($iBook);
			$stringStart = 'new Date(' . $timeDateParse['year'] . ',' . ($timeDateParse['month'] - 1) . ',' . $timeDateParse['day'] . ',' . $timeDateParse['hour'] . ',' . $timeDateParse['minute'] . ')';
			$stringEnd = 'new Date(' . $timeDateParse['year'] . ',' . ($timeDateParse['month'] - 1) . ',' . $timeDateParse['day'] . ',' . $timeDateParse['hour'] . ',' . ($timeDateParse['minute'] + 1) . ')';
			$timeBooked[] = "{title:'Not Available',start:" . $stringStart . ",end:" . $stringEnd . ", allDay:false}";
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

	var minRentalPeriodMessage = '<?php echo sysLanguage::get('PPR_ERR_AT_LEAST') . ' ' . $pprTable->min_period . ' ' . ReservationUtilities::getPeriodType($pprTable->min_type) . ' ' . sysLanguage::get('PPR_ERR_DAYS_RESERVED'); ?>';
	var maxRentalPeriodMessage = '<?php echo sysLanguage::get('PPR_ERR_MAXIMUM') . ' ' . $pprTable->max_period . ' ' . ReservationUtilities::getPeriodType($pprTable->max_type) . ' ' . sysLanguage::get('PPR_ERR_DAYS_RESERVED'); ?>';
	var allowSelectionBefore = true;
	var allowSelectionAfter = true;
	var allowSelection = true;
	var allowSelectionMin = true;
	var allowSelectionMax = true;
	var productsID = <?php echo $pID_string;?>;

	var startArray = [<?php echo implode(',', $timeBooked);?>];
	var bookedTimesArr = [<?php echo implode(',', $timeBookedDate);?>];

	var selected = '';
	var selectedDate;
	var isStart = false;
	//var autoChanged = false;
	var isHour = false;


	$(document).ready(function () {
		$('#inCart').hide();
		$('#checkAvail').hide();
		$('#refreshCal').click(function() {
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
	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_FORCE_START_DATE') == 'True') {
				?>
				$('#datePicker').datepick('setDate', -1);
				var todayDate = new Date();
				selected = 'start';
				selectedDate = todayDate;
				isStart = true;
				var today_day = '';
				var today_month = '';

				if (todayDate.getDate() < 10) {
					today_day = '0' + todayDate.getDate();
				} else {
					today_day = todayDate.getDate();
				}

				if (todayDate.getMonth() < 10) {
					today_month = '0' + todayDate.getMonth();
				} else {
					today_month = todayDate.getMonth();
				}

				$('#start_date').val(today_month + '/' + today_day + '/' + todayDate.getFullYear());
				$('#end_date').val('');
				//$('#datePicker').datepick('setDate', new Date(), new Date());
				//$('#datePicker')._selectDay(d);
				<?php

			} else {
				?>
				if (selected == 'start') {
					$('#datePicker').datepick('setDate', 0);
				} else {
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

		if ($('input[name=rental_shipping]').size() > 0 && $('input[name=rental_shipping]:checked').size() == 0) {
			$('input[name=rental_shipping]').each(function () {
				$(this).trigger('click');
			});
		}

		$('#datePicker').datepick({
			useThemeRoller: true,
			minDate: '+1',
			dateFormat: '<?php echo getJsDateFormat();?>',
			rangeSelect: <?php echo ((sysConfig::get('EXTENSION_PAY_PER_RENTALS_FORCE_START_DATE') == 'True') ? 'false' : 'true');?>,
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
			beforeShowDay: function (dateObj) {
				dateObj.setHours(0, 0, 0, 0);
				var dateFormatted = $.datepick.formatDate('yy-m-d', dateObj);
				if ($.inArray(dayShortNames[dateObj.getDay()], disabledDays) > -1) {
					return [false, 'ui-datepicker-disabled ui-datepicker-shipable', 'Disabled By Admin'];
				} else if ($.inArray(dateFormatted, bookedDates) > -1) {
					return [false, 'ui-datepicker-reserved', 'Reserved'];
				} else if ($.inArray(dateFormatted, disabledDatesPadding) > -1) {
					return [false, 'ui-datepicker-disabled', 'Disabled by Admin'];
				} else if ($.inArray(dateFormatted, shippingDaysPadding) > -1) {
					return [true, 'hasd dayto-' + shippingDaysArray[$.inArray(dateFormatted, shippingDaysPadding)], 'Available'];
				} else {
					if (disabledDates.length > 0) {
						for (var i = 0; i < disabledDates.length; i++) {
							var dateFrom = new Date();
							dateFrom.setFullYear(
									disabledDates[i][0][0],
									disabledDates[i][0][1] - 1,
									disabledDates[i][0][2]
									);
							dateFrom.setHours(0, 0, 0, 0);

							var dateTo = new Date();
							dateTo.setFullYear(
									disabledDates[i][1][0],
									disabledDates[i][1][1] - 1,
									disabledDates[i][1][2]
									);
							dateTo.setHours(0, 0, 0, 0);

							if (dateObj >= dateFrom && dateObj <= dateTo) {
								return [false, 'ui-datepicker-disabled', '<?php echo sysLanguage::get('PPR_DISABLED_BY_ADMIN'); ?>'];
							}
						}
					}
				}
				return [true, '', '<?php echo sysLanguage::get('PPR_AVAILABLE'); ?>'];
			},
			onHover: function (value, date, inst, curTd) {
				if (date == null) {
					$('.ui-datepicker-shipping-day-hover').removeClass('ui-datepicker-shipping-day-hover');
					$(curTd).removeClass('ui-datepicker-start_date');
				} else {
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
			onDayClick: function (date, inst, td) {
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
						if (sDay != 1000)
							if (shippingDaysAfter > sDay) {
								allowSelectionAfter = false;
							}
					}
				}
				if (selected == 'start') {
					allowSelection = true;
					for (var k = 0; k < bookedDates.length; k++) {
						bDateArr = bookedDates[k].split('-');
						bDate = new Date(parseInt(bDateArr[0]), parseInt(bDateArr[1]) - 1, parseInt(bDateArr[2]));
						if (selectedDate.getTime() <= bDate.getTime() && date.getTime() >= bDate.getTime()) {
							allowSelection = false;
						}
					}
					allowSelectionMin = true;
					if ((date.getTime() - selectedDate.getTime()) < ((minRentalPeriod))) {
						allowSelectionMin = false;
					}
					allowSelectionMax = true;
					if (((date.getTime() - selectedDate.getTime()) > (maxRentalPeriod)) && maxRentalPeriod != -1) {
						allowSelectionMax = false;
					}
				}

				//end check here
				if (allowSelectionMin == false) {
					alert(minRentalPeriodMessage);
					return false;
				}
				if (allowSelectionMax == false) {
					alert(maxRentalPeriodMessage);
					return false;
				}
				if (allowSelection == false) {
					alert('<?php echo sysLanguage::get('PPR_ERR_RESERVATION_BETWEEN'); ?>');
					return false;
				}
				if (allowSelectionBefore == false) {
					var shippingDaysBefore = $('input[name=rental_shipping]:checked').attr('days_before');
					shippingLabel = $('input[name=rental_shipping]:checked').parent().parent().find('td').first().html();
					alert('<?php echo sysLanguage::get('PPR_ERR_SHIP_METHOD'); ?> ' + shippingLabel + ', <?php echo sysLanguage::get('PPR_ERR_NEED_TO_ALLOW'); ?> ' + shippingDaysBefore + ' <?php echo sysLanguage::get('PPR_ERR_SHIP_DAYS_BEFORE_RESERVATION'); ?>');
					return false;
				}
				if (allowSelectionAfter == false) {
					var shippingDaysAfter = $('input[name=rental_shipping]:checked').attr('days_after');
					shippingLabel = $('input[name=rental_shipping]:checked').parent().parent().find('td').first().html();
					alert('<?php echo sysLanguage::get('PPR_ERR_SHIP_METHOD'); ?> ' + shippingLabel + ', <?php echo sysLanguage::get('PPR_ERR_NEED_TO_ALLOW'); ?> ' + shippingDaysBefore + ' <?php echo sysLanguage::get('PPR_ERR_SHIP_DAYS_AFTER_RESERVATION'); ?>');
					return false;
				}

				selected = (selected == '' || selected == 'end' ? 'start' : 'end');

				if (selected == 'start') {
					selectedDate = date;
					$('#datePicker').datepick('option', 'initStatus', '<?php echo sysLanguage::get('PPR_SELECT_END_DATE'); ?>');
					$('#inCart').hide();
				} else if (selected == 'end') {
					$('#datePicker').datepick('option', 'initStatus', '<?php echo sysLanguage::get('PPR_DATES_SELECTED'); ?>.<br /><?php echo sysLanguage::get('PPR_CLICK_RESTART_PROCESS'); ?>');
					var monthT = date.getMonth() + 1;
					var daysT = date.getDate();
					var daysTs = '';
					var monthTs = '';
					if (daysT < 10) {
						daysTs = '0' + daysT;
					} else {
						daysTs = daysT + '';
					}
					if (monthT < 10) {
						monthTs = '0' + monthT;
					} else {
						monthTs = monthT + '';
					}
					$('#end_date').val(monthTs + '/' + daysTs + '/' + date.getFullYear());
					var $this = $('#datePicker');
					$sDate = new Date($('#start_date').val());
					$eDate = new Date($('#end_date').val());
					//alert($sDate + '   '+$eDate +' '+$('#start_date').val()+'  '+$('#end_date').val());
					if ($sDate.getTime() != $eDate.getTime()) {
						showAjaxLoader($this, 'xlarge');
						$.ajax({
							cache: false,
							dataType: 'json',
							type: 'post',
							url: js_catalog_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
							data: 'action=checkRes&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),
							success: function (data) {
								if (data.success == true) {
									$('#priceQuote').html(data.price + ' ' + data.message);
									$('#inCart').show();
									$('#inCart').button();
									//$('#checkAvail').hide();
								} else if (data.success == 'not_supported') {
									$('#priceQuote').html(data.price);
								} else {
									alert('<?php echo sysLanguage::get('PPR_NOTICE_RESERVATION_NOT_AVAILABLE'); ?>.');
								}
								hideAjaxLoader($this);
							}
						});
					}
				} else {
					$('#datePicker').datepick('option', 'initStatus', '<?php echo sysLanguage::get('PPR_SELECT_START_DATE'); ?>');
				}
			<?php
   		if ($allowHourly) {
					?>
					$('#calendarTime').show();
					$('#calendarTime').fullCalendar('gotoDate', date);
					$sDate = new Date($('#start_date').val());
					$eDate = new Date($('#end_date').val());

					if ($sDate.getTime() != $eDate.getTime() || selected == 'end') {
						if (selectedStartTimeTd != null) {
							selectedStartTimeTd.data('element').remove();
						}
						if (selectedEndTimeTd != null) {
							selectedEndTimeTd.data('element').remove();
						}
					}
					if ($sDate.getTime() != $eDate.getTime()) {
						$('#calendarTime').hide();
					}
					<?php

				}
				?>
			},
			onSelect: function (value, date, inst) {
				var dates = value.split(',');
			<?php
   	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_FORCE_START_DATE') == 'True') {
					?>
					if ($('#start_date').val() == '' && isStart) {
						$('#start_date').val(dates[0]);
						$('#end_date').val(dates[1]);
						isStart = false;
						if (dates[0] != dates[1]) {
							$('#datePicker').datepick('option', 'maxDate', null);
						} else {
							isStart = true;
						}
					} else {
						var todayDate = new Date();
						$('#end_date').val(dates[0]);
						selected = 'start';
						selectedDate = todayDate;
						isStart = true;

					}
					<?php

				} else {
					?>
					var dates = value.split(',');
					$('#start_date').val(dates[0]);
					$('#end_date').val(dates[1]);
					isStart = false;
					if (dates[0] != dates[1]) {
						$('#datePicker').datepick('option', 'maxDate', null);
					} else {
						isStart = true;
					}
					<?php
				}
				?>
			}
		});
			<?php
   	if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_FORCE_START_DATE') == 'True') {
			?>
			var todayDate = new Date();
			selected = 'start';
			selectedDate = todayDate;
			isStart = true;
			var today_day = '';
			var today_month = '';

			if (todayDate.getDate() < 10) {
				today_day = '0' + todayDate.getDate();
			} else {
				today_day = todayDate.getDate();
			}

			if (todayDate.getMonth() < 10) {
				today_month = '0' + (todayDate.getMonth() + 1);
			} else {
				today_month = todayDate.getMonth() + 1;
			}

			$('#start_date').val(today_month + '/' + today_day + '/' + todayDate.getFullYear());
			//$('#datePicker').datepick('setDate', new Date(), new Date());
			//$('#datePicker')._selectDay(d);
			<?php

		}
		?>
		$('#checkAvail').click(function () {
			var $this = $(this);
			showAjaxLoader($this, 'small');
			if (
					($('#start_date').val() == '')
							||
							($('#end_date').val() == '')
							||
							($('input[name=rental_shipping]').size() > 0 && $('input[name=rental_shipping]:checked').size() <= 0)
					) {
				var errorMsg = '';
				if ($('input[name=rental_shipping]').size() > 0 && $('input[name=rental_shipping]:checked').size() <= 0) {
					errorMsg += "\n" + '<?php echo sysLanguage::get('PPR_SHIPPING_METHOD'); ?>';
				}
				if ($('#start_date').val() == '') {
					errorMsg += "\n" + '<?php echo sysLanguage::get('PPR_START_METHOD'); ?>';
				}
				if ($('#end_date').val() == '') {
					errorMsg += "\n" + '<?php echo sysLanguage::get('PPR_END_METHOD'); ?>';
				}
				alert('<?php echo sysLanguage::get('PPR_ERR_CHOOSE'); ?> ' + errorMsg);
				hideAjaxLoader($this);
			} else {
				$.ajax({
					cache: false,
					dataType: 'json',
					type: 'post',
					url: js_catalog_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
					data: 'action=checkRes&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),
					success: function (data) {
						if (data.success == true) {
							$('#priceQuote').html(data.price + ' ' + data.message);
							$('#inCart').show();
							$('#inCart').button();
							$('#checkAvail').hide();
						} else if (data.success == 'not_supported') {
							$('#priceQuote').html(data.price);
						} else {
							alert('<?php echo sysLanguage::get('PPR_NOTICE_RESERVATION_NOT_AVAILABLE'); ?>.');
						}
						hideAjaxLoader($this);
					}
				});
			}
		});

		$('#rental_qty').blur(function () {
			showAjaxLoader($('#datePicker'), 'xlarge');
			$.ajax({
				cache: false,
				dataType: 'json',
				type: 'post',
				url: js_catalog_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
				data: 'action=getReservedDates&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),
				success: function (data) {
					if (data.success == true) {
						bookedDates = data.bookedDates;
						shippingDaysPadding = data.shippingDaysPadding;
						shippingDaysArray = data.shippingDaysArray;
						disabledDatesPadding = data.disabledDatesPadding;
						disabledDays = data.disabledDays;
						if ($('#selected_period')) {
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

		$('#selected_period').change(function() {
			if ($(this).val() != '') {
				var selectedPeriod = $(this);
				var startDateString = $("#selected_period option:selected").attr('start_date');
				var endDateString = $("#selected_period option:selected").attr('end_date');
				$('#start_date').val(startDateString.substr(0, startDateString.length - 9));
				$('#end_date').val(endDateString.substr(0, endDateString.length - 9));
				//var price = $("#selected_period option:selected").attr('price');
				showAjaxLoader(selectedPeriod, 'xlarge');
				$.ajax({
					cache: false,
					dataType: 'json',
					type: 'post',
					url: js_catalog_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
					data: 'action=checkRes&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),//+'&price='+price,//isSemester=1&
					success: function (data) {
						if (data.success == true) {
							$('#priceQuote').html(data.price + ' ' + data.message);
							$('#inCart').show();
							$('#inCart').button();
							//$('#checkAvail').hide();
						} else if (data.success == 'not_supported') {
							$('#priceQuote').html(data.price);
						} else {
							alert('<?php echo sysLanguage::get('PPR_NOTICE_RESERVATION_NOT_AVAILABLE'); ?>.');
						}
						hideAjaxLoader(selectedPeriod);
					}
				});
			} else {
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
				if (isTimeStart == false) {
					isTimeStart = true;
					if (selectedStartTimeTd != null) {
						selectedStartTimeTd.data('element').remove();
					}
					if (selectedEndTimeTd != null) {
						selectedEndTimeTd.data('element').remove();
					}
					selectedStartTimeTd = $(this);
					selectedEndTime = null;
					selectedEndTimeTd = null;

					selectedStartTime = new Date(date);
					$el = $('<span></span>').html('Selected Start Time');
					$el.css('background-color', 'red');
					$el.css('color', 'white');
					selectedStartTimeTd.find('div').first().remove();
					selectedStartTimeTd.append($el);
					selectedStartTimeTd.data('element', $el);

					if (selectedStartTime.getDate() < 10) {
						today_day = '0' + selectedStartTime.getDate();
					} else {
						today_day = selectedStartTime.getDate();
					}

					if (selectedStartTime.getMonth() < 10) {
						today_month = '0' + (selectedStartTime.getMonth() + 1);
					} else {
						today_month = selectedStartTime.getMonth() + 1;
					}

					$('#start_date').val(today_month + '/' + today_day + '/' + selectedStartTime.getFullYear() + ' ' + selectedStartTime.getHours() + ':' + selectedStartTime.getMinutes() + ':00');
				} else {
					if (selectedStartTime < new Date(date)) {

						var allowSelectionTime = true;
						for (var k = 0; k < bookedTimesArr.length; k++) {
							//bDateArr = bookedDates[k].split('-');
							//bDate = new Date(parseInt(bDateArr[0]),parseInt(bDateArr[1])-1, parseInt(bDateArr[2]));
							if (selectedStartTime.getTime() <= bookedTimesArr[k].getTime() && date.getTime() >= bookedTimesArr[k].getTime()) {
								allowSelectionTime = false;
							}
						}
						var allowSelectionMinTime = true;
						if ((date.getTime() - selectedStartTime.getTime()) < ((minRentalPeriod))) {
							allowSelectionMinTime = false;
						}
						var allowSelectionMaxTime = true;
						if (((date.getTime() - selectedStartTime.getTime()) > (maxRentalPeriod)) && maxRentalPeriod != -1) {
							alert(date.getTime() - selectedStartTime.getTime());
							allowSelectionMaxTime = false;
						}


						//end check here
						if (allowSelectionMinTime == false) {
							alert(minRentalPeriodMessage);
							return false;
						}
						if (allowSelectionMaxTime == false) {
							alert(maxRentalPeriodMessage);
							return false;
						}
						if (allowSelectionTime == false) {
							alert('<?php echo sysLanguage::get('PPR_ERR_RESERVATION_BETWEEN'); ?>');
							return false;
						}

						isTimeStart = false;
						selectedEndTimeTd = $(this);
						selectedEndTime = new Date(date);
						$el = $('<span></span>').html('Selected End Time');
						$el.css('background-color', 'red');
						$el.css('color', 'white');
						selectedEndTimeTd.find('div').first().remove();
						selectedEndTimeTd.append($el);
						selectedEndTimeTd.data('element', $el);

						if (selectedEndTime.getDate() < 10) {
							today_day = '0' + selectedEndTime.getDate();
						} else {
							today_day = selectedEndTime.getDate();
						}

						if (selectedEndTime.getMonth() < 10) {
							today_month = '0' + (selectedEndTime.getMonth() + 1);
						} else {
							today_month = selectedEndTime.getMonth() + 1;
						}

						$('#end_date').val(today_month + '/' + today_day + '/' + selectedEndTime.getFullYear() + ' ' + selectedEndTime.getHours() + ':' + selectedEndTime.getMinutes() + ':00');


						var $this = $('#datePicker');

						showAjaxLoader($this, 'xlarge');
						$.ajax({
							cache: false,
							dataType: 'json',
							type: 'post',
							url: js_catalog_app_link('rType=ajax&appExt=payPerRentals&app=build_reservation&appPage=default'),
							data: 'action=checkRes&pID=' + productsID + '&' + $('.reservationTable *, .ui-widget-footer-box *').serialize(),
							success: function (data) {
								if (data.success == true) {
									$('#priceQuote').html(data.price + ' ' + data.message);
									$('#inCart').show();
									$('#inCart').button();
									//$('#checkAvail').hide();
								} else if (data.success == 'not_supported') {
									$('#priceQuote').html(data.price);
								} else {
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
   		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_USE_UPS_RESERVATION') == 'True') {
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

        $('#semRow').hide();

        $('input[name="cal_or_semester"]').change(function(){
			if($(this).val() == '1'){
				$('#dateRow').show();
				$('#semRow').hide();
				$('#dateSelectedCalendar').show();
				$('#selected_period').attr('name','sem');
			}else{
				$('#dateRow').hide();
				$('#dateSelectedCalendar').hide();
				$('#semRow').show();
				$('#selected_period').attr('name','semester_name');
			}
        });

        $('#calendarTime').hide();
	});
	</script>
	<style>
		.ui-datepicker-group {
			margin: .5em;
		}

		.ui-datepicker-header {
			padding: 0;
			text-align: center;
		}

		.ui-datepicker-header span {
			margin: .5em;
		}

		.ui-datepicker .ui-datepicker-prev, .ui-datepicker .ui-datepicker-next {
			top: 0px;
		}

		.fc-event-time {
			display: none !important;
		}

		.fc-event {
			width: 460px !important;
		}

		.ui-datepicker-status {
			margin: .5em;
			text-align: center;
			font-weight: bold;
		}

		.fc-minor {

		}

		#calendarTime {
			width: 540px;
		}

		.fc-agenda-body td.ui-state-default {
			cursor: pointer;
		}

		#datePicker {
		}

		.ui-datepicker {
			display: block;
		}

			/*#datePicker { font-size: 1.25em; }
			#datePicker .ui-datepicker-calendar td { font-size: 1.25em; }
			#datePicker .ui-datepicker-start_date { background: #00FF00; }*/
		.ui-datepicker-shipping-day-hover, .ui-datepicker-shipping-day-hover-info {
			background: #F7C8D3;
		}

		#datePicker .ui-state-active {
			background: #CACEE6;
		}
	</style>
	<table class="reservationTable">
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
			->where('products_id=?', $productsId)
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
		</table>
			<?php
   		$calendar = ob_get_contents();
		ob_end_clean();
		return $calendar;
	}

	public static function getMaxShippingDays($productId, $start, $allowOverbooking = false){

		$maxDays = 0;
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_ALLOW_OVERBOOKING') == 'False' && $allowOverbooking === false){

			$Qcheck = Doctrine_Query::create()
			->select('MAX(shipping_days_before) as max_before, MAX(shipping_days_after) as max_after')
			->from('OrdersProductsReservation opr')
			->leftJoin('opr.ProductsInventoryBarcodes ib')
			->leftJoin('ib.ProductsInventory i')
			->where('i.products_id = ?', $productId)
			->andWhereIn('opr.rental_state', array('reserved', 'out'))
			->andWhere('opr.parent_id IS NULL')
			->andWhere('DATE_ADD(end_date, INTERVAL shipping_days_after DAY) >= ?', $start)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			if($Qcheck[0]['max_before'] > $Qcheck[0]['max_after']){
				$maxDays = $Qcheck[0]['max_before'];
			}else{
				$maxDays = $Qcheck[0]['max_after'];
			}
		}
		return $maxDays;
	}

	public static function getMyReservations($productId, $start, $allowOverbooking = false){

		$reservArr = array();
		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_ALLOW_OVERBOOKING') == 'False' && $allowOverbooking === false){

			$Qcheck = Doctrine_Query::create()
			->from('OrdersProductsReservation opr')
			->leftJoin('opr.ProductsInventoryBarcodes ib')
			->leftJoin('ib.ProductsInventory i')
			->where('i.products_id = ?', $productId)
			->andWhereIn('opr.rental_state', array('reserved', 'out'))
			->andWhere('opr.parent_id IS NULL')
			->andWhere('DATE_ADD(end_date, INTERVAL shipping_days_after DAY) >= ?', $start)
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

			foreach($Qcheck as $iReservation){
					$reservationArr = array();

					$startDateArr = date_parse($iReservation['start_date']);
					$endDateArr = date_parse($iReservation['end_date']);

					$startTime = mktime($startDateArr['hour'],$startDateArr['minute'],$startDateArr['second'],$startDateArr['month'],$startDateArr['day']-$iReservation['shipping_days_before'],$startDateArr['year']);
					$endTime = mktime($endDateArr['hour'],$endDateArr['minute'],$endDateArr['second'],$endDateArr['month'],$endDateArr['day']+$iReservation['shipping_days_after'],$endDateArr['year']);

					$dateStart = date('Y-n-j', $startTime);
					$timeStart = date('G:i', $startTime);

					$dateEnd = date('Y-n-j', $endTime);
					$timeEnd = date('G:i', $endTime);

					if($timeStart == '0:00'){
						$reservationArr['start'] = $dateStart;
					}else{
						$reservationArr['start_time'] = $timeStart;
						$reservationArr['start_date'] = $dateStart;
						$reservationArr['end_time'] = '23:59';
						$reservationArr['end_date'] = $dateStart;
						$nextStartTime = strtotime('+1 day', strtotime($dateStart));
						$prevEndTime = strtotime('-1 day', strtotime($dateEnd));
						if( $nextStartTime <= $prevEndTime){
							$reservationArr['start'] = date('Y-n-j', $nextStartTime);
						}
					}

					if($timeEnd == '0:00'){
						$reservationArr['end'] = $dateEnd;
					}else{
						if(!isset($reservationArr['start_time'])){
							$reservationArr['start_time'] = '0:00';
						}
						$reservationArr['start_date'] = $dateEnd;
						$reservationArr['end_time'] = $timeEnd;
						$reservationArr['end_date'] = $dateEnd;
						$nextStartTime = strtotime('+1 day', strtotime($dateStart));
						$prevEndTime = strtotime('-1 day', strtotime($dateEnd));
						if( $nextStartTime <= $prevEndTime){
							$reservationArr['end'] = date('Y-n-j', $prevEndTime);
						}
					}

				    $reservationArr['barcode'] = $iReservation['barcode_id'];//if barcode_id is null or 0 this means is quantity and check will be made with the total qty at some point.
					$reservationArr['qty'] = 1;

					$reservArr[] = $reservationArr;
			}
		}

		return $reservArr;
	}
   /*
	public static function getReservations($productId, $start, $end, $allowOverbooking = false){
		$booked = array();

		if (sysConfig::get('EXTENSION_PAY_PER_RENTALS_ALLOW_OVERBOOKING') == 'False' && $allowOverbooking === false){
			$Qcheck = Doctrine_Query::create()
			->from('OrdersProductsReservation opr')
			->leftJoin('opr.ProductsInventoryBarcodes ib')
			->leftJoin('ib.ProductsInventory i')
			->where('i.products_id = ?', $productId)
			->andWhereIn('opr.rental_state', array('reserved', 'out'))
			->andWhere('opr.parent_id IS NULL')

			->andWhere('(
				(
					(
						start_date
							BETWEEN
								DATE_SUB(CAST("' . $start . '" AS DATETIME), INTERVAL shipping_days_before DAY)
									AND
								DATE_ADD(CAST("' . $end . '" AS DATETIME), INTERVAL shipping_days_after DAY)
					) AND TRUE
				) OR (
					(
						end_date
							BETWEEN
								DATE_SUB(CAST("' . $start . '" AS DATETIME), INTERVAL shipping_days_before DAY)
									AND
								DATE_ADD(CAST("' . $end . '" AS DATETIME), INTERVAL shipping_days_after DAY)
					) AND TRUE
				) OR (
					(
						DATE_SUB(CAST("' .  $start . '" AS DATETIME), INTERVAL shipping_days_before DAY) >= start_date
							AND
						DATE_ADD(CAST("' . $end . '" AS DATETIME), INTERVAL shipping_days_after DAY) <= end_date
					) AND TRUE
				) AND TRUE
			) AND TRUE')
			->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			//if date to check is 05.07 -- 08.07 and one reservation date from db is 04.07 -- 09.07
			if ($Qcheck){
				foreach($Qcheck as $oprInfo){
					$startDateArr = date_parse($oprInfo['start_date']);
					$endDateArr = date_parse($oprInfo['end_date']);
//print_r($opInfo);
//print_r($oprInfo);
					$startTime = mktime($startDateArr['hour'],$startDateArr['minute'],$startDateArr['second'],$startDateArr['month'],$startDateArr['day']-$oprInfo['shipping_days_before'],$startDateArr['year']);
					$endTime = mktime($endDateArr['hour'],$endDateArr['minute'],$endDateArr['second'],$endDateArr['month'],$endDateArr['day']+$oprInfo['shipping_days_after'],$endDateArr['year']);

					$days = ($endTime - $startTime) / (60 * 60 * 24);
					$date = date('Y-n-j', $startTime);

					if (tep_not_null($oprInfo['barcode_id'])){
						self::addBookedBarcode($booked, array(
							'date'      => $date,
							'barcodeID' => $oprInfo['barcode_id'],
							'startTime' => $startTime,
							'days'      => $days
						));
					}elseif (tep_not_null($oprInfo['quantity_id'])){
						self::addBookedQuantity($booked, array(
							'date'       => $date,
							'quantityID' => $oprInfo['quantity_id'],
							'startTime'  => $startTime,
							'days'       => $days
						));
					}

					$Qpackaged = Doctrine_Query::create()
					->leftJoin('OrdersProductsReservation opr')
					->where('opr.parent_id = ?', $oprInfo['orders_products_reservations_id'])
					->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
					if ($Qpackaged){
						foreach($Qpackaged as $opprInfo){
							if (tep_not_null($opprInfo['barcode_id'])){
								self::addBookedBarcode($booked, array(
									'date'      => $date,
									'barcodeID' => $opprInfo['barcode_id'],
									'startTime' => $startTime,
									'days'      => $days
								));
							}elseif (tep_not_null($opprInfo['quantity_id'])){
								self::addBookedQuantity($booked, array(
									'date'       => $date,
									'quantityID' => $opprInfo['quantity_id'],
									'startTime'  => $startTime,
									'days'       => $days
								));
							}
						}
					}
				}
			}
		}
		//print_r($booked);
		return $booked;
	}

	private static function addBookedBarcode(&$booked, $dataArray){
		$date = $dataArray['date'];
		$barcodeID = $dataArray['barcodeID'];
		if (!isset($booked['barcode'][$date])){
			$booked['barcode'][$date] = array($barcodeID);
		}else{
			if (!in_array($barcodeID, $booked['barcode'][$date])){
				$booked['barcode'][$date][] = $barcodeID;
			}
		}

		for($i=0; $i<$dataArray['days']; $i++){
			$date = date('Y-n-j', ($dataArray['startTime'] + (($i+1) * 86400)));
			if (!isset($booked['barcode'][$date])){
				$booked['barcode'][$date] = array($barcodeID);
			}elseif (isset($booked['barcode'][$date]) && !in_array($barcodeID, $booked['barcode'][$date])){
				$booked['barcode'][$date][] = $barcodeID;
			}
		}
	}

	private static function addBookedQuantity(&$booked, $dataArray){
		$date = $dataArray['date'];
		$quantityID = $dataArray['quantityID'];
		if (!isset($booked['quantity'][$date])){
			$booked['quantity'][$date][$quantityID] = 1;
		}else{
			if (!isset($booked['quantity'][$date][$quantityID])){
				$booked['quantity'][$date][$quantityID] = 1;
			}else{
				$booked['quantity'][$date][$quantityID] += 1;
			}
		}

		for($i=0; $i<$dataArray['days']; $i++){
			$date = date('Y-n-j', ($dataArray['startTime'] + (($i+1) * 86400)));
			if (!isset($booked['quantity'][$date][$quantityID])){
				$booked['quantity'][$date][$quantityID] = 1;
			}else{
				$booked['quantity'][$date][$quantityID] += 1;
			}
		}
	}
    */
	public static function CheckBooking($settings){
		$returnVal = 0;
		if(isset($settings['start_date']) && isset($settings['end_date'])){
			$Qcheck = Doctrine_Query::create();

			if ($settings['item_type'] == 'barcode'){
				$Qcheck->select('barcode_id');
			}else{
				$Qcheck->select('quantity_id');
			}

			$Qcheck->from('OrdersProductsReservation');

			if ($settings['item_type'] == 'barcode'){
				$Qcheck->where('barcode_id = ?', $settings['item_id']);
			}else{
				$Qcheck->where('quantity_id = ?', $settings['item_id']);
			}

			$Qcheck->andWhere('
					(
						(
							(CAST("' . date('Y-m-d H:i:s', $settings['start_date']) . '" as DATETIME)
								between
									DATE_SUB(CAST(start_date as DATETIME), INTERVAL shipping_days_before DAY)
										AND
									DATE_ADD(CAST(end_date as DATETIME), INTERVAL shipping_days_after DAY)
							)
						AND TRUE)
								OR
						(
							(CAST("' . date('Y-m-d H:i:s', $settings['end_date']) . '" as DATETIME)
								between
									DATE_SUB(CAST(start_date as DATETIME), INTERVAL shipping_days_before DAY)
										AND
									DATE_ADD(CAST(end_date as DATETIME), INTERVAL shipping_days_after DAY)
							)
						AND TRUE)
								OR
						(
							(
							CAST("' . date('Y-m-d H:i:s', $settings['start_date']) . '" as DATETIME) <= DATE_SUB(CAST(start_date as DATETIME), INTERVAL shipping_days_before DAY)
								AND
							CAST("' . date('Y-m-d H:i:s', $settings['end_date']) . '" as DATETIME) >= DATE_ADD(CAST(end_date as DATETIME), INTERVAL shipping_days_after DAY)
							)
						AND TRUE)
					AND TRUE)
				AND TRUE');

			if ($settings['item_type'] == 'barcode'){
				$Qcheck->andWhere('(rental_state = "reserved" or rental_state = "out")');
			}else{
				$Qcheck->andWhere('rental_state = ?', 'out');
			}
			//echo 'ddd'. $Qcheck->getSqlQuery();
			EventManager::notify('ReservationCheckQueryBeforeExecute', &$Qcheck, $settings);

			$Result = $Qcheck->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
			$returnVal = ($Result ? sizeof($Result) : 0);

			EventManager::notify('ReservationCheckQueryAfterExecute', &$Result, $settings, &$returnVal);
		}
		return $returnVal;
	}

	public static function returnReservation($bID, $status, $comment, $lost, $broken){
		global $appExtension, $messageStack;
		
		$Qcheck = Doctrine_Query::create()
		->select('orders_products_id')
		->from('OrdersProductsReservation')
		->where('orders_products_reservations_id = ?', $bID)
		->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

		if (isset($Qcheck[0]['orders_products_id']) && is_null($Qcheck[0]['orders_products_id']) === false){
			$ReservationQuery = Doctrine_Query::create()
			->from('Orders o')
			->leftJoin('o.Customers c')
			->leftJoin('o.OrdersAddresses oa')
			->leftJoin('o.OrdersProducts op')
			->leftJoin('op.OrdersProductsReservation opr')
			->where('opr.orders_products_reservations_id = ?', $bID)
			->andWhere('oa.address_type = ?', 'customer')
			->andWhere('parent_id IS NULL');
		}else{
			$ReservationQuery = Doctrine_Query::create()
			->from('OrdersProductsReservation opr')
			->where('opr.orders_products_reservations_id = ?', $bID);
		}
		
		$ReservationQuery->leftJoin('opr.ProductsInventoryBarcodes ib')
		->leftJoin('ib.ProductsInventory ibi')
		->leftJoin('opr.ProductsInventoryQuantity iq')
		->leftJoin('iq.ProductsInventory iqi');
		
		if ($appExtension->isInstalled('inventoryCenters') && $appExtension->isEnabled('inventoryCenters')){
			$extInventoryCenters = $appExtension->getExtension('inventoryCenters');
			if ($extInventoryCenters->stockMethod == 'Store'){
				$ReservationQuery->leftJoin('ib.ProductsInventoryBarcodesToStores b2s')
				->leftJoin('b2s.Stores');
			}else{
				$ReservationQuery->leftJoin('ib.ProductsInventoryBarcodesToInventoryCenters b2c')
				->leftJoin('b2c.ProductsInventoryCenters');
			}
		}
		
		$Reservation = $ReservationQuery->execute();
		foreach($Reservation as $oInfo){
			if (isset($oInfo->OrdersProducts)){
				$Products = $oInfo->OrdersProducts;
				$sendEmail = true;
			}else{
				$Products = $oInfo;
				$sendEmail = false;
			}
			foreach($Products as $pInfo){
				if (isset($pInfo->OrdersProductsReservation)){
					$Reservations = $pInfo->OrdersProductsReservation;
				}else{
					$Reservations = array($pInfo);
				}
				foreach($Reservations as $oprInfo){
					$reservationId = $oprInfo->orders_products_reservations_id;
					$trackMethod = $oprInfo->track_method;

					$oprInfo->rental_state = 'returned';
					$oprInfo->date_returned = date('Y-m-d h:i:s');
					$oprInfo->broken = $broken;
					//$oprInfo->lost = $lost;

					if (!empty($comment)){
						if ($reservationId == 'barcode'){
							$oprInfo->ProductsInventoryBarcodes->ProductsInventoryBarcodesComments[]->comments = $comment;
						}elseif ($reservationId == 'quantity'){
							$oprInfo->ProductsInventoryQuantity->ProductsInventoryQuantitysComments[]->comments = $comment;
						}
					}

					if (isset($extInventoryCenters)){
						$invCenterChanged = false;
						if (isset($_POST['inventory_center'][$reservationId])){
							$invCenter = $_POST['inventory_center'][$reservationId];
							if ($trackMethod == 'barcode'){
								if ($extInventoryCenters->stockMethod == 'Store'){
									$Barcode = $oprInfo->ProductsInventoryBarcodes->ProductsInventoryBarcodesToStores;
									if ($Barcode->inventory_store_id != $invCenter){
										$Barcode->inventory_store_id = $invCenter;
										$invCenterChanged = true;
									}
								}else{
									$Barcode = $oprInfo->ProductsInventoryBarcodes->ProductsInventoryBarcodesToInventoryCenters;
									if ($Barcode->inventory_center_id != $invCenter){
										$Barcode->inventory_center_id = $invCenter;
										$invCenterChanged = true;
									}
								}
							}elseif ($trackMethod == 'quantity'){
								$Quantity = $oprInfo->ProductsInventoryQuantity;
								if ($extInventoryCenters->stockMethod == 'Store'){
									if ($Quantity->inventory_store_id != $invCenter){
										$Qupdate = Doctrine_Query::create()
										->update('ProductsInventoryQuantity')
										->where('inventory_store_id = ?', $invCenter)
										->andWhere('inventory_id = ?', $Quantity->inventory_id);
										if ($status == 'B' || $status == 'L'){
											$Qupdate->set('broken = broken+1');
										}else{
											$Qupdate->set('available = available+1');
										}
										$Qupdate->execute();
										$invCenterChanged = true;
									}
								}else{
									if ($Quantity->inventory_center_id != $invCenter){
										$Qupdate = Doctrine_Query::create()
										->update('ProductsInventoryQuantity')
										->where('inventory_center_id = ?', $invCenter)
										->andWhere('inventory_id = ?', $Quantity->inventory_id);
										if ($status == 'B' || $status == 'L'){
											$Qupdate->set('broken = broken+1');
										}else{
											$Qupdate->set('available = available+1');
										}
										$Qupdate->execute();
										$invCenterChanged = true;
									}
								}
							}
						}
					}else{
						if ($trackMethod == 'barcode'){
							$oprInfo->ProductsInventoryBarcodes->status = $status;
						}elseif ($trackMethod == 'quantity'){
							$oprInfo->ProductsInventoryQuantity->qty_out--;
							if ($status == 'B' || $status == 'L'){
								$oprInfo->ProductsInventoryQuantity->broken++;
							}else{
								$oprInfo->ProductsInventoryQuantity->available++;
							}
						}
					}

					if ($sendEmail === true){
						$emailEvent = new emailEvent('reservation_returned', $oInfo->Customers->language_id);
						if (date('Y-m-d h:i:s') > $oprInfo->end_date){
							$dateArr = date_parse($oprInfo->end_date);
							$days_late = (mktime(0, 0, 0) - mktime(0, 0, 0, $dateArr['month'], $dateArr['day'], $dateArr['year'])) / (60 * 60 * 24);
						}else{
							$days_late = 0;
						}
						$emailEvent->setVars(array(
							'days_late' => $days_late,
							'full_name' => $oInfo->OrdersAddresses['customer']->entry_name,
							'email_address' => $oInfo->customers_email_address,
							'rented_product' => $pInfo->products_name
						));

						$emailEvent->sendEmail(array(
							'email' => $oInfo->customers_email_address,
							'name' => $oInfo->OrdersAddresses['customer']->entry_name
						));
					}
				}
			}
		}
		$Reservation->save();
	}
}
?>