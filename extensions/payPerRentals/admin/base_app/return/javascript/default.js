var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
$(document).ready(function (){
	$('#DP_startDate').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true,
		altField: '#start_date',
		dayNamesMin: dayShortNames
	});

	$('#DP_endDate').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true,
		altField: '#end_date',
		dayNamesMin: dayShortNames
	});
});
