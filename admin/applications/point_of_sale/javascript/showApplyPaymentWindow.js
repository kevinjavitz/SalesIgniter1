(function($) {
	$.extend($.ui.pointOfSale.prototype, {
		showApplyPaymentWindow: function (el){
			var self = this;
			if (!self.paymentWindow){
				self.paymentWindow = $('#applyPaymentWindow');
				self.paymentWindow.dialog({
					width: 775,
					height: 325,
					draggable: true,
					resizable: true,
					modal: true,
					position: 'center',
					autoOpen: false,
					buttons: {
						'Apply Payment': function (){
							showAjaxLoader(self.paymentWindow.parent(), 'large');

							var paymentFieldsSelector = '#' + $('#paymentMethod', self.paymentWindow).val() + 'Fields *';

							var urlVars = [];
							urlVars.push('payment=' + $('#paymentMethod', self.paymentWindow).val());
							urlVars.push('paymentAmount=' + $('#paymentAmount', self.paymentWindow).val());

							urlVars.push('cardNumber=' + $('#cardNumber', $(paymentFieldsSelector)).val());
							urlVars.push('cardExpMonth=' + $('#cardExpMonth', $(paymentFieldsSelector)).val());
							urlVars.push('cardExpYear=' + $('#cardExpYear', $(paymentFieldsSelector)).val());
							urlVars.push('cardOwner=' + $('#cardOwner', $(paymentFieldsSelector)).val());
							urlVars.push('cardCVV=' + $('#cardCVV', $(paymentFieldsSelector)).val());
							urlVars.push('cardType=' + $('#cardType', $(paymentFieldsSelector)).val());
							urlVars.push('comment=' + $('#comments', self.paymentWindow).val());
							urlVars.push('status=' + $('#status', self.paymentWindow).val());
							urlVars.push('notify=' + $('input[name="notify"]:checked', self.paymentWindow).val());

							$.ajax({
								url: self._getURL('order'),
								dataType: 'json',
								data: 'action=processPayment&' + urlVars.join('&'),
								type: 'post',
								cache: false,
								success: function (data){
									$('.errMsg, .successMsg').hide();
									if (typeof data.errMsg != 'undefined'){
										$('.errMsg', self.paymentWindow).html(data.errMsg);
										$('.errMsg', self.paymentWindow).show().parent().removeClass('ui-helper-hidden');
										hideAjaxLoader(self.paymentWindow.parent());
									}else if (typeof data.successMsg != 'undefined'){
										$('.successMsg', self.paymentWindow).html(data.successMsg);
										$('.successMsg', self.paymentWindow).show().parent().removeClass('ui-helper-hidden');

										confirm('Payment Was Successful');
										removeAjaxLoader(self.paymentWindow.parent());
										document.location = data.redirectUrl;
									}else{
										removeAjaxLoader(self.paymentWindow.parent());
										self.paymentWindow.dialog('close');
									}

									//$('#paymentLogDialog table').append('<tr><td class="main">' + data.logDateAdded + '</td><td class="main">' + data.logPaymentMethod + '</td><td class="main">' + data.logGatewayMessage + '</td><td class="main">' + data.logPaymentAmount + '</td></tr>');
								}
							});
						},
						'Cancel': function (){
							self.paymentWindow.dialog('close');
						}
					}
				});
			}

			if (self.paymentWindow.dialog('isOpen')) return false;

			$.ajax({
				url: self._getURL('order'),
				dataType: 'html',
				data: 'action=applyPayment',
				type: 'get',
				cache: false,
				success: function (data){
					$(self.paymentWindow).html(data);
					$('.paymentFields', self.paymentWindow).addClass('ui-helper-hidden');
					$('#paymentMethod', self.paymentWindow).change(function (){
						$('.paymentFields').addClass('ui-helper-hidden');
						if ($('#' + $(this).val() + 'Fields', self.paymentWindow).size() > 0){
							$('#' + $(this).val() + 'Fields', self.paymentWindow).removeClass('ui-helper-hidden');
						}
					});
					self.paymentWindow.dialog('open');
				}
			});
		}
	});
})(jQuery);