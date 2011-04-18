$(document).ready(function (){
	$('#continueButton').hide();
	$('<button><span>Validate Address</span></button>').insertAfter($('#continueButton')).click(function (){
		var $button = $(this);
		$.ajax({
			url: js_app_link('app=checkout&appPage=default&action=getCassAddresses'),
			cache: false,
			dataType: 'json',
			type: 'post',
			data: $('form[name=checkout]').find('input, select').serialize(),
			success: function (data){
				if (data.success){
					$.each(data.addresses, function (k, v){
						$.each(v, function(inputName, inputVal){
							$('input[name=' + inputName + '], select[name=' + inputName + ']').val(inputVal);
						})
					});
					$button.remove();
					$('#continueButton').show();
				}else{
					alert(data.addresses);
				}
			}
		});
	}).button();
});