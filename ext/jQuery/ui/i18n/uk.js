/* Ukrainian (UTF-8) Translations. */
/* Written by Maxim Drogobitskiy (maxdao@gmail.com). */
jQuery(function($){
	var langCode = 'uk';
	var calSettings = {
		closeText: 'Закрити',
		prevText: '&#x3c;',
		nextText: '&#x3e;',
		currentText: 'Сьогодні',
		monthNames: ['Січень','Лютий','Березень','Квітень','Травень','Червень',
		'Липень','Серпень','Вересень','Жовтень','Листопад','Грудень'],
		monthNamesShort: ['Січ','Лют','Бер','Кві','Тра','Чер',
		'Лип','Сер','Вер','Жов','Лис','Гру'],
		dayNames: ['неділя','понеділок','вівторок','середа','четвер','п’ятниця','субота'],
		dayNamesShort: ['нед','пнд','вів','срд','чтв','птн','сбт'],
		dayNamesMin: ['Нд','Пн','Вт','Ср','Чт','Пт','Сб'],
		weekHeader: 'Не',
		dateFormat: 'dd/mm/yy',
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