$(document).ready(function (){
	$('#select_page').change(function(){
		$('#form_page').submit();	
	});
	$('#limit').change(function(){
		$('#search').submit();
	});
	$('.selectallSuppliers').click(function(){
		var self = this;

		$('.selectedSuppliers').each(function (){
			this.checked = self.checked;
		});

		if (self.checked){
			$('.selectAllSuppliersText').html('Uncheck All Suppliers');
		}else{
			$('.selectAllSuppliersText').html('Check All Suppliers');
		}
	});
	$('.deleteMultipleSuppliers').click(function(){
		var $selfButton = $(this);
		var checked = false;

        $('.selectedSuppliers').each(function (){
            if(this.checked)
                checked = true;
        });

        if(checked == true){
            $('<div></div>').dialog({
                autoOpen: true,
                width: 300,
                modal: true,
                resizable: false,
                allowClose: false,
                title: 'Delete Suppliers Confirm',
                open: function (e){
                    $(e.target).html('Are you sure you want to delete the selected Suppliers');
                },
                close: function (){
                    $(this).dialog('destroy');
                },
                buttons: {
                    'Delete Suppliers': function(){
                        $.ajax({
                            cache: false,
                            url: js_app_link('app=suppliers&appPage=default&action=deleteMultipleSupplierConfirm'),
                            data:$('.gridContainer *').serialize(),
                            type:'post',
                            dataType: 'json',
                            success: function (data){
                                js_redirect(js_app_link('app=suppliers&appPage=default'));
                            }
                        });
                    },
                    'Don\'t Delete': function(){
                        $(this).dialog('destroy');
                    }
                }
            });
        }
		return false;
	});
	$('.copyButton').click(function(){
		window.location = js_app_link('app=suppliers&appPage=default&action=copySupplier&Suppliers_id=' + $(this).attr('Suppliers_id'));
	});
	$('.deleteSupplierButton').click(function (){
		var $selfButton = $(this);
		$('<div></div>').dialog({
			autoOpen: true,
			width: 300,
			modal: true,
			resizable: false,
			allowClose: false,
			title: 'Delete Supplier Confirm',
			open: function (e){
				$(e.target).html('Are you sure you want to delete this Supplier?');
			},
			close: function (){
				$(this).dialog('destroy');
			},
			buttons: {
				'Delete Supplier': function(){
					window.location = js_app_link('app=suppliers&appPage=default&action=deleteSupplierConfirm&Suppliers_id=' + $selfButton.attr('Suppliers_id'));
				},
				'Don\'t Delete': function(){
					$(this).dialog('destroy');
				}
			}
		});
		return false;
	});

});