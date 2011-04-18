$(document).ready(function (){
	$('.netPricing_discount').keyup(function (){
		var taxRate = getTaxRate();
		var grossValue = $(this).val();

		if (taxRate > 0) {
			grossValue = grossValue * ((taxRate / 100) + 1);
		}
		$('input[id=' + $(this).attr('id') + '_gross]').val(doRound(grossValue, 4));
	});

	$('.grossPricing_discount').keyup(function (){
		var taxRate = getTaxRate();
		var netValue = $(this).val();

		if (taxRate > 0) {
			netValue = netValue / ((taxRate / 100) + 1);
		}
		var name = $(this).attr('id');
		$('input[id=' + name.replace('_gross', '') + ']').val(doRound(netValue, 4));
	});

	$('#tax_class_id').change(function (){
		$('.netPricing_discount').trigger('keyup');
	}).trigger('change');
});