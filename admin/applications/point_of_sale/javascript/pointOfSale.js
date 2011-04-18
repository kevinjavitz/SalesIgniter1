(function($) {

	$.widget("ui.pointOfSale", {
		_init: function() {
			var self = this,
			options = this.options;

			$(self._getButton('browseProducts')).click(function (){
				self.showProductSelectWindow(this);
			}).button();

			$(self._getButton('browseCustomers')).click(function (){
				self.showCustomerSelectWindow(this);
			}).button();

			$(self._getButton('applyPayment')).click(function (){
				self.showApplyPaymentWindow(this);
				return false;
			}).button();

			$(self._getButton('resetOrder')).click(function (){
				document.location = 'point_of_sale.php';
				return false;
			}).button();

			$(self._getButton('orderTotal')).click(function (){
				self.showOrderTotalWindow(this);
				return false;
			}).button();

			$(self._getButton('shippingAddressEdit') + ', ' + self._getButton('billingAddressEdit') + ', ' + self._getButton('pickupAddressEdit')).each(function (){
				$(this).click(function (){
					if ($(this).hasClass('ui-state-disabled')){
						return false;
					}
					self.showEditAddressWindow(this);
				});
			});

			$(self._getButton('commentsUpdate')).click(function (){
			}).button();
		},

		destroy: function() {
		},

		_setData: function(key, value) {
			$.widget.prototype._setData.apply(this, arguments);
		},

		_getURL: function (key){
			return this.options.urls[key];
		},

		_getButton: function (key){
			return this.options.buttons[key + 'Button'];
		},

		_addProductRow: function ($newRow){
			var self = this;
			var $productsTable = $('#productsTable');

			$('tbody', $productsTable).append($newRow);

			var $curRow = $('tr:last', $('tbody', $productsTable));
			var $firstCol = $('td:eq(0)', $curRow);

			$('#editProduct, #removeProduct', $firstCol).hover(function (){
				$(this).css('cursor', 'pointer');
			}, function (){
				$(this).css('cursor', 'default');
			});

			$('#removeProduct', $firstCol).click(function (){
				$.ajax({
					url: self._getURL('order'),
					cache: false,
					data: 'action=removeProduct&purchaseType=' + $curRow.attr('purchase_type') + '&pID=' + $curRow.attr('id'),
					type: 'get',
					dataType: 'html',
					success: function (html){
						self._addProductRows(html);
					}
				});
			});

			$('#editProduct', $firstCol).click(function (){
				self.showProductEditWindow(this);
			});
		},

		_addProductRows: function (newRows){
			var self = this;
			var $newRows = $(newRows);

			$('tbody', $('#productsTable')).empty();
			$('tr', $newRows).each(function (){
				self._addProductRow($(this));
			});
			self._orderUpdated('productsReloaded');
		},

		_getOrdersProductsHTML: function (){
			var self = this;
			$.ajax({
				url: this._getURL('order'),
				dataType: 'html',
				data: 'action=getProductRow&fromOrder=true',
				type: 'get',
				cache: false,
				success: function (html){
					self._addProductRows(html);
				}
			});
		},

		_orderUpdated: function (actionTaken){
			var self = this;
			switch(actionTaken){
				case 'productsReloaded':
				if ($('.defaultText', $('#billingAddressDialog')).size() <= 0){
					self.showShippingMethods($('#shippingMethodDialog'));
				}else{
					self.showOrderTotals($('#orderTotals'));
				}
				break;
				case 'customerUpdated':
				if ($('tr', $('tbody', $('#productsTable'))).size() > 0){
					self._getOrdersProductsHTML();
				}
				break;
				case 'paymentMethodSelected':
				case 'shippingMethodSelected':
				case 'shippingMethodsUpdated':
				self.showOrderTotals($('#orderTotals'));
				break;
			}
		}
	});

	$.extend($.ui.pointOfSale, {
		version: "0.1pre",
		defaults: {
			allowFunctionKeys: false,
			buttons: {
				browseCustomersButton: '#browseCustomers',
				quickCustomersButton: '#quickCustomers',
				browseProductsButton: '#browseProducts',
				quickProductsButton: '#quickProducts',
				orderTotalButton: '#orderTotalButton',
				resetOrderButton: '#resetButton',
				applyPaymentButton: '#applyPayment',
				shippingAddressEditButton: '#shippingAddressEdit',
				billingAddressEditButton: '#billingAddressEdit',
				pickupAddressEditButton: '#pickupAddressEdit',
				commentsUpdateButton: '#commentsUpdate'
			}
		}
	});

})(jQuery);