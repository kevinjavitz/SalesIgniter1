$(document).ready(function (){
	$('input[name="dfrom"]').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true
	});
	$('input[name="dto"]').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true
	});
});