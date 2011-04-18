function showAjaxLoader(){
	$('#ajaxLoader').dialog({
		modal: true,
		resizable: false,
		draggable: false,
		position: 'center'
	}).show();
}

function removeAjaxLoader(){
	$('#ajaxLoader').dialog('close');
}
function updateRes(){
	$.ajax({
		cache: false,
		dataType: 'html',
		data: {
			start_date: $('#start_date').val(),
			end_date: $('#end_date').val()
		},
		beforeSend: showAjaxLoader,
		complete: removeAjaxLoader,
		url: js_app_link('appExt=payPerRentals&app=send&appPage=default&action=getReservations'),
		success: function (data){
			$('tbody', $('#reservationsTable')).html(data);
		}
	});
}

function submitRes(){

	$.ajax({
		cache: false,
		dataType: 'json',
		data: $('#reservationsTable *').serialize(),
        type:'post',
		beforeSend: showAjaxLoader,
		complete: removeAjaxLoader,
		url: js_app_link('appExt=payPerRentals&app=send&appPage=default&action=sendReservations'),
		success: function (data){
			$('.reservations', $('#reservationsTable')).each(function (){
				if (this.checked){
					$(this).parent().parent().remove();
				}
			});
		}
	});
}

var dayShortNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

$(document).ready(function (){
	updateRes();
	$('#get_res').click(updateRes);
	$('#send').click(submitRes);

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