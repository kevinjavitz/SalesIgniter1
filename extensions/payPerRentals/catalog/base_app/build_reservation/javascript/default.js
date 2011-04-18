function showOverlay($elem){
		var selfLeft = $elem.position().left;
		var selfTop = $elem.position().top;
		var selfWidth = $elem.outerWidth(true);
		var selfHeight = $elem.outerHeight(true);
		var $overlay = $('<div></div>').addClass('ui-widget-overlay').css({
			position: 'absolute',
			width: selfWidth,
			height: selfHeight,
			left: selfLeft,
			top: selfTop,
            opacity:0.7,
			zIndex: 2
		});
		$overlay.insertAfter($elem);
		$elem.data('overlay', $overlay);
}

function removeOverlay($elem){
	$elem.data('overlay').remove();
	//$elem.removeData('overlay');
}

$(document).ready(function (){
    $('#semRow').hide();
    //$('#hourRow').hide();
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
    /*$('input[name="hour_or_day"]').each(function(){
       if($(this).val() == '1'){
            $(this).attr('checked','checked');
       }
    });*/

    $('#calendarTime').hide();
    //$('#start_time_input').attr('readonly','readonly');
    //$('#end_time_input').attr('readonly','readonly');
   /*$('input[name="hour_or_day"]').change(function(){
        if($(this).val() == '1'){
            $('#dateRow').show();
            $('#hourRow').hide();
            $('#calendarTime').hide();
            $('#dateSelectedCalendar').show();
            isHour = false;
        }else{
            //$('#dateRow').hide();
            //$('#dateSelectedCalendar').hide();
            isHour = true;
            $('#hourRow').show();
            $('#calendarTime').show();
        }
    });*/

	$('#getQuotes').click(function(){
		 showAjaxLoader($('#getQuotes'), 'xlarge');
		 $('#shipMethods').hide();
         $.ajax({
			 	cache:false,
                url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=getUpsQuotes&products_id='+$('#pid').val()+'&qty='+$('#rental_qty').val()),
                type: 'post',
			 	data: 'rental_qty='+$('#rental_qty').val()+'&street_address='+$('#street_address').val() + '&state='+$('#state').val() +'&city='+$('#city').val() +'&postcode1='+$('#postcode1').val() +'&postcode2='+$('#postcode2').val() +'&country='+$('#countryDrop').val() + '&iszip=' + $('#zipAddress').is(":visible"),
                dataType: 'json',
                success: function (data) {

					removeAjaxLoader($('#getQuotes'));
					if(data.success == true){
						if (data.nr == 0){
							$('#zipAddress').hide();
							$('#fullAddress').show();
							removeOverlay($('#datePicker'));
							showOverlay($('#datePicker'));
                            $('#datePicker').datepick('option', 'initStatus', '');
						} else{
							$('#shipMethods').show();
							$('#rowquotes').html(data.html);
							$('#zipAddress').show();
							$('#fullAddress').hide();
							if ($('input[name=rental_shipping]').size() > 0){
								$('input[name=rental_shipping]').each(function (){
										$(this).trigger('click');
								});
							}
                            $('#datePicker').datepick('option', 'initStatus', 'Please select a start date');
							removeOverlay($('#datePicker'));
						}
					}
					//foreach data.quotesid make them visible
					//the same for data.quotescosts
                }
         });
	});

	$('#countryDrop').change(function (){
		var $stateColumn = $('#stateCol');
		showAjaxLoader($stateColumn, 'icon', 'append');
		$.ajax({
			cache: true,
			url: js_app_link('appExt=payPerRentals&app=build_reservation&appPage=default&action=getCountryZones'),
			data: 'cID=' + $(this).val(),
			dataType: 'html',
			success: function (data){
				removeAjaxLoader($stateColumn);
				$('#stateCol').html(data);
			}
		})
	});
	$('#countryDrop').val('223').trigger('change');
	$('#fullAddress').hide();
	$('#shipMethods').hide();
	$('#zipAddress').show();
	/*if ($('#zipAddress').val() != ''){
		$('#getQuotes').trigger('click');
	}*/
});