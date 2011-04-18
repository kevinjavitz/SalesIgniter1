	$(document).ready(function (){
		$('.barcodeMenu').change(function (){
			$('#barcodeImage', $(this).parent().parent()).html('<img src="showBarcode.php?code=' + $(this).val() + '">');
		});
	
		$('.barcodeMenu').each(function (){
			$(this).trigger('change');
		});
	});