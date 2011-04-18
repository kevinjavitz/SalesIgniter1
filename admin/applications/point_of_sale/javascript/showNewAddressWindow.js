(function($) {
	$.extend($.ui.pointOfSale.prototype, {
		showNewAddressWindow: function (el){
			var self = this;
			if (!self.addressWindow){
				self.addressWindow = $('#newAddressWindow').dialog({
					width: ($(window).width() * .75),
					height: ($(window).height() * .5),
					draggable: true,
					resizable: true,
					stack: false,
					modal: true,
					position: 'center',
					autoOpen: false,
					buttons: {
						'Verify Service Availability': function (e){
							showAjaxLoader($(e.target), 'small');
							$.ajax({
								url: self._getURL('order'),
								dataType: 'json',
								type: 'post',
								data: 'action=verifyService&' + $('*', self.addressWindow).serialize(),
								cache: false,
								success: function (data){
									if (data.inService == true){
										alert('This pick up address is in an available service area');
									}else{
										alert('This customer is not in an available service area');
									}
									hideAjaxLoader($(e.target));
								}
							});
						},
						'Save': function (){
							var urlVars = $('*', self.addressWindow.element).serialize();

							$.ajax({
								url: self._getURL('order'),
								dataType: 'html',
								type: 'get',
								data: 'action=saveCustomerAddress&' + urlVars,
								cache: false,
								success: function (data){
									self._addAddress(data);
									self.addressWindow.dialog('close');
								}
							});
						},
						'Cancel': function (){
							self.addressWindow.dialog('close');
						}
					}
				});
			}

			if (self.addressWindow.dialog('isOpen')) return false;

			$.ajax({
				url: self._getURL('orders'),
				dataType: 'html',
				data: 'action=getAddressInsert',
				type: 'get',
				cache: false,
				success: function (data){
					$(self.addressWindow).html(data);
					self.addressWindow.dialog('open');
				}
			});
		}
	});
})(jQuery);