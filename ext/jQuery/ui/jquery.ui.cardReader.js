(function ($) {

	$.widget("ui.cardReader", {
		regExp: [
			/%B([0-9]+)\^([A-Z\/ ]+)\^([0-9]{4})([0-9]{3})([0-9]{1})([0-9]{4})([0-9]+)\?;([0-9]+)=([0-9]{4})([0-9]{3})([0-9]{1})([0-9]{4})([0-9]+)\?/
		],
		enteredVal: '',
		monitorKeyStrokes: false,
		stopShowingValues: false,
		keyCodes: {
			percent: 37,
			B: 66,
			enter: 13
		},
		options: {},
		_create: function (){
			var self = this;
			$(this.element).bind('parseScan', function (e){
				var m = self.regExp[0].exec(self.enteredVal);
				if (m && m.length > 0){
					var AccountNumber          = m[1]; //Account Number
					var CardholderName         = m[2]; //Cardholder Name
					var Expiration             = m[3]; //Expiration (YYMM)
					var ServiceCode            = m[4]; //Service Code
					var PinVerificationKey     = m[5]; //Pin Verification Key Indicator
					var PinVerificationValue   = m[6]; //PIN Verification Value
					var CardVerificationValue  = m[7]; //Card Verification Value ( Doesn't match cvv on back of card??? )

					var AccountNumber2         = m[8]; //Account Number
					var Expiration2            = m[9]; //Expiration (YYMM)
					var ServiceCode2           = m[10]; //Service Code
					var PinVerificationKey2    = m[11]; //Pin Verification Key Indicator
					var PinVerificationValue2  = m[12]; //PIN Verification Value
					var CardVerificationValue2 = m[13]; //Card Verification Value ( Doesn't match cvv on back of card??? )

					var error = false;
					if (ServiceCode[0] == 9){
						alert('This is not a valid card');
						error = true;
					}

					if (ServiceCode[1] > 0){
						alert('Must Contact Issuer Via Online Means');
						error = true;
					}

					if (ServiceCode[2] == 0 || ServiceCode[2] == 5){
						alert('Pin Number Required For This Card');
						error = true;
					}

					if (ServiceCode[2] == 3 || ServiceCode[2] == 4){
						alert('This Card Is For ATM Use Only');
						error = true;
					}

					if (error === false){
						$(this).val(AccountNumber);
					}
				}else{
					alert('Card Read Error');
				}

				self.monitorKeyStrokes = false;
				self.stopShowingValues = false;
				self.enteredVal = '';
			});
		},
		_init: function (){
			var self = this;

			$(document.body).keypress(function (e){
				self.enteredVal += String.fromCharCode(e.which);
				if (self.stopShowingValues === true){
					if (e.which != self.keyCodes.enter){
						return false;
					}else{
						$(self.element).trigger('parseScan');
					}
				}
				if (self.monitorKeyStrokes === true && e.which == self.keyCodes.B){
					$(document.activeElement).val('');
					self.stopShowingValues = true;
					return false;
				}
				if (e.which == self.keyCodes.percent){
					self.monitorKeyStrokes = true;
				}
			});
		}
	});
})(jQuery);