$(document).ready(function(){
	$('.attrSelect').live('change', function(){
		var self= $(this).closest('.attributesTable');
		var selfParent = self.parent();
		showAjaxLoader(self, 'xlarge');
		$.ajax({
			cache: false,
			dataType: 'json',
			data:self.find('*').serialize(),
			type:'post',
			url: js_app_link('app=product&appPage=default&action=getAttributes&pID=' + self.parent().attr('pID')+'&purchase_type=' + self.parent().attr('purchase_type')),
			success: function (data){
				removeAjaxLoader(self);
				self.parent().html(data.html);
				if(data.hasButton == false){
					selfParent.closest('form').find('.ui-dialog-buttonpane').hide();
				}else{
					selfParent.closest('form').find('.ui-dialog-buttonpane').show();
				}
			}
		});
	});
	$('.attrSelect').change();
});