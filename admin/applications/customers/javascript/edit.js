
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
}); 