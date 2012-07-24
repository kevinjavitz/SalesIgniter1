$(document).ready(function (){
	$('select[name=customers_inventory_center]').live('change', function (){
		var $self = $(this);
		showAjaxLoader($self, 'small');
		$.ajax({
			cache: false,
			url: js_app_link('rType=ajax&appExt=orderCreator&app=default&appPage=new&action=setOrdersInventory&id=' + $(this).val()),
			dataType: 'json',
			success: function (){
				removeAjaxLoader($self);
				if($('select[name=customers_inventory_lp]')){
					$('select[name=customers_inventory_lp]').trigger('change');
				}else{
				$('.purchaseType').trigger('updateInfo');
					showAjaxLoader($('.extraOCInfo'), 'xlarge');
					$.ajax({
						cache: false,
						dataType: 'json',
						data: $('.extraOCInfo *').serialize(),
						type: 'post',
						url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadExtraFields'),
						success: function (data){
							removeAjaxLoader($('.extraOCInfo'));
						}
					});
				}
			}
		});
	});
	$('select[name=customers_inventory_lp]').live('change', function (){
		var $self = $(this);
		showAjaxLoader($self, 'small');
		$.ajax({
			cache: false,
			url: js_app_link('rType=ajax&appExt=orderCreator&app=default&appPage=new&action=setOrdersLP&id=' + $(this).val()),
			dataType: 'json',
			success: function (data){
				removeAjaxLoader($self);
				if(data.selectedInventory != ''){
					$('select[name=customers_inventory_center]').val(data.selectedInventory);
				}
				$('.purchaseType').trigger('updateInfo');
				showAjaxLoader($('.extraOCInfo'), 'xlarge');
				$.ajax({
					cache: false,
					dataType: 'json',
					data: $('.extraOCInfo *').serialize(),
					type: 'post',
					url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=loadExtraFields'),
					success: function (data){
						removeAjaxLoader($('.extraOCInfo'));
					}
				});
			}
		});
	});
	$('select[name=customers_inventory_center]').trigger('change');
});