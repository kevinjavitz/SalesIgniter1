(function($) {
	$.extend($.ui.pointOfSale.prototype, {
		showCustomerSelectWindow: function (el){
			var self = this;
			if (!self.customerWindow){
				self.customerWindow = $('#selectCustomerWindow');
				self.customerWindow.dialog({
					width: 750,
					height: 550,
					draggable: true,
					resizable: true,
					modal: true,
					position: 'center',
					autoOpen: false,
					buttons: {
						'Select Customer': {
							id: 'selectCustomerWindow_selectCustomerButton',
							click: function (e){
								showAjaxLoader($(e.target), 'small');
								if ($('option:selected', self.selectBox).size() <= 0){
									alert('Please select a customer first.');
									hideAjaxLoader($(e.target));
									return false;
								}

								var urlVars = $('#customers, :radio[name="sendTo"], :radio[name="billTo"], :radio[name="pickupFrom"]', self.customerWindow).serialize();

								$.ajax({
									url: self._getURL('order'),
									dataType: 'json',
									type: 'get',
									data: 'action=setOrdersCustomer&' + urlVars,
									cache: false,
									success: function (data){
										$(self._getButton('shippingAddressEdit'))
										.attr('address_type', 'delivery')
										.attr('address_id', data.shippingAddressID)
										.show();

										$(self._getButton('billingAddressEdit'))
										.attr('address_type', 'billing')
										.attr('address_id', data.billingAddressID)
										.show();

										$(self._getButton('pickupAddressEdit'))
										.attr('address_type', 'pickup')
										.attr('address_id', data.pickupAddressID)
										.show();

										$('#shippingAddressDialog').html(data.shippingAddressFormatted);
										$('#billingAddressDialog').html(data.billingAddressFormatted);
										$('#pickupAddressDialog').html(data.pickupAddressFormatted);

										if ($('tr', $('tbody', $('#productsTable'))).size() > 0){
											self._orderUpdated('customerUpdated');
										}else{
											$('.defaultText', $('#shippingMethodDialog')).html('Please Add A Product To The Order To Get Shipping Quotes');
											//self.showShippingMethods($('#shippingMethodDialog'));
										}

										hideAjaxLoader($(e.target));
										self.customerWindow.dialog('close');
									}
								});
							}
						},
						'New Address': {
							id: 'selectCustomerWindow_newAddressButton',
							click: function (e){
								showAjaxLoader($(e.target), 'small');
								if ($('option:selected', self.selectBox).size() <= 0){
									alert('Please select a customer first.');
									hideAjaxLoader($(e.target));
									return false;
								}
								self.showNewAddressWindow($('option:selected', self.selectBox));
								hideAjaxLoader($(e.target));
							}
						},
						'Add Customer': {
							id: 'selectCustomerWindow_addCustomerButton',
							hidden: true,
							click: function (e){
								showAjaxLoader($(e.target), 'small');
								$.ajax({
									url: self._getURL('order'),
									dataType: 'json',
									type: 'post',
									data: 'action=addNewCustomer&' + $('*', self.customerWindow).serialize(),
									cache: false,
									success: function (data){
										$(self.customerSelectBox).addOption(data.customerID, data.customerName, false).sortOptions();
										$(self.customerSelectBox).val(data.customerID).trigger('change');
										hideAjaxLoader($(e.target));
									}
								});
							}
						},
						'Verify Service Availability': {
							id: 'selectCustomerWindow_verifyServiceButton',
							hidden: true,
							click: function (e){
								showAjaxLoader($(e.target), 'small');
								$.ajax({
									url: self._getURL('order'),
									dataType: 'json',
									type: 'post',
									data: 'action=verifyService&' + $('*', self.customerWindow).serialize(),
									cache: false,
									success: function (data){
										if (data.inService == true){
											if ($('input[name="firstname"]:visible', self.customerWindow).size() > 0){
												$('#selectCustomerWindow_addCustomerButton').show();
												$('#selectCustomerWindow_verifyServiceButton').hide();
											}else{
												alert('This pick up address is in an available service area');
											}
										}else{
											alert('This customer is not in an available service area');
										}
										hideAjaxLoader($(e.target));
									}
								});
							}
						}
					}
				});

				self._setupCustomerSelectBox();
				self._setupCustomerSearchBox();
			}

			if (self.customerWindow.dialog('isOpen')) return false;

			self.customerSelectBox.trigger('unselect');
			self.customerWindow.dialog('open');
		},

		_setupCustomerSearchBox: function (){
			var self = this,
			windowElement = self.customerWindow,
			customerArr = [];

			$(':option', self.customerSelectBox).each(function (){
				var obj = {
					id: $(this).val(),
					text: $(this).html()
				};
				customerArr.push(obj);
			});

			$('#customerSearch', windowElement).autocomplete(customerArr, {
				formatItem: function(row, i, max) {
					return row.text;
				},
				formatMatch: function(row, i, max) {
					return row.text;
				},
				formatResult: function(row) {
					return row.text;
				}
			});

			$('#customerSearch').result(function (event, data, formatted){
				self.customerSelectBox.selectOptions(data.id, true).trigger('change');
			});
		},
		_setupCustomerSelectBox: function (){
			var self = this,
			windowElement = self.customerWindow;

			self.customerSelectBox = $('#customers', windowElement).change(function (){
				showAjaxLoader(windowElement.parent(), 'xlarge');

				var selectedVal = $(this).val();
				$.ajax({
					url: self._getURL('order'),
					dataType: 'html',
					type: 'get',
					data: 'action=getCustomerInfo&customers_id=' + selectedVal,
					cache: false,
					success: function (html){
						hideAjaxLoader(windowElement.parent());
						$('div[id="customerInfo"]', windowElement).html(html);

						if (selectedVal == 'new'){
							$('#selectCustomerWindow_selectCustomerButton, #selectCustomerWindow_newAddressButton, #selectCustomerWindow_addCustomerButton').hide();
							$('#selectCustomerWindow_verifyServiceButton').show();
							$().keyup(function (){
								if ($('#selectCustomerWindow_verifyServiceButton:visible').size() <= 0){
									$('#selectCustomerWindow_selectCustomerButton, #selectCustomerWindow_newAddressButton, #selectCustomerWindow_addCustomerButton').hide();
									$('#selectCustomerWindow_verifyServiceButton').show();
								}
							});
						}else{
							$('#selectCustomerWindow_selectCustomerButton, #selectCustomerWindow_newAddressButton, #selectCustomerWindow_verifyServiceButton').show();
							$('#selectCustomerWindow_addCustomerButton').hide();
						}
					}
				});
			}).bind('unselect', function (){
				$('#customerInfo', windowElement).html('');
				$('option:selected', this).each(function (){
					this.selected = false;
				});
				$('#selectCustomerWindow_selectCustomerButton, #selectCustomerWindow_newAddressButton').show();
				$('#selectCustomerWindow_addCustomerButton').hide();
			});
		},
		_addAddress: function (html){
			var self = this,
			windowElement = self.customerWindow;
			$('#addressesOnFile', windowElement).append(html);
		}
	});
})(jQuery);