(function($) {
	$.extend($.ui.pointOfSale.prototype, {
		showOrderTotalWindow: function (el){
			if ($(el).hasClass('ui-button')){
				var windowAction = 'new';
			}else{
				var windowAction = 'edit';
			}
			$('<div></div>').dialog({
				autoOpen: true,
				title: 'Order Total',
				open: function (e, ui){
					$('.ui-dialog-content', ui.element).html('<div class="ui-ajax-loader ui-ajax-loader-xlarge" style="margin-left:auto;margin-right:auto;"></div>');
					$.ajax({
						cache: false,
						url: js_href_link(thisFile, 'windowAction=' + windowAction + '&action=getOrderTotalWindow'),
						dataType: 'html',
						success: function (data){
							$('.ui-dialog-content', ui.element).html(data);
						}
					});
				},
				buttons: {
					'Save': function (){
						var self = $(this);
						$.ajax({
							cache: false,
							url: js_href_link(thisFile, 'action=saveOrderTotal'),
							data: $('.ui-dialog-content *', self.element).serialize(),
							type: 'post',
							dataType: 'html',
							success: function (data){
								$('#orderTotalsList').append(data);
								self.dialog('destroy');
							}
						});
					},
					'Cancel': function (){
						$(this).dialog('destroy');
					}
				}
			});
		}
	});
})(jQuery);