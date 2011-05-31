$(document).ready(function (){
	$('#select_page').change(function(){
		$('#form_page').submit();	
	});
	$('#limit').change(function(){
		$('#search').submit();
	});
	$('.deleteProductButton').click(function (){
		var $selfButton = $(this);
		$('<div></div>').dialog({
			autoOpen: true,
			width: 300,
			modal: true,
			resizable: false,
			allowClose: false,
			title: 'Delete Product Confirm',
			open: function (e){
				$(e.target).html('Are you sure you want to delete this product?');
			},
			close: function (){
				$(this).dialog('destroy');
			},
			buttons: {
				'Delete Product': function(){
					window.location = js_app_link('app=products&appPage=default&action=deleteProductConfirm&products_id=' + $selfButton.attr('products_id'));
				},
				'Don\'t Delete': function(){
					$(this).dialog('destroy');
				}
			}
		});
		return false;
	});
	
	$('.setExpander').click(function (){
		if ($(this).hasClass('ui-icon-triangle-1-s')){
			$(this).removeClass('ui-icon-triangle-1-s').addClass('ui-icon-triangle-1-e');
			$('tr[box_id=' + $(this).parent().parent().attr('infobox_id') + ']').hide();
		}else{
			$(this).removeClass('ui-icon-triangle-1-e').addClass('ui-icon-triangle-1-s');
			$('tr[box_id=' + $(this).parent().parent().attr('infobox_id') + ']').show();
		}
	});
});