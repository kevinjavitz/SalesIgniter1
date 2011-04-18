(function($) {
	$.extend($.ui.pointOfSale.prototype, {
		showShippingMethods: function ($el, useCallBack){
			var self = this,
			useCallBack = useCallBack || false,
			callback = false,
			oldChecked = false;

			if (useCallBack == true){
				callback = function (){
					self._orderUpdated('shippingMethodsUpdated');
				};
			}

			if ($(':radio[name="shipping"]:checked', $el).size() > 0){
				oldChecked = $(':radio[name="shipping"]:checked', $el).val();
			}

			showAjaxLoader($el, 'large');
			$.ajax({
				url: self._getURL('order'),
				dataType: 'html',
				type: 'get',
				data: 'action=getMethods&method=shipping',
				cache: false,
				success: function (data){
					removeAjaxLoader($el);
					$el.html(data);
					$(':radio', $el).each(function (){
						$(this).click(function (){
							self._setShippingMethod($(this));
						});
						if (oldChecked != false && $(this).val() == oldChecked){
							$(this).click();
						}else if (this.checked){
							$(this).click();
						}
					});
				},
				complete: callback
			});
		},
		_setShippingMethod: function ($el){
			var self = this;

			$.ajax({
				url: self._getURL('order'),
				data: 'action=setShippingMethod&method=' + $el.val(),
				type: 'get',
				cache: false,
				dataType: 'json',
				success: function (data){
					self._orderUpdated('shippingMethodSelected');
				}
			});
		}
	});
})(jQuery);