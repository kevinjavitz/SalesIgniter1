(function($) {
	$.extend($.ui.pointOfSale.prototype, {
		showEditAddressWindow: function (el){
			var self = this;
			var addressType = $(el).attr('address_type');
			//if (!self.addressEditWindow){
			self.addressEditWindow = $('<div></div>').attr('title', 'Edit Address');
			self.addressEditWindow.dialog({
				width: 750,
				height: 550,
				draggable: true,
				resizable: true,
				modal: true,
				position: 'center',
				autoOpen: false,
				close: function (){
					$(this).dialog('destroy');
				},
				buttons: {
					'Verify Service Availability': {
						id: 'editAddressWindow_verifyServiceButton',
						hidden: true,
						click: function (e){
							showAjaxLoader($(e.target), 'small');
							$.ajax({
								url: self._getURL('order'),
								dataType: 'json',
								type: 'post',
								data: 'action=verifyService&' + $('*', self.addressEditWindow).serialize(),
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
						}
					},
					'Save': {
						id: 'editAddressWindow_saveButton',
						click: function (){
							var urlVars = $('*', self.addressEditWindow).serialize();
							$.ajax({
								url: self._getURL('order'),
								dataType: 'html',
								type: 'get',
								data: 'action=saveCustomerAddress&order_address_key=' + addressType + '&' + urlVars,
								cache: false,
								success: function (data){
									if ($('input[name="updateAddressBook"]:checked', self.addressEditWindow).size() > 0){
										var addressId = $(el).attr('address_id');
										$('#billingAddressEdit, #shippingAddressEdit, #pickupAddressEdit').each(function (){
											if ($(this).attr('address_id') == addressId){
												$('.ui-inline-dialog-content', $(this).parent().parent()).html(data);
											}
										});
									}else{
										$('.ui-inline-dialog-content', $(el).parent().parent()).html(data);
									}
									self.addressEditWindow.dialog('destroy');
								}
							});
						}
					},
					'Cancel': {
						id: 'editAddressWindow_cancelButton',
						click: function (){
							self.addressEditWindow.dialog('destroy');
						}
					}
				}
			});
			// }

			if (self.addressEditWindow.dialog('isOpen')) return false;

			$.ajax({
				url: self._getURL('order'),
				dataType: 'html',
				data: 'action=getAddressEdit&address_book_id=' + $(el).attr('address_id') + '&address_type=' + $(el).attr('address_type'),
				type: 'get',
				cache: false,
				success: function (data){
					$(self.addressEditWindow).html(data);

					$(':radio', $(self.addressEditWindow)).click(function (){
						var action;
						var addressID;
						switch ($(el).attr('address_type')){
							case 'shipping':
							action = 'setSendTo';
							addressID = $('input[name="shipTo"]:checked', $(self.addressEditWindow)).val();
							break;
							case 'billing':
							action = 'setBillTo';
							addressID = $('input[name="billTo"]:checked', $(self.addressEditWindow)).val();
							break;
							case 'pickup':
							action = 'setPickupFrom';
							addressID = $('input[name="pickupFrom"]:checked', $(self.addressEditWindow)).val();
							break;
						}

						$.ajax({
							url: self._getURL('order'),
							dataType: 'html',
							data: 'action=' + action + '&address_book_id=' + addressID,
							type: 'get',
							cache: false,
							success: function (data){
								$('#addressEntryTable', $(self.addressEditWindow)).replaceWith(data);
							}
						});
					});

					self.addressEditWindow.dialog('open');
				}
			});
		}
	});
})(jQuery);