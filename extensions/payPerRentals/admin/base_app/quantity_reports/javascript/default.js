	function showToolTipAttr(settings){
		var $toolTip = $('<div></div>')
		.addClass('ui-widget')
		.addClass('ui-widget-content')
		.addClass('ui-corner-all')
		.css({
			position: 'absolute',
			left: settings.offsetLeft,
			top: settings.offsetTop ,
			zIndex: 9999,
			padding: '5px'
		})
		.html(settings.tipText)
		.appendTo($('body'));
		$toolTip.css('width', '300px');
		if ((parseInt($toolTip.css('left'), 10) + 300) >= $(window).width()){

			$toolTip.css('left', (parseInt($toolTip.css('left'), 10) - 300));
		}
		$toolTip.css('top', parseInt($toolTip.css('top'), 10) - $toolTip.height());
		return $toolTip;
	}

// Read a page's GET URL variables and return them as an associative array.
	function getUrlVarsLocal(){
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1);
		if (window.location.href.indexOf('?') >= 0){
			return '&' + hashes;
		}else{
			return '';
		}
	}

function popupWindowEditReservation(type, rID, barcode_id, selectedBarcodes, products_id, w, h) {
	$('<div id="editWindow"></div>').dialog({
		autoOpen: true,
		width: w,
		height: h,
		close: function (e, ui){
			$('#editWindowForm').appendTo($('body'));
			$('#editWindowForm').css({
				'display':'none'
			});
			$(this).dialog('destroy').remove();
		},
		open: function (e, ui){
			$('#editWindowForm').css({
				'display':'block'
			});
			$('#editWindowForm').appendTo($(e.target));

			showAjaxLoader($('#editWindow'), 'xlarge');

			$.ajax({
				cache: false,
				url: $('#editWindowForm').attr('action') + '?action=getReservation' + '&type=' + type + '&rID=' + rID,
				dataType: 'json',
				success: function (data){
					hideAjaxLoader($('#editWindow'));
					if(data.success == true){
						if(data.type == 'reservation'){
							$('#date_added_edit').hide();
							$('#return_date_edit').hide();
							$("label[for=date_added_edit]").hide();
							$("label[for=return_date_edit]").hide();
							$("label[for=customers_edit]").hide();
							$("label[for=start_date_edit]").show();
							$("label[for=end_date_edit]").show();
							$('#customers_edit').hide();
							$('#start_date_edit').show();
							$('#end_date_edit').show();
							$('#start_date_edit').val(data.startDate);
							$('#end_date_edit').val(data.endDate);
							$('#rental_status_edit').val(data.status);
							$('#header_edit').html(data.header);
							$('#editWindow').dialog( "option" , 'title',data.title);
							$("label[for=rental_status_edit]").css({
											'margin-right':'45px'
							});
						}else{
							$('#date_added_edit').show();
							$('#return_date_edit').show();
							$('#customers_edit').show();
							$("label[for=date_added_edit]").show();
							$("label[for=return_date_edit]").show();
							$("label[for=customers_edit]").show();
							$("label[for=start_date_edit]").hide();
							$("label[for=end_date_edit]").hide();
							$('#start_date_edit').hide();
							$('#end_date_edit').hide();
							$('#date_added_edit').val(data.startDate);
							$('#return_date_edit').val(data.endDate);
							$('#rental_status_edit').val(data.status);
							$('#customers_edit').val(data.customer);							
							$('#header_edit').html(data.header);
							$('#editWindow').dialog( "option" , 'title',data.title);
							$("label[for=rental_status_edit]").css({
											'margin-right':'61px'
							});
						}

					}
				}
			});
		},
		buttons: {
				'Save': function() {
						dialog = $(this);
					    showAjaxLoader($('#editWindow'), 'xlarge');

						$.ajax({
							cache: false,
							url: $('#editWindowForm').attr('action') + '?action=editReservation' + '&type=' + type + '&rID=' + rID + '&barcode_id=' + barcode_id + '&products_id=' + products_id + '&selectedBarcodes[]=' + selectedBarcodes,
							data:$('#editWindowForm').serialize(),
							type: 'post',
							dataType: 'json',
							success: function (data){
								hideAjaxLoader($('#editWindow'));
								if (data.success == true){
                                    extlink = js_app_link('appExt=payPerRentals&app=reservations_reports&appPage=default' + getUrlVarsLocal());
                                    var lastPos = extlink.length-1;
                                    if (extlink.charAt(lastPos) == '?'){
                                       extlink = extlink.substring(0, lastPos);
                                    }
									js_redirect(extlink);
								}else{
									alert('An error occured');
								}
								dialog.dialog('close');
							}
						});
				},
				'Delete': function() {
						dialog = $(this);
					    showAjaxLoader($('#editWindow'), 'xlarge');
						$.ajax({
							cache: false,
							url: $('#editWindowForm').attr('action') + '?action=deleteReservation' + '&type=' + type + '&rID=' + rID + '&barcode_id=' + barcode_id,
							data:$('#editWindowForm').serialize(),
							type: 'post',
							dataType: 'json',
							success: function (data){
								hideAjaxLoader($('#editWindow'));
								if (data.success == true){
								    extlink = js_app_link('appExt=payPerRentals&app=reservations_reports&appPage=default' + getUrlVarsLocal());
				                                    var lastPos = extlink.length-1;
				                                    if (extlink.charAt(lastPos) == '?'){
				                                       extlink = extlink.substring(0, lastPos);
				                                    }
									js_redirect(extlink);
								}else{
									alert('An error occured');
								}
								dialog.dialog('close');
							}
						});
					$(this).dialog('close');
				},
				'Cancel': function() {					
					$(this).dialog('close');
				}
			}
	});
	return false;
}


	$(document).ready(function (){
		$('#editWindowForm').css({
			'display':'none'
		});
		$('#start_date').datepicker({
					dateFormat: 'yy-mm-dd',
					gotoCurrent: true
		});

		$('.barcodeName').autocomplete({
				source: js_app_link('appExt=payPerRentals&app=reservations_reports&appPage=default&action=getBarcodes'),
				minLength: 1,
				select: function(event, ui) {
					$('.barcodeName').val(ui.item.label);
					$('#searchFormReports').submit();
					return true;
				}
		});

        $('.prodName').autocomplete({
            source: js_app_link('appExt=payPerRentals&app=reservations_reports&appPage=default&action=getProducts'),
            minLength: 1,
            select: function(event, ui) {
                $('.prodName').val(ui.item.label);
                $('#searchFormReports').submit();
                return true;
            }
        });

        $('#start_date').change(function(){
            var d = new Date($(this).val());
            //var curDate = $('#calendarTime').fullCalendar('gotoDate',);

            //var valInc = parseInt((d.getTime()-curDate.getTime())/(1000*24*60*60));

            if(viewType == 'hour'){
                $('.fc-agenda-body table').tableTranspose();
                $('#calendarTime').fullCalendar( 'gotoDate', d.getFullYear(), d.getMonth(), d. getDate());
                //$('#calendarTime').fullCalendar( 'changeView','agendaDay');
              //  $('#calendarTime').fullCalendar( 'next',valInc );

                $('.fc-agenda-body table').tableTranspose();
                //$('.fc-agenda-body table').css('table-layout','fixed');

                $('.fc-agenda-body').height($('#prodTable').height()-50);
                //$('.fc-agenda-body').height($('.fc-agenda-body').height()*ratio);
                $('.fc-agenda-body table th:first').css('width','40px');
                $('.fc-agenda-body table tr:nth-child(2)').each(function(){
                    $(this).height($('#prodTable').height()-83);

                });
            }else{

                $('#calendarTime').fullCalendar( 'gotoDate', d.getFullYear(), d.getMonth(), d. getDate());
            }
            //$('#calendarTime').fullCalendar('rerenderEvents');
            //$('#calendarTime').fullCalendar( 'next' );
            //$('#calendarTime').fullCalendar( 'next' );
            //$('#calendarTime').fullCalendar( 'next' );

        });

        var datesReservation = $('#start_date_edit, #end_date_edit').datetimepicker({
            dateFormat: 'yy-mm-dd',
        	changeMonth: true,
        	onSelect: function(selectedDate) {
        	    var option = this.id == "start_date_edit" ? "minDate" : "maxDate";
        	    var instance = $(this).data("datepicker");
        		var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
        		if(this.id == "start_date_edit"){
                   		var days = "0";
				dateFut = new Date(date.setDate(date.getDate() + parseInt(days)));
	        		datesReservation.not(this).datepicker("option", option, dateFut);
			}
        }});

        var datesRental = $('#date_added_edit, #return_date_edit').datetimepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
        	onSelect: function(selectedDate) {
        		var option = this.id == "date_added_edit" ? "minDate" : "maxDate";
        		var instance = $(this).data("datepicker");
        		var date = $.datepicker.parseDate(instance.settings.dateFormat || $.datepicker._defaults.dateFormat, selectedDate, instance.settings);
        		if(this.id == "date_added_edit"){
        	        	var days = "0";
				dateFut = new Date(date.setDate(date.getDate() + parseInt(days)));
	        		datesRental.not(this).datepicker("option", option, dateFut);
			}
        }});

		$('#prevArrow, #nextArrow').css({'cursor':'pointer', 'padding':'8px'});
		$('#prevArrow').click(function(){
            prevDate = new Date($('#start_date').val());
			$('#start_date').val($(this).attr('prevData'));
            newDate = new Date($(this).attr('prevData'));
            newDate.setTime(newDate.getTime()-parseInt($('#numCols').val()*1000*24*60*60));
            if(newDate.getMonth()+1<10){
							tmonth = '0'+(newDate.getMonth()+1);
						}else{
							tmonth = (newDate.getMonth()+1);
						}

						if(newDate.getDate()<10){
							tday = '0'+(newDate.getDate());
						}else{
							tday = (newDate.getDate());
						}

            if(prevDate.getMonth()+1<10){
							tmonth1 = '0'+(prevDate.getMonth()+1);
						}else{
							tmonth1 = (prevDate.getMonth()+1);
						}

						if(prevDate.getDate()<10){
							tday1 = '0'+(prevDate.getDate());
						}else{
							tday1 = (prevDate.getDate());
						}

            $(this).attr('prevData',newDate.getFullYear()+'-'+tmonth+'-'+tday);
            $('#nextArrow').attr('nextData',prevDate.getFullYear()+'-'+tmonth1+'-'+tday1);
            $('#start_date').trigger('change');
            tableOffset = $("#calendarTime .fc-view-basicWeek table").offset().top;
            $header = $("#calendarTime .fc-view-basicWeek table > thead").clone();
            $fixedHeader = $("#header-fixed").empty().append($header);
			//$('#searchFormReports').submit();
			return false;
		});

        $('#limitProd, #purchType, #numCols, #limitInventory').change(function(){
            $('#searchFormReports').submit();
		    return false;
        });

		$('#nextArrow').click(function(){
            prevDate = new Date($('#start_date').val());
			$('#start_date').val($(this).attr('nextData'));
            newDate = new Date($(this).attr('nextData'));

            newDate.setTime(newDate.getTime()+parseInt($('#numCols').val()*1000*24*60*60));

            if(newDate.getMonth()+1<10){
							tmonth = '0'+(newDate.getMonth()+1);
						}else{
							tmonth = (newDate.getMonth()+1);
						}

						if(newDate.getDate()<10){
							tday = '0'+(newDate.getDate());
						}else{
							tday = (newDate.getDate());
						}

            if(prevDate.getMonth()+1<10){
							tmonth1 = '0'+(prevDate.getMonth()+1);
						}else{
							tmonth1 = (prevDate.getMonth()+1);
						}

						if(prevDate.getDate()<10){
							tday1 = '0'+(prevDate.getDate());
						}else{
							tday1 = (prevDate.getDate());
						}

            $(this).attr('nextData',newDate.getFullYear()+'-'+tmonth+'-'+tday);
            $('#prevArrow').attr('prevData',prevDate.getFullYear()+'-'+tmonth1+'-'+tday1);
            $('#start_date').trigger('change');
            tableOffset = $("#calendarTime .fc-view-basicWeek table").offset().top;
            $header = $("#calendarTime .fc-view-basicWeek table > thead").clone();
            $fixedHeader = $("#header-fixed").empty().append($header);
			//$('#searchFormReports').submit();
			return false;
		});

		$("label[for=date_added_edit]").css({
											'margin-right':'65px'
		});
		$("label[for=start_date_edit]").css({
											'margin-right':'65px'
		});
		$("label[for=end_date_edit]").css({
											'margin-right':'74px'
		});
		$("label[for=return_date_edit]").css({
											'margin-right':'60px'
		});
		$("label[for=rental_status_edit]").css({
											'margin-right':'61px'
		});
		$("label[for=customers_edit]").css({
											'margin-right':'82px'
		});
        $('#start_date').focus(function(){
            $('#ui-datepicker-div').css('z-index','1000');
        });

        /*new Reservations calendar*/

	});