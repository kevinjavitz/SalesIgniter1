$(document).ready(function (){

	$('.addonsPopup').live('click', function(){

		var $Row = $(this).parentsUntil('tbody').last();

		var addCartData = {
			id: $Row.attr('data-id'),
			pID: $Row.attr('data-product_id'),
			qty: $Row.find('.productQty').val()
		};
		$.ajax({
			cache: false,
			dataType: 'json',
			type:'post',
			data: addCartData,
			url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=getAddons'),
			success: function (data) {
				var $dialog = $('<div></div>').dialog({
					title: 'Select Addon Products',
					width: 600,
					height: 561,
					open: function (){
						$(this).html(data.addonProducts);
					},

					buttons: {
						'Add Addons': function (){
							var self = this;

								showAjaxLoader($(self).parent(), 'large');

								$.ajax({
									cache: false,
									dataType: 'json',
									type:'post',
									data: $(self).find('.myAddons *').serialize()+'&id='+$Row.attr('data-id')+'&pID='+$Row.attr('data-product_id'),
									url: js_app_link('appExt=orderCreator&app=default&appPage=new&action=saveResInfoAddons'),
									success: function (postResp) {
										$('.productTable tbody').append(postResp.html);
										for(i=0;i<postResp.idarr.length;i++){
											var myPurchasetype = $('select[name="'+postResp.idarr[i]+'"]');
											myPurchasetype.val(postResp.purchaseType[i]);
											myPurchasetype.trigger('change');
										}
										$Row.parent().find('')
										removeAjaxLoader($(self).parent());
										$dialog.dialog('destroy').remove();
									}
								});
						}
					}

				});

			}
		})
	});

});