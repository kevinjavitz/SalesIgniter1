$(document).ready(function (){
	$('#genList').click(function (){
		var getVars = $('#start_date, #end_date, #filter, #invCenter').serialize();
		$.ajax({
			cache: false,
			dataType: 'json',
			data: getVars,
			url: js_app_link('app=label_maker&appPage=default&action=getListing', 'SSL'),
			success: function (data){
				if (data){
					var listingData = data.listingData;
					$('#reservations > tbody').empty();
					if (typeof listingData == 'object'){
						$(listingData).each(function (i){
							var $row = $('<tr>').appendTo('#reservations > tbody');
							$('<td>').addClass('main').append('<input type="checkbox" name="print[]" class="rowBox" value="' + listingData[i][7] + '">').appendTo($row);
							$('<td>').addClass('main').append(listingData[i][0]).appendTo($row);
							for (var col=1; col<7; col++){
								$('<td>').addClass('main').append(listingData[i][col]).appendTo($row);
							}
						});
					}else{
						var $row = $('<tr>').appendTo('#reservations > tbody');
						$('<td colspan="8">').addClass('main').append(listingData).appendTo($row);
					}
				}
			}
		});
	});

	/*$('#genLabels').click(function (){
		if ($('input[name="print[]"]:checked').size() > 0){
			var checked = new Array();
			$('input[name="print[]"]:checked').each(function (){
				checked.push($(this).val());
			});
			window.open(js_app_link('app=label_maker&appPage=default&action=printLabels&checked=' + checked.join(',') + '&labelType=' + $('#label_type').val() + '&invCenter=' + $('#invCenter').val()));
		}else{
			alert('No rentals selected to generate labels.');
		}
	});*/
	
	$('#genLabels').labelPrinter({
		//labelTypes: ['8160-b'],
		printUrl : js_app_link('app=label_maker&appPage=default&action=printLabels'),
		getData : function () {
			return $('input[name="print[]"]:checked').serialize();
		},
		beforeShow : function () {
			if ($('input[name="print[]"]:checked').size() <= 0){
				alert('Please select barcodes to print using the checkboxes on the left of the table rows');
				return false;
			}
			return true;
		}
	});
	$('#DP_startDate').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true,
		altField: '#start_date'
	});

	$('#DP_endDate').datepicker({
		dateFormat: 'yy-mm-dd',
		gotoCurrent: true,
		altField: '#end_date'
	});

	$('.checkAll').click(function (){
		var main = this;
		$('.rowBox').each(function (){
			this.checked = main.checked;
		});
	});

	$('#printPage').click(function (){
		$('#headerMenu, .hideForPrint').hide();
		window.print();
		$('#headerMenu, .hideForPrint').show();
	});
});