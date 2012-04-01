$(document).ready(function (){
	$('#select_page').change(function(){
		$('#form_page').submit();	
	});
	$('#limit').change(function(){
		$('#search').submit();
	});
	$('.selectallProducts').click(function(){
		var self = this;

		$('.selectedProducts').each(function (){
			this.checked = self.checked;
		});

		if (self.checked){
			$('.selectAllProductsText').html('Uncheck All Products');
		}else{
			$('.selectAllProductsText').html('Check All Products');
		}
	});
	$('.deleteMultipleProducts').click(function(){
		var $selfButton = $(this);
		$('<div></div>').dialog({
			autoOpen: true,
			width: 300,
			modal: true,
			resizable: false,
			allowClose: false,
			title: 'Delete Products Confirm',
			open: function (e){
				$(e.target).html('Are you sure you want to delete the selected products');
			},
			close: function (){
				$(this).dialog('destroy');
			},
			buttons: {
				'Delete Products': function(){
					$.ajax({
						cache: false,
						url: js_app_link('app=products&appPage=default&action=deleteMultipleProductConfirm'),
						data:$('.gridContainer *').serialize(),
						type:'post',
						dataType: 'json',
						success: function (data){
							js_redirect(js_app_link('app=products&appPage=default'));
						}
					});
				},
				'Don\'t Delete': function(){
					$(this).dialog('destroy');
				}
			}
		});
		return false;
	});
	$('.copyButton').click(function(){
		window.location = js_app_link('app=products&appPage=default&action=copyProduct&products_id=' + $(this).attr('products_id'));
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