/* Russian (UTF-8) Translations. */
/* Written by Andrew Stromnov (stromnov@gmail.com). */
jQuery(function($){
	var langCode = 'ru';
	var calSettings = {
		closeText: 'Закрыть',
		prevText: '&#x3c;Пред',
		nextText: 'След&#x3e;',
		currentText: 'Сегодня',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
		'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
		'Июл','Авг','Сен','Окт','Ноя','Дек'],
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		weekHeader: 'Нед',
		dateFormat: 'dd.mm.yy',
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: ''
	};

	if ($.datepicker){
		$.datepicker.regional[langCode] = calSettings;
		$.datepicker.setDefaults($.datepicker.regional[langCode]);
	}

	if ($.datepick){
		$.datepick.regional[langCode] = calSettings;
		$.datepick.setDefaults($.datepick.regional[langCode]);
	}
});