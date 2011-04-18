(function ($){
	$.extend($.ui.pointOfSale.prototype, {
		showProductSelectWindow: function (el){
			var self = this;
			if (!self.productWindow){
				self.productWindow = $('#selectProductWindow');
				self.productWindow.dialog({
					width: 750,
					height: 550,
					draggable: true,
					resizable: true,
					modal: true,
					position: 'center',
					autoOpen: false,
					buttons: {
						'Add Product And Close Window': function (){
							$.ajax({
								url: self._getURL('order'),
								dataType: 'html',
								type: 'post',
								data: 'action=getProductRow&' + $('*', self.productWindow).serialize(),
								cache: false,
								success: function (html){
									self._addProductRows(html);
									self.productWindow.dialog('close');
								}
							});
						}
					}
				});

				self._setupProductSelectBox();
				self._setupProductSearchBox();
				self._setupAddMultipleButton();
			}

			if (self.productWindow.dialog('isOpen')) return false;

			self.productWindow.dialog('open');
		},

		_setupProductSelectBox: function (){
			var self = this,
			windowElement = self.productWindow;

			self.productSelectBox = $('#products', windowElement).change(function (){
				$('#end_date, #start_date', $('#productsAttribs', windowElement)).datepicker('destroy');
				$('#productsAttribs', windowElement).html('Searching For Attributes....');
				$('#calanders', windowElement).hide();

				var pID = $(this).val();
				$.ajax({
					url: self._getURL('order'),
					dataType: 'json',
					type: 'get',
					data: 'action=getProductsAttribs&products_id=' + pID,
					cache: false,
					success: function (data){
						$('#productsAttribs', windowElement).html(data.tableHtml);

						self.reservation = new $.reservationProduct({
							startDate: '#start_date',
							endDate: '#end_date',
							quantity: '#qty',
							pageLink: self._getURL('order'),
							productsID: pID,
							endDateOnSelect: function (){
								$.ajax({
									url: self._getURL('order'),
									dataType: 'json',
									type: 'post',
									data: 'action=getProductsInfo&products_id=' + pID + '&type=' + $('#purchaseType', windowElement).val() + '&start_date=' + $('#start_date', windowElement).val() + '&end_date=' + $('#end_date', windowElement).val() + '&rental_qty=' + $('#qty', windowElement).val() + '&rental_shipping=' + $('input[name="rental_shipping"]:checked').val(),
									cache: false,
									success: function (data){
										$('#pricing', $('#productsAttribs', windowElement)).html(data.productPrice);
									}
								});
							}
						});

						$('#purchaseType', windowElement).change(function (){
							var purchaseType = $(this).val();
							if (purchaseType == 'reservation'){
								$('.reservationRow', $('#productsAttribs', windowElement)).show();
							}else{
								$('.reservationRow', $('#productsAttribs', windowElement)).hide();
							}

							$.ajax({
								url: self._getURL('order'),
								dataType: 'json',
								type: 'post',
								data: 'action=getProductsInfo&products_id=' + pID + '&type=' + purchaseType,
								cache: false,
								success: function (data){
									var $newMenu = $(data.barcodeHtml);
									$('#barcodes', $('#productsAttribs', windowElement)).replaceWith($newMenu);
									$('#pricing', $('#productsAttribs', windowElement)).html(data.productPrice);
								}
							});
						});

						$('#purchaseType').trigger('change');
					}
				});
			});
		},

		_setupProductSearchBox: function (){
			var self = this,
			windowElement = self.productWindow,
			productsArr = [];

			$(':option', self.productSelectBox).each(function (){
				var obj = {
					id: $(this).val(),
					text: $(this).html()
				};
				productsArr.push(obj);
			});

			$('#productSearch', windowElement).autocomplete(productsArr, {
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

			$('#productSearch').result(function (event, data, formatted){
				self.productSelectBox.selectOptions(data.id, true).trigger('change');
			});
		},

		_setupAddMultipleButton: function (){
			return;
			var self = this,
			windowElement = self.productWindow;

			$('#selectProductWindow_addMultiple', windowElement).click(function (){
				var urlVars = $('#form_selectProductTable', windowElement).serialize();
				urlVars = urlVars + '&country_id=' + $('#billingCountry').val();
				urlVars = urlVars + '&zone_id=' + $('#billingZone').val();

				$.ajax({
					url: self._getURL('order'),
					dataType: 'html',
					type: 'get',
					data: 'action=getProductRow&' + urlVars,
					cache: false,
					success: function (html){
						addProductRows(html);
						getOrderTotals();
					}
				});
			});
		}
	});
})(jQuery);