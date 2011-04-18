
$(document).ready(function (){

	$('#product_list').css({'display':'block','float':'left'});
	$('#barcode_list').css({'display':'block','float':'left'});
	$('.maind').css({'border-bottom':'1px solid #000000'});
	$('#product_list').change(function(){

						var self = $(this);
						showAjaxLoader(self,'xlarge');
                        $.ajax({
							 type: "post",
							 url: js_app_link('appExt=payPerRentals&app=show_reports&appPage=default&action=barcode_list'),
							 data: "rType=ajax&pID="+self.val(),
							 success: function(data) {

								if(typeof data.data != undefined){									
									$('#barcode_list').html(data.data);
								}
								hideAjaxLoader(self);
							}});	
	});

	/*$('#start_date').css({'display':'block'});
	$('#end_date').css({'display':'block'});
	$('#cnt_provider').css({'display':'block'});
	$('#start_date').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true
	});

	$('#end_date').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true
	});*/

});