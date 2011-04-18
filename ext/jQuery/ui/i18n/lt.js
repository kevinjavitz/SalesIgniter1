/* Lithuanian (UTF-8) Translations. */
/* @author Arturas Paleicikas <arturas@avalon.lt> */
jQuery(function($){
	var langCode = 'lt';
	var calSettings = {
		closeText: 'Uždaryti',
		prevText: '&#x3c;Atgal',
		nextText: 'Pirmyn&#x3e;',
		currentText: 'Šiandien',
		monthNames: ['Sausis','Vasaris','Kovas','Balandis','Gegužė','Birželis',
		'Liepa','Rugpjūtis','Rugsėjis','Spalis','Lapkritis','Gruodis'],
		monthNamesShort: ['Sau','Vas','Kov','Bal','Geg','Bir',
		'Lie','Rugp','Rugs','Spa','Lap','Gru'],
		dayNames: ['sekmadienis','pirmadienis','antradienis','trečiadienis','ketvirtadienis','penktadienis','šeštadienis'],
		dayNamesShort: ['sek','pir','ant','tre','ket','pen','šeš'],
		dayNamesMin: ['Se','Pr','An','Tr','Ke','Pe','Še'],
		weekHeader: 'Wk',
		dateFormat: 'yy-mm-dd',
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