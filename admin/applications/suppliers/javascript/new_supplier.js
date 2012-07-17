
$(document).ready(function (){

	$('#page-2').tabs();
	$('#pricingTabs').tabs();
	$('#inventory_tab_normal_tabs').tabs();
	$('#inventory_tab_attribute_tabs').tabs();
	$('#inventory_tabs').tabs();
	$('#tabs_packages').tabs();
	$('#tab_container').tabs();
	
	makeTabsVertical('#tab_container');

    $('.makeFCK').each(function (){
        $(this).data('editorInstance', CKEDITOR.replace(this, {
            filebrowserBrowseUrl: DIR_WS_ADMIN + 'rentalwysiwyg/editor/filemanager/browser/default/browser.php'
        }));
    });

	$('.ajaxSave').click(function (){
		showAjaxLoader($(document.body), 'xlarge');
		
		$('.makeFCK').each(function (){
			if ($(this).data('editorInstance')){
				var ckEditor = $(this).data('editorInstance');
				
				$(this).val(ckEditor.getData());
			}
		});
		
		$.ajax({
			cache: false,
			type: 'post',
			url: js_app_link('app=suppliers&appPage=new_supplier&action=saveSupplier&rType=ajax' + (supplierID > 0 ? '&sID=' + supplierID : '')),
			data: $('form[name="new_supplier"]').serialize(),
			dataType: 'json',
			success: function (data){
				$('.programDisabled').removeAttr('disabled').removeClass('ui-state-disabled').removeClass('programDisabled');
				supplierID = data.sID;
				
				var $form = $('form[name=new_supplier]');
				if ($('input[name=supplier_id]', $form).size() <= 0){
					$('<input type="hidden"></input>').attr('name', 'supplier_id').val(supplierID).appendTo($form);
				}else{
					$('input[name=supplier_id]', $form).val(supplierID);
				}
				
				$('#newsupplierMessage').remove();
				hideAjaxLoader($(document.body));
			}
		});
		return false;
	});
	
	$('.ui-state-disabled').each(function (){
		$('input', this).each(function (){
			if (!$(this).attr('disabled')){
				$(this).attr('disabled', 'disabled').addClass('programDisabled');
			}
		});
		$('.ui-button', this).addClass('ui-state-disabled').addClass('programDisabled');
		$(this).addClass('programDisabled');
	});
	

});

