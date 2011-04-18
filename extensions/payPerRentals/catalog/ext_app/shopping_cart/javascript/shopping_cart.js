$(document).ready(function (){
	$('.startDate').datepicker({
		minDate: '+1',
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true,
		onSelect: function (dateText){
			var dateObj = $.datepicker.parseDate('yy-mm-dd', dateText);
			var longDate = $.datepicker.formatDate('D, dd MM yy', dateObj);
			$('.startDateLong').html(longDate);
			
			dateObj.setDate(parseFloat(dateObj.getDate())+1);
			$('.endDate').datepicker('option', 'minDate', dateObj);
			//alert(dateObj);
		}
	});
	
	$('.endDate').datepicker({
		minDate: '+1',
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true,
		onSelect: function (dateText){
			var dateObj = $.datepicker.parseDate('yy-mm-dd', dateText);
			var longDate = $.datepicker.formatDate('D, dd MM yy', dateObj);
			$('.endDateLong').html(longDate);
		}
	});
});