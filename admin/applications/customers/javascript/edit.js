
$(document).ready(function (){

/*$('#customers_dob').datepicker({
		dateFormat: 'mm/dd/yy',
		yearRange:'1920:2010',
		changeYear:true
	});
*/	
    $('select[name="activate"]').live('change',function (){
        fnClicked();
    });
	$('input[name="make_member"]').live('change',function (){
		if($('input[name="make_member"]').is(':checked') == true) {
			$('select[name="activate"]').removeAttr('disabled');
			$('select[name="activate"]').val('Y');
		} else {
			$('select[name="activate"]').attr('disabled','disabled');
			$('select[name="activate"]').val('N');

		}
		fnClicked();
	});
	$('select[name="payment_method"]').trigger('change');
	$('select[name="activate"]').trigger('change');
}); 