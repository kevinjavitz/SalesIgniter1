(function ($){
	$.extend($.ui.pointOfSale.prototype, {
		showProductEditWindow: function (el){
			var self = this;
			if (!self.productEditWindow){
				self.productEditWindow = $('<div>').attr('id', 'productEditWindow').attr('title', 'Edit Order Product');
				self.productEditWindow.dialog({
					width: ($(window).width() * .75),
					height: ($(window).height() * .5),
					draggable: true,
					resizable: true,
					modal: true,
					position: 'center',
					autoOpen: false,
					buttons: {
						'Update Product': function (){
							var urlParams = $('*', self.productEditWindow).serialize();
							$.ajax({
								url: self._getURL('order'),
								cache: false,
								data: 'action=updateProduct&' + urlParams,
								type: 'get',
								dataType: 'json',
								success: function (data){
									self._getOrdersProductsHTML();
									self.productEditWindow.dialog('close');
								}
							});
						},
						'Cancel': function (){
							self.productEditWindow.dialog('close');
						}
					}
				});
			}

			if (self.productEditWindow.dialog('isOpen')) return false;

			$.ajax({
				url: self._getURL('order'),
				cache: false,
				data: 'action=editProduct&pID=' + $(el).parent().parent().attr('id'),
				type: 'get',
				dataType: 'html',
				success: function (html){
					$(self.productEditWindow).html(html);
					self.productEditWindow.dialog('open');
				}
			});
		}
	});
})(jQuery);