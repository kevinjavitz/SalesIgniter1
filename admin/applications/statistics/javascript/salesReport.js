$(document).ready(function (){
	$('.checkAll').click(function (){
		var self = this;
		$(self).parent().find(':checkbox').each(function (){
			this.checked = self.checked;
		});
	});

	$('#date_from').datepicker({
		altField: 'input[name=date_from]',
		dateFormat: 'yy-mm-dd'
	});
	$('#date_to').datepicker({
		altField: 'input[name=date_to]',
		dateFormat: 'yy-mm-dd'
	});

	$('input[name=report_type]').click(function (){
		$('.reportTypeFilter').hide();
		$('.reportTypeFilter').find('input, select').attr('disabled', 'disabled');
		$('#' + $(this).val() + 'Filter').show();
		$('#' + $(this).val() + 'Filter').find('input, select').removeAttr('disabled');
	});

	$('#generate').click(function (){
		$.ajax({
			url: js_app_link('app=statistics&appPage=salesReport&action=genSalesReport'),
			cache: false,
			dataType: 'json',
			type: 'post',
			data: $('input, select').serialize(),
			success: function (data){
				$('.reportHolder').html(data.html);
			}
		});
	});

	$('#generateCsv').click(function (){
		$.ajax({
			url: js_app_link('app=statistics&appPage=salesReport&action=genSalesReport&csv=true'),
			cache: false,
			dataType: 'json',
			type: 'post',
			data: $('input, select').serialize(),
			success: function (data){
				js_redirect(data.redirectTo);
			}
		});
	});
});