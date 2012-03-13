$(document).ready(function (){
	var linkParams = js_get_all_get_params(['app', 'appPage', 'action']);
	if($('.productListingColBoxContent_image').size() > 0){
		$(".productListingColBoxContent_image").each(function() {
			var $thisImage = $(this);
			if(!$('#desc' + $(this).attr('pID')).html()){
				$.ajax({
					url: js_app_link(linkParams + 'rType=ajax&app=products&appPage=all&action=getTooltipWindow&products_id=' + $thisImage.attr('pID')),
					data: 'pID=' + $thisImage.attr('pID'),
					type: 'post',
					dataType: 'json',
					success: function (data) {
						$('body').append('<div id="'+'desc' + $thisImage.attr('pID')+'" style="display:none">'+data.pageHtml+'</div>');
						//$('#desc' + $(this).attr('pID')).html(data.pageHtml);
						try{
							$thisImage.mopTip({'w':350,'style':"overClick",'get':"#desc" + $thisImage.attr('pID')});
						}
						catch(err){

						}
					}
				});
			}

		});
	}
	$('.myAddons').hide();
	$('.myAddons').each(function(){
		$(this).clone().appendTo($(this).next());
		$(this).remove();
	});
	$('.inCart').click(function(){
		var self = $(this);
		if(self.parent().parent().find('.myAddons .myAddonsInner .priceOptions').size() > 0){
			$( '<div id="dialog-mesage" title="Recommended Addons"></div>' ).dialog({
				modal: false,
				autoOpen: true,
				open: function (e, ui){
					//alert(self.parent().attr('name'));
					$('#dialog-mesage').html(self.parent().parent().find('.myAddons').html());
				},
				buttons: {
					Submit: function() {
						var myForm = self.closest('form');
						myForm.find('.myAddons').append($('#dialog-mesage .myAddonsInner').clone());
						$(this).dialog("destroy");
						myForm.find('.priceOptions').remove();
						myForm.append('<input type="hidden" name="add_reservation_product"/>');
						myForm.submit();

					}
				}
			});
		}else{
			return true;
		}

		return false;
	});
});