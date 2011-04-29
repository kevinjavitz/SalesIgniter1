
$(document).ready(function (){

	$('#tab_container').tabs();
	var xhr;
	$('#customerList').css('border','1px solid #000000');
	$('#searchingCustomerList').css('border','1px solid #000000');

	$('#customerList ul').css('list-style','none');
	$('#searchCustomer').keyup(function(){
		var $button = $('#searchingCustomerList');
		if(xhr != null){
			xhr.abort();
		}else{
			showAjaxLoader($button,'small');
		}
        xhr = $.ajax({
				cache: false,
				dataType: 'json',
	            type:'post',
				url: js_app_link('appExt=customerGroups&app=manage&appPage=new&action=getCustomers&cName='+$(this).val()),
	            data:$('#customerList *').serialize(),
				success: function (data){
					removeAjaxLoader($button);
					xhr = null;
					$('#searchingCustomerList').html(data.customers);
					$('#searchingCustomerList ul').css('list-style','none');
				}
		});
	});
	$('#addToList').click(function(){
		$('.searchCustomer:checked').each(function(){
			//$('#customerList ul').append($(this).parent());
			$inpElem =$(this).parent().find('input[name="searchCustomer[]"]');
			$newElem = '<input type="hidden" name="selectedCustomer[]" class="selectedCustomer" value="'+$inpElem.val()+'">';
			$(this).parent().remove().append('<span class="ui-icon ui-icon-minusthick removeCustomer"></span>').append($newElem).appendTo($('#customerList ul'));
			$inpElem.remove();
			$('.removeCustomer').click(function(){
				$(this).parent().remove();
			});
		});

	});
	$('.removeCustomer').click(function(){
				$(this).parent().remove();
	});

});